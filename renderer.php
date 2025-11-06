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
 * Renderer for tool_redirectplus
 *
 * This renderer uses the Moodle output API pattern with renderable/templatable classes.
 * Each render method instantiates an output class that implements renderable and templatable,
 * which exports data for the template through export_for_template(). This provides:
 * - Proper data caching through Moodle's output system
 * - Clean separation of data preparation and presentation
 * - Automatic handling of mustache template context
 * - Better testability and maintainability
 *
 * @package    tool_redirectplus
 * @copyright  2025 G Wiz IT Solutions {@link https://gwizit.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Renderer class for tool_redirectplus
 *
 * @package    tool_redirectplus
 * @copyright  2025 G Wiz IT Solutions
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_redirectplus_renderer extends plugin_renderer_base {

    /**
     * Render the settings tab using output API
     *
     * @param array $data Template data
     * @return string HTML output
     */
    public function render_settings_tab($data) {
        $settings_tab = new \tool_redirectplus\output\settings_tab($data);
        return $this->render_from_template('tool_redirectplus/settings_tab', $settings_tab->export_for_template($this));
    }

    /**
     * Render the report tab using output API
     *
     * @param int $page Current page number
     * @param int $perpage Records per page
     * @param \moodle_url $baseurl Base URL for links
     * @return string HTML output
     */
    public function render_report_tab($page, $perpage, $baseurl) {
        $report_tab = new \tool_redirectplus\output\report_tab($page, $perpage, $baseurl);
        return $this->render_from_template('tool_redirectplus/report_tab', $report_tab->export_for_template($this));
    }

    /**
     * Render the redirects tab using output API
     *
     * @param bool $has_redirects Whether there are redirects
     * @param bool $show_edit_form Whether to show edit form
     * @param string $edit_form_html Edit form HTML
     * @param string $table_html Table HTML
     * @param string $add_url Add URL
     * @param string $sesskey Session key
     * @return string HTML output
     */
    public function render_redirects_tab($has_redirects, $show_edit_form, $edit_form_html, $table_html, $add_url, $sesskey) {
        $redirects_tab = new \tool_redirectplus\output\redirects_tab(
            $has_redirects, $show_edit_form, $edit_form_html, $table_html, $add_url, $sesskey
        );
        return $this->render_from_template('tool_redirectplus/redirects_tab', $redirects_tab->export_for_template($this));
    }

    /**
     * Render the main page with all tabs using output API
     *
     * @param string $report_tab Report tab HTML
     * @param string $redirects_tab Redirects tab HTML
     * @param string $settings_tab Settings tab HTML
     * @return string HTML output
     */
    public function render_main_page($report_tab, $redirects_tab, $settings_tab) {
        $main_page = new \tool_redirectplus\output\main_page($report_tab, $redirects_tab, $settings_tab);
        return $this->render_from_template('tool_redirectplus/main', $main_page->export_for_template($this));
    }

    /**
     * Render the edit redirect form using output API
     *
     * @param object|int|null $redirect Redirect record, ID, or null for new
     * @param \moodle_url $baseurl Base URL for form actions
     * @return string HTML output
     */
    public function render_edit_redirect_form($redirect, $baseurl) {
        $edit_form = new \tool_redirectplus\output\edit_redirect_form($redirect, $baseurl);
        return $this->render_from_template('tool_redirectplus/edit_redirect_form', $edit_form->export_for_template($this));
    }
}
