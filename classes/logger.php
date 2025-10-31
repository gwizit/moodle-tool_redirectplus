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
 * Logger class for tool_redirectplus.
 *
 * @package     tool_redirectplus
 * @copyright   2025 G Wiz IT Solutions <support@gwizit.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_redirectplus;

defined('MOODLE_INTERNAL') || die();

/**
 * Logger class for recording 404 errors.
 *
 * @package     tool_redirectplus
 * @copyright   2025 G Wiz IT Solutions <support@gwizit.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class logger {

    /**
     * Log a 404 error to the database.
     *
     * @param string $tool_redirectplus_url The URL that generated the 404 error
     * @param string $tool_redirectplus_referrer The referrer URL (optional)
     * @param int $tool_redirectplus_userid The user ID (default: 0 for guest)
     * @param string $tool_redirectplus_ip The IP address (optional)
     * @param string $tool_redirectplus_useragent The user agent string (optional)
     * @return bool True on success, false on failure
     */
    public static function tool_redirectplus_log_404(
        $tool_redirectplus_url,
        $tool_redirectplus_referrer = '',
        $tool_redirectplus_userid = 0,
        $tool_redirectplus_ip = '',
        $tool_redirectplus_useragent = ''
    ) {
        global $DB;

        try {
            $tool_redirectplus_record = new \stdClass();
            $tool_redirectplus_record->url = $tool_redirectplus_url;
            $tool_redirectplus_record->referrer = $tool_redirectplus_referrer;
            $tool_redirectplus_record->userid = $tool_redirectplus_userid;
            $tool_redirectplus_record->timecreated = time();
            $tool_redirectplus_record->ip = $tool_redirectplus_ip;
            $tool_redirectplus_record->useragent = $tool_redirectplus_useragent;

            $DB->insert_record('tool_redirectplus_404', $tool_redirectplus_record);
            return true;
        } catch (\Exception $tool_redirectplus_exception) {
            debugging('tool_redirectplus: Failed to log 404 error - ' . $tool_redirectplus_exception->getMessage(), DEBUG_DEVELOPER);
            return false;
        }
    }

    /**
     * Get all 404 error records with pagination.
     *
     * @param int $tool_redirectplus_limitfrom Record offset
     * @param int $tool_redirectplus_limitnum Number of records to fetch
     * @return array Array of 404 error records
     */
    public static function tool_redirectplus_get_404_records($tool_redirectplus_limitfrom = 0, $tool_redirectplus_limitnum = 0) {
        global $DB;

        return $DB->get_records('tool_redirectplus_404', null, 'timecreated DESC', '*',
            $tool_redirectplus_limitfrom, $tool_redirectplus_limitnum);
    }

    /**
     * Count total 404 error records.
     *
     * @return int Total count of records
     */
    public static function tool_redirectplus_count_404_records() {
        global $DB;

        return $DB->count_records('tool_redirectplus_404');
    }

    /**
     * Delete a single 404 error record.
     *
     * @param int $tool_redirectplus_id Record ID to delete
     * @return bool True on success, false on failure
     */
    public static function tool_redirectplus_delete_404_record($tool_redirectplus_id) {
        global $DB;

        return $DB->delete_records('tool_redirectplus_404', ['id' => $tool_redirectplus_id]);
    }

    /**
     * Delete all 404 error records.
     *
     * @return bool True on success, false on failure
     */
    public static function tool_redirectplus_delete_all_404_records() {
        global $DB;

        return $DB->delete_records('tool_redirectplus_404');
    }
}
