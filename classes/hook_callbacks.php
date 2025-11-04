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
 * Hook callbacks for Redirect Plus plugin.
 *
 * @package     tool_redirectplus
 * @copyright   2025 G Wiz IT Solutions <support@gwizit.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_redirectplus;

/**
 * Hook callbacks class for redirect processing.
 *
 * @package     tool_redirectplus
 * @copyright   2025 G Wiz IT Solutions
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_callbacks {
    
    /**
     * Callback executed after config is loaded but before any output.
     * This is called via Moodle's hook system to check for redirects.
     *
     * @param \core\hook\after_config $hook The hook instance
     */
    public static function after_config(\core\hook\after_config $hook): void {
        global $CFG, $DB;
        
        // Don't redirect if we're in CLI mode.
        if (defined('CLI_SCRIPT') && CLI_SCRIPT) {
            return;
        }
        
        // Don't redirect if we're in AJAX mode.
        if (defined('AJAX_SCRIPT') && AJAX_SCRIPT) {
            return;
        }
        
        // Don't redirect during installation or upgrade.
        if (!empty($CFG->upgraderunning) || during_initial_install()) {
            return;
        }
        
        // Don't redirect if database is not yet installed.
        if (empty($CFG->version)) {
            return;
        }
        
        // Don't redirect on the plugin's own pages.
        $script = $_SERVER['SCRIPT_NAME'] ?? '';
        if (strpos($script, '/admin/tool/redirectplus/') !== false) {
            return;
        }
        
        // Don't redirect on critical Moodle pages.
        if (strpos($script, '/admin/index.php') !== false ||
            strpos($script, '/login/') !== false ||
            strpos($script, '/admin/cli/') !== false) {
            return;
        }
        
        try {
            // Check if admin bypass is enabled and user is admin.
            if (\tool_redirectplus_should_bypass_redirect()) {
                return;
            }
            
            // Get the requested URL path.
            $request_uri = $_SERVER['REQUEST_URI'] ?? '';
            $url_parts = parse_url($request_uri);
            $request_path = $url_parts['path'] ?? '';
            
            // Convert to relative path.
            if (!empty($CFG->wwwroot)) {
                $wwwroot_path = parse_url($CFG->wwwroot, PHP_URL_PATH) ?? '';
                if ($wwwroot_path && strpos($request_path, $wwwroot_path) === 0) {
                    $request_path = substr($request_path, strlen($wwwroot_path));
                }
            }
            
            // Look for a matching redirect.
            $redirect = \tool_redirectplus_find_redirect($request_path);
            
            if (!$redirect) {
                return; // No redirect found.
            }
            
            // Get user context for conditional parameters.
            $is_logged_in = isloggedin() && !isguestuser();
            $user_lang = \tool_redirectplus_get_user_language();
            
            // Evaluate the redirect options to get destination URL.
            $destination_url = \tool_redirectplus_evaluate_redirect($redirect, $is_logged_in, $user_lang);
            
            if ($destination_url) {
                // Perform the redirect using Moodle's redirect function.
                redirect($destination_url);
            }
        } catch (\Exception $e) {
            // Silently fail - don't break the page.
            debugging('tool_redirectplus: Error in after_config hook - ' . $e->getMessage(), DEBUG_DEVELOPER);
        }
    }
}
