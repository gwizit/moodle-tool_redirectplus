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
 * Privacy Subsystem implementation for tool_redirectplus.
 *
 * @package     tool_redirectplus
 * @copyright   2025 G Wiz IT Solutions <support@gwizit.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_redirectplus\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy Subsystem for tool_redirectplus implementing metadata and request provider.
 *
 * @copyright   2025 G Wiz IT Solutions <support@gwizit.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\request\plugin\provider {

    /**
     * Returns meta data about this system.
     *
     * @param collection $tool_redirectplus_collection The initialised collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $tool_redirectplus_collection): collection {
        $tool_redirectplus_collection->add_database_table('tool_redirectplus_404', [
            'userid' => 'privacy:metadata:tool_redirectplus_404:userid',
            'url' => 'privacy:metadata:tool_redirectplus_404:url',
            'referrer' => 'privacy:metadata:tool_redirectplus_404:referrer',
            'ip' => 'privacy:metadata:tool_redirectplus_404:ip',
            'useragent' => 'privacy:metadata:tool_redirectplus_404:useragent',
            'timecreated' => 'privacy:metadata:tool_redirectplus_404:timecreated',
        ], 'privacy:metadata:tool_redirectplus_404');

        return $tool_redirectplus_collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $tool_redirectplus_userid The user to search.
     * @return contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $tool_redirectplus_userid): contextlist {
        $tool_redirectplus_contextlist = new contextlist();

        $tool_redirectplus_sql = "SELECT ctx.id
                      FROM {context} ctx
                      JOIN {tool_redirectplus_404} tr ON ctx.contextlevel = :contextlevel
                     WHERE tr.userid = :userid";

        $tool_redirectplus_params = [
            'contextlevel' => CONTEXT_SYSTEM,
            'userid' => $tool_redirectplus_userid,
        ];

        $tool_redirectplus_contextlist->add_from_sql($tool_redirectplus_sql, $tool_redirectplus_params);

        return $tool_redirectplus_contextlist;
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $tool_redirectplus_contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $tool_redirectplus_contextlist) {
        global $DB;

        if (empty($tool_redirectplus_contextlist->count())) {
            return;
        }

        $tool_redirectplus_userid = $tool_redirectplus_contextlist->get_user()->id;

        $tool_redirectplus_records = $DB->get_records('tool_redirectplus_404', ['userid' => $tool_redirectplus_userid]);

        if (!empty($tool_redirectplus_records)) {
            $tool_redirectplus_data = [];
            foreach ($tool_redirectplus_records as $tool_redirectplus_record) {
                $tool_redirectplus_data[] = (object)[
                    'url' => $tool_redirectplus_record->url,
                    'referrer' => $tool_redirectplus_record->referrer,
                    'ip' => $tool_redirectplus_record->ip,
                    'useragent' => $tool_redirectplus_record->useragent,
                    'timecreated' => \core_privacy\local\request\transform::datetime($tool_redirectplus_record->timecreated),
                ];
            }

            $tool_redirectplus_context = \context_system::instance();
            writer::with_context($tool_redirectplus_context)->export_data(
                [get_string('pluginname', 'tool_redirectplus')],
                (object)['404errors' => $tool_redirectplus_data]
            );
        }
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param \context $tool_redirectplus_context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $tool_redirectplus_context) {
        global $DB;

        if ($tool_redirectplus_context->contextlevel == CONTEXT_SYSTEM) {
            $DB->delete_records('tool_redirectplus_404');
        }
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $tool_redirectplus_contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $tool_redirectplus_contextlist) {
        global $DB;

        if (empty($tool_redirectplus_contextlist->count())) {
            return;
        }

        $tool_redirectplus_userid = $tool_redirectplus_contextlist->get_user()->id;
        $DB->delete_records('tool_redirectplus_404', ['userid' => $tool_redirectplus_userid]);
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param userlist $tool_redirectplus_userlist The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $tool_redirectplus_userlist) {
        $tool_redirectplus_context = $tool_redirectplus_userlist->get_context();

        if ($tool_redirectplus_context->contextlevel != CONTEXT_SYSTEM) {
            return;
        }

        $tool_redirectplus_sql = "SELECT userid
                      FROM {tool_redirectplus_404}";

        $tool_redirectplus_userlist->add_from_sql('userid', $tool_redirectplus_sql, []);
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param approved_userlist $tool_redirectplus_userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $tool_redirectplus_userlist) {
        global $DB;

        $tool_redirectplus_context = $tool_redirectplus_userlist->get_context();

        if ($tool_redirectplus_context->contextlevel != CONTEXT_SYSTEM) {
            return;
        }

        $tool_redirectplus_userids = $tool_redirectplus_userlist->get_userids();

        if (!empty($tool_redirectplus_userids)) {
            list($tool_redirectplus_insql, $tool_redirectplus_inparams) = $DB->get_in_or_equal($tool_redirectplus_userids, SQL_PARAMS_NAMED);
            $DB->delete_records_select('tool_redirectplus_404', "userid {$tool_redirectplus_insql}", $tool_redirectplus_inparams);
        }
    }
}
