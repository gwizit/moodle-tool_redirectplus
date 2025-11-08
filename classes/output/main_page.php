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
 * Main page renderable for Redirect Plus plugin.
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
 * Main page renderable class.
 *
 * @package    tool_redirectplus
 * @copyright  2025 G Wiz IT Solutions
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class main_page implements renderable, templatable {

    /** @var string Report tab HTML */
    protected $report_tab;

    /** @var string Redirects tab HTML */
    protected $redirects_tab;

    /** @var string Settings tab HTML */
    protected $settings_tab;

    /**
     * Constructor.
     *
     * @param string $report_tab Report tab HTML
     * @param string $redirects_tab Redirects tab HTML
     * @param string $settings_tab Settings tab HTML
     */
    public function __construct($report_tab, $redirects_tab, $settings_tab) {
        $this->report_tab = $report_tab;
        $this->redirects_tab = $redirects_tab;
        $this->settings_tab = $settings_tab;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output Renderer
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $data->report_tab = $this->report_tab;
        $data->redirects_tab = $this->redirects_tab;
        $data->settings_tab = $this->settings_tab;
        return $data;
    }
}
