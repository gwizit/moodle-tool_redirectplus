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
 * Auto-prepend file for 404 error detection
 *
 * This file is prepended to all PHP requests when using the .htaccess method.
 * It registers a shutdown function to check for 404 errors.
 *
 * NOTE: Custom redirects for existing pages are now handled by Moodle's callback
 * system (tool_redirectplus_after_config in lib.php), which works with any PHP setup
 * including PHP-FPM and FastCGI.
 *
 * This file is now ONLY needed for 404 error detection if you cannot configure
 * your web server (Apache ErrorDocument or Nginx error_page).
 *
 * Setup: Add this line to .htaccess in Moodle root:
 *   php_value auto_prepend_file "/path/to/moodle/admin/tool/redirectplus/prepend.php"
 *
 * Note: This only works with mod_php. Does NOT work with PHP-FPM or FastCGI.
 *       For 404 detection, use Apache ErrorDocument or Nginx error_page instead (recommended).
 *
 * @package     tool_redirectplus
 * @copyright   2025 G Wiz IT Solutions <support@gwizit.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Only proceed if this is not CLI.
if (php_sapi_name() !== 'cli') {
    // Register shutdown function to check for 404 errors.
    register_shutdown_function('tool_redirectplus_check_404');
}

/**
 * Shutdown function to check if a 404 error occurred
 */
function tool_redirectplus_check_404() {
    $status = http_response_code();
    
    // Only proceed if 404 error
    if ($status !== 404) {
        return;
    }
    
    // Don't redirect to error page if we're already on it
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    if (strpos($request_uri, 'tool/redirectplus/error404.php') !== false) {
        return;
    }
    
    // Redirect to our custom 404 handler
    $error_url = '/admin/tool/redirectplus/error404.php?url=' . urlencode($request_uri);
    
    // Clear any output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Redirect
    header('Location: ' . $error_url);
    exit;
}
