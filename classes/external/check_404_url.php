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
 * External service to check if a specific URL was logged as a 404 error
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
 * External service to check if a specific URL was logged as a 404 error
 */
class check_404_url extends external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'url' => new external_value(PARAM_RAW, 'URL to check for in 404 log')
        ]);
    }

    /**
     * Check if a specific URL was logged
     * @param string $url
     * @return array
     */
    public static function execute($url) {
        global $DB;

        // Validate context and permissions.
        $context = \context_system::instance();
        self::validate_context($context);
        require_capability('moodle/site:config', $context);

        // Validate parameters.
        $params = self::validate_parameters(self::execute_parameters(), [
            'url' => $url
        ]);

        // Check if the URL exists in the 404 log.
        // Look for URLs that contain the test path (handles cases where it might have query params or be slightly different).
        $records = $DB->get_records_select(
            'tool_redirectplus_404',
            $DB->sql_like('url', ':url', false),
            ['url' => '%' . $DB->sql_like_escape($params['url']) . '%'],
            'timecreated DESC',
            '*',
            0,
            1
        );

        if (!empty($records)) {
            $record = reset($records);
            return [
                'found' => true,
                'url' => $record->url,
                'useragent' => $record->useragent,
                'ip' => $record->ip,
                'time' => userdate($record->timecreated),
                'message' => 'Found matching 404 error log entry'
            ];
        }

        return [
            'found' => false,
            'url' => '',
            'useragent' => '',
            'ip' => '',
            'time' => '',
            'message' => 'No matching 404 error log entry found'
        ];
    }

    /**
     * Returns description of method result value
     * @return external_single_structure
     */
    public static function execute_returns() {
        return new external_single_structure([
            'found' => new external_value(PARAM_BOOL, 'Whether the URL was found in the log'),
            // PARAM_RAW is safe here as data comes from database and will be sanitized on output by the consuming code.
            'url' => new external_value(PARAM_RAW, 'The logged URL'),
            'useragent' => new external_value(PARAM_RAW, 'User agent string'),
            'ip' => new external_value(PARAM_RAW, 'IP address'),
            'time' => new external_value(PARAM_RAW, 'Formatted time'),
            'message' => new external_value(PARAM_TEXT, 'Status message')
        ]);
    }
}
