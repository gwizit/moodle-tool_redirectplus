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
     * Render the settings tab
     *
     * @param array $data Template data
     * @return string HTML output
     */
    public function render_settings_tab($data) {
        return $this->render_from_template('tool_redirectplus/settings_tab', $data);
    }

    /**
     * Render the setup tab
     *
     * @param array $data Template data
     * @return string HTML output
     */
    public function render_setup_tab($data) {
        return $this->render_from_template('tool_redirectplus/setup_tab', $data);
    }

    /**
     * Render the report tab
     *
     * @param array $data Template data
     * @return string HTML output
     */
    public function render_report_tab($data) {
        return $this->render_from_template('tool_redirectplus/report_tab', $data);
    }

    /**
     * Render the main page with all tabs
     *
     * @param array $data Template data
     * @return string HTML output
     */
    public function render_main_page($data) {
        return $this->render_from_template('tool_redirectplus/main', $data);
    }
}
