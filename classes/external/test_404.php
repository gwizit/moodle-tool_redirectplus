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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * External service to test 404 tracking
 *
 * @package     tool_redirectplus
 * @copyright   2025 G Wiz IT Solutions <support@gwizit.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_redirectplus\external;

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

/**
 * External service to test 404 tracking
 */
class test_404 extends external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'testurl' => new external_value(PARAM_URL, 'URL to test')
        ]);
    }

    /**
     * Test 404 tracking
     * @param string $testurl
     * @return array
     */
    public static function execute($testurl) {
        global $CFG, $DB;

        // Validate context and permissions.
        $context = \context_system::instance();
        self::validate_context($context);
        require_capability('moodle/site:config', $context);

        // Validate parameters.
        $params = self::validate_parameters(self::execute_parameters(), [
            'testurl' => $testurl
        ]);

        // Extract the path from the test URL.
        $parsed_url = parse_url($params['testurl']);
        $test_path = $parsed_url['path'] ?? '/test-404-unknown';

        // Count existing records before test.
        $before_count = $DB->count_records('tool_redirectplus_404');

        // Make a request to the test URL.
        $ch = curl_init();
        if ($ch === false) {
            return [
                'success' => false,
                'message' => 'Failed to initialize cURL. Please ensure cURL is enabled on your server.'
            ];
        }

        curl_setopt($ch, CURLOPT_URL, $params['testurl']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            return [
                'success' => false,
                'message' => 'cURL request failed: ' . $curl_error
            ];
        }

        // Wait a moment for the record to be inserted.
        sleep(1);

        // Count records after test.
        $after_count = $DB->count_records('tool_redirectplus_404');

        // Check if a new record was created.
        if ($after_count > $before_count) {
            // Find the most recent record with our test path using Moodle's proper API.
            $records = $DB->get_records_sql(
                "SELECT * FROM {tool_redirectplus_404} WHERE url LIKE ? ORDER BY timecreated DESC",
                ['%' . $test_path . '%'],
                0,
                1
            );

            if (!empty($records)) {
                $record = reset($records);
                return [
                    'success' => true,
                    'message' => 'Test successful! 404 error was logged correctly. URL: ' . $record->url
                ];
            }
        }

        // Test failed - no new record.
        return [
            'success' => false,
            'message' => 'Test failed. No 404 error was logged. Please check your server configuration. HTTP Code: ' . $http_code
        ];
    }

    /**
     * Returns description of method result value
     * @return external_single_structure
     */
    public static function execute_returns() {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Operation success'),
            'message' => new external_value(PARAM_TEXT, 'Message')
        ]);
    }
}
