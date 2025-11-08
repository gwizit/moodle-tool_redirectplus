<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Report tab renderable for Redirect Plus plugin.
 *
 * @package    tool_redirectplus
 * @copyright  2025 G Wiz IT Solutions {@link https://gwizit.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_redirectplus\output;

use renderable;
use renderer_base;
use templatable;
use stdClass;
use html_table;
use html_writer;
use moodle_url;

/**
 * Report tab renderable class.
 *
 * Handles data processing and table generation for 404 error reports.
 *
 * @package    tool_redirectplus
 * @copyright  2025 G Wiz IT Solutions
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_tab implements renderable, templatable {

    /** @var int Current page */
    protected $page;

    /** @var int Records per page */
    protected $perpage;

    /** @var moodle_url Base URL */
    protected $baseurl;

    /**
     * Constructor.
     *
     * @param int $page Current page number
     * @param int $perpage Records per page
     * @param moodle_url $baseurl Base URL for links
     */
    public function __construct($page, $perpage, $baseurl) {
        $this->page = $page;
        $this->perpage = $perpage;
        $this->baseurl = $baseurl;
    }

    /**
     * Build the 404 errors table.
     *
     * @param array $records Database records
     * @return string HTML table
     */
    protected function build_table($records) {
        global $CFG, $DB;
        
        $table = new html_table();
        $table->head = [
            get_string('url', 'tool_redirectplus'),
            get_string('referrer', 'tool_redirectplus'),
            get_string('user'),
            get_string('ipaddress', 'tool_redirectplus'),
            get_string('useragent', 'tool_redirectplus'),
            get_string('timecreated', 'tool_redirectplus'),
            get_string('actions'),
        ];
        $table->attributes['class'] = 'admintable generaltable';
        $table->id = 'tool_redirectplus_report';

        foreach ($records as $record) {
            $row = [];

            // URL.
            $row[] = html_writer::link(
                $CFG->wwwroot . $record->url,
                s($record->url),
                ['target' => '_blank']
            );

            // Referrer.
            $referrer = $record->referrer ?: '-';
            if ($referrer !== '-') {
                $row[] = html_writer::link(
                    $referrer,
                    s($referrer),
                    ['target' => '_blank']
                );
            } else {
                $row[] = '-';
            }

            // User.
            if ($record->userid > 0) {
                $user = $DB->get_record('user', ['id' => $record->userid]);
                if ($user) {
                    $row[] = fullname($user);
                } else {
                    $row[] = get_string('deleteduser', 'tool_redirectplus');
                }
            } else {
                $row[] = get_string('guest');
            }

            // IP.
            $row[] = s($record->ip);

            // User agent (truncated).
            $useragent = $record->useragent ?: '-';
            if (strlen($useragent) > 60) {
                $useragent = substr($useragent, 0, 60) . '...';
            }
            $row[] = html_writer::tag('span', s($useragent), [
                'title' => s($record->useragent),
            ]);

            // Time.
            $row[] = userdate($record->timecreated);

            // Actions.
            // Check if redirect already exists for this URL.
            $redirect = $DB->get_record('tool_redirectplus_redirects', ['source_url' => $record->url]);
            
            // Single Add/Edit Redirect button.
            if ($redirect) {
                $redirecturl = new moodle_url($this->baseurl, [
                    'editid' => $redirect->id,
                ]);
            } else {
                $redirecturl = new moodle_url($this->baseurl, [
                    'action' => 'add',
                    'source_url' => $record->url,
                ]);
            }
            $redirectbutton = html_writer::link($redirecturl->out(false), get_string('addeditredirect', 'tool_redirectplus'), [
                'class' => 'btn btn-sm btn-block btn-primary mb-1 report-redirect-btn',
            ]);
            
            // Delete button.
            $deleteurl = new moodle_url($this->baseurl, [
                'delete' => $record->id,
                'sesskey' => sesskey(),
            ]);
            $deletebutton = html_writer::link($deleteurl->out(false), get_string('delete'), [
                'class' => 'btn btn-sm btn-block btn-danger report-delete-btn',
                'onclick' => 'return confirm(\'' . get_string('deleterecordconfirm', 'tool_redirectplus') . '\');',
            ]);
            
            $row[] = $redirectbutton . $deletebutton;

            $table->data[] = $row;
        }

        return html_writer::table($table);
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output Renderer
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        global $DB;
        
        // Initialize cache.
        $cache = \cache::make('tool_redirectplus', 'report404');
        
        // Create unique cache key based on page and perpage.
        $cachekey = "page_{$this->page}_perpage_{$this->perpage}";
        
        // Try to get cached data.
        $data = $cache->get($cachekey);
        
        if ($data === false) {
            // Cache miss - build the data.
            $data = new stdClass();
            
            // Get total count and records.
            $totalcount = $DB->count_records('tool_redirectplus_404');
            $data->has_errors = $totalcount > 0;
            
            if ($data->has_errors) {
                $records = $DB->get_records('tool_redirectplus_404', null, 'timecreated DESC',
                    '*', $this->page * $this->perpage, $this->perpage);
                
                $data->table_html = $this->build_table($records);
                $data->paging_html = $output->paging_bar($totalcount, $this->page, $this->perpage, $this->baseurl);
            } else {
                $data->table_html = '';
                $data->paging_html = '';
            }
            
            // Delete all URL.
            $deleteall_url = new moodle_url($this->baseurl, [
                'deleteall' => 1,
                'sesskey' => sesskey(),
            ]);
            $data->deleteall_url = $deleteall_url->out(false);
            
            // Store in cache.
            $cache->set($cachekey, $data);
        }
        
        return $data;
    }
}
