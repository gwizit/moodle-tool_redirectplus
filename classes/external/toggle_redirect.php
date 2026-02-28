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
 * External service to toggle a redirect's enabled/disabled status.
 *
 * @package     tool_redirectplus
 * @copyright   2025 G Wiz IT Solutions <support@gwizit.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_redirectplus\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;
use core_external\external_single_structure;

defined('MOODLE_INTERNAL') || die();

/**
 * External service to toggle a redirect's enabled/disabled status.
 */
class toggle_redirect extends external_api {

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'id' => new external_value(PARAM_INT, 'The redirect ID to toggle'),
        ]);
    }

    /**
     * Toggle a redirect's enabled status.
     *
     * @param int $id The redirect ID.
     * @return array
     */
    public static function execute($id) {
        global $DB;

        // Validate context and permissions.
        $context = \context_system::instance();
        self::validate_context($context);
        require_capability('moodle/site:config', $context);

        // Validate parameters.
        $params = self::validate_parameters(self::execute_parameters(), ['id' => $id]);

        // Get the redirect record.
        $redirect = $DB->get_record('tool_redirectplus_redirects', ['id' => $params['id']], '*', MUST_EXIST);

        // Toggle the enabled status.
        $newstatus = $redirect->enabled ? 0 : 1;
        $DB->set_field('tool_redirectplus_redirects', 'enabled', $newstatus, ['id' => $params['id']]);
        $DB->set_field('tool_redirectplus_redirects', 'timemodified', time(), ['id' => $params['id']]);

        // Purge the redirects list cache so changes take effect immediately.
        $cache = \cache::make('tool_redirectplus', 'redirectslist');
        $cache->purge();

        return [
            'success' => true,
            'enabled' => $newstatus,
            'statustext' => $newstatus
                ? get_string('enabled', 'tool_redirectplus')
                : get_string('disabled', 'tool_redirectplus'),
        ];
    }

    /**
     * Returns description of method result value.
     *
     * @return external_single_structure
     */
    public static function execute_returns() {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Whether the toggle was successful'),
            'enabled' => new external_value(PARAM_INT, 'New enabled status (0 or 1)'),
            'statustext' => new external_value(PARAM_TEXT, 'Localized status text'),
        ]);
    }
}
