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
 * Plugin upgrade steps are defined here.
 *
 * @package     tool_redirectplus
 * @category    upgrade
 * @copyright   2025 G Wiz IT Solutions <support@gwizit.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/upgradelib.php');

/**
 * Execute tool_redirectplus upgrade from the given old version.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_tool_redirectplus_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    // For further information please read {@link https://docs.moodle.org/dev/Upgrade_API}.
    //
    // You will also have to create the db/install.xml file by using the XMLDB Editor.
    // Documentation for the XMLDB Editor can be found at {@link https://docs.moodle.org/dev/XMLDB_editor}.

    // Add custom redirects table for version 2025110302.
    if ($oldversion < 2025110302) {

        // Define table tool_redirectplus_redirects to be created.
        $table = new xmldb_table('tool_redirectplus_redirects');

        // Adding fields to table tool_redirectplus_redirects.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('source_url', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('redirect_options', XMLDB_TYPE_TEXT, 'medium', null, XMLDB_NOTNULL, null, null);
        $table->add_field('enabled', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table tool_redirectplus_redirects.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table tool_redirectplus_redirects.
        $table->add_index('source_url_idx', XMLDB_INDEX_NOTUNIQUE, ['source_url']);
        $table->add_index('enabled_idx', XMLDB_INDEX_NOTUNIQUE, ['enabled']);

        // Conditionally launch create table for tool_redirectplus_redirects.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Add default config setting for disabling redirects for administrators (enabled by default).
        set_config('disable_redirect_admin', 1, 'tool_redirectplus');

        // Redirectplus savepoint reached.
        upgrade_plugin_savepoint(true, 2025110302, 'tool', 'redirectplus');
    }

    // Version 2025110303: Updated to use proper Moodle callback system (tool_redirectplus_after_config).
    // No database changes needed - this is just a marker for the improved implementation.
    if ($oldversion < 2025110303) {
        // No database changes required.
        // The plugin now uses Moodle's after_config callback for redirect interception.
        // This works with all PHP configurations (PHP-FPM, FastCGI, mod_php).
        
        // Redirectplus savepoint reached.
        upgrade_plugin_savepoint(true, 2025110303, 'tool', 'redirectplus');
    }

    return true;
}
