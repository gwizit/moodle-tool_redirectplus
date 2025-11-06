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
 * Edit redirect form renderable for Redirect Plus plugin.
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
use moodle_url;

/**
 * Edit redirect form renderable class.
 *
 * Handles data processing and string loading for the edit/add redirect form.
 *
 * @package    tool_redirectplus
 * @copyright  2025 G Wiz IT Solutions
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class edit_redirect_form implements renderable, templatable {

    /** @var object|null Redirect record from database */
    protected $redirect;

    /** @var moodle_url Base URL */
    protected $baseurl;

    /**
     * Constructor.
     *
     * @param object|null $redirect Redirect record (null for new redirect)
     * @param moodle_url $baseurl Base URL for form actions
     */
    public function __construct($redirect, $baseurl) {
        global $DB;
        
        if ($redirect && is_numeric($redirect) && $redirect > 0) {
            // Load from database if ID provided.
            $this->redirect = $DB->get_record('tool_redirectplus_redirects', ['id' => $redirect], '*', MUST_EXIST);
            $this->redirect->options = json_decode($this->redirect->redirect_options, true);
        } else if ($redirect && is_object($redirect)) {
            // Use provided object.
            $this->redirect = $redirect;
        } else {
            // Create new redirect object.
            $this->redirect = new stdClass();
            $this->redirect->id = 0;
            $this->redirect->source_url = '';
            $this->redirect->enabled = 1;
            $this->redirect->options = [
                'type' => 'simple',
                'destination_url' => '',
                'use_login_param' => 0,
                'use_language_param' => 0,
                'language_rules' => [],
            ];
        }
        
        $this->baseurl = $baseurl;
    }

    /**
     * Process language rules for template display.
     *
     * @return array Formatted language rules
     */
    protected function process_language_rules() {
        $language_rules = $this->redirect->options['language_rules'] ?? [];
        
        if (empty($language_rules)) {
            $language_rules = [['lang' => '', 'url' => '']];
        }
        
        $formatted = [];
        foreach ($language_rules as $index => $rule) {
            $formatted[] = [
                'lang' => $rule['lang'] ?? '',
                'url' => $rule['url'] ?? '',
                'number' => $index + 1,
                'is_first' => $index === 0,
            ];
        }
        
        return $formatted;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output Renderer
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        
        // Form data.
        $data->redirect_id = $this->redirect->id;
        $data->source_url = $this->redirect->source_url;
        $data->enabled = $this->redirect->enabled;
        $data->destination_url = $this->redirect->options['destination_url'] ?? '';
        $data->is_simple = ($this->redirect->options['type'] ?? 'simple') === 'simple';
        $data->is_conditional = ($this->redirect->options['type'] ?? 'simple') === 'conditional';
        $data->use_login_param = !empty($this->redirect->options['use_login_param']);
        $data->loggedin_url = $this->redirect->options['loggedin_url'] ?? '';
        $data->loggedout_url = $this->redirect->options['loggedout_url'] ?? '';
        $data->use_language_param = !empty($this->redirect->options['use_language_param']);
        $data->language_rules = $this->process_language_rules();
        $data->default_language_url = $this->redirect->options['default_language_url'] ?? '';
        $data->form_action = $this->baseurl->out(false);
        $data->cancel_url = $this->baseurl->out(false);
        $data->sesskey = sesskey();
        
        // Load strings for JavaScript - these are used in dynamic content.
        $data->js_strings = [
            'languagerule' => get_string('languagerule', 'tool_redirectplus'),
            'moveup' => get_string('moveup', 'tool_redirectplus'),
            'movedown' => get_string('movedown', 'tool_redirectplus'),
            'languagecode' => get_string('languagecode', 'tool_redirectplus'),
            'languagecode_help' => get_string('languagecode_help', 'tool_redirectplus'),
            'languageurl' => get_string('languageurl', 'tool_redirectplus'),
            'delete' => get_string('delete', 'tool_redirectplus'),
        ];
        
        return $data;
    }
}
