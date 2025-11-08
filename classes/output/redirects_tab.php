<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Redirects tab renderable for Redirect Plus plugin.
 *
 * @package    tool_redirectplus
 * @copyright  2025 G Wiz IT Solutions {@link https://gwizit.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_redirectplus\output;

use renderable;
use renderer_base;
use templatable;
use stdClass;

/**
 * Redirects tab renderable class.
 *
 * @package    tool_redirectplus
 * @copyright  2025 G Wiz IT Solutions
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class redirects_tab implements renderable, templatable {

    /** @var bool Whether there are redirects */
    protected $has_redirects;

    /** @var bool Whether to show edit form */
    protected $show_edit_form;

    /** @var string Edit form HTML */
    protected $edit_form_html;

    /** @var string Table HTML */
    protected $table_html;

    /** @var string Add URL */
    protected $add_url;

    /** @var string Session key */
    protected $sesskey;

    /**
     * Constructor.
     *
     * @param bool $has_redirects Whether there are redirects
     * @param bool $show_edit_form Whether to show edit form
     * @param string $edit_form_html Edit form HTML
     * @param string $table_html Table HTML
     * @param string $add_url Add URL
     * @param string $sesskey Session key
     */
    public function __construct($has_redirects, $show_edit_form, $edit_form_html, $table_html, $add_url, $sesskey) {
        $this->has_redirects = $has_redirects;
        $this->show_edit_form = $show_edit_form;
        $this->edit_form_html = $edit_form_html;
        $this->table_html = $table_html;
        $this->add_url = $add_url;
        $this->sesskey = $sesskey;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output Renderer
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $data->has_redirects = $this->has_redirects;
        $data->show_edit_form = $this->show_edit_form;
        $data->edit_form_html = $this->edit_form_html;
        $data->table_html = $this->table_html;
        $data->add_url = $this->add_url;
        $data->sesskey = $this->sesskey;
        return $data;
    }
}
