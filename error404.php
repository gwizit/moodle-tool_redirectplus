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
 * Custom 404 Error Handler Page
 *
 * This page should be set as your server's custom 404 error page.
 * It logs the 404 error and then displays custom content or redirects.
 *
 * @package     tool_redirectplus
 * @copyright   2025 G Wiz IT Solutions <support@gwizit.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/filelib.php');
require_once(__DIR__ . '/lib.php');

// Log the 404 error first - capture the ORIGINAL requested URL, not this error page.
// The URL should be passed as a query parameter from the ErrorDocument directive.
$url = optional_param('url', '', PARAM_TEXT);

// If not in query string, try server variables.
if (empty($url)) {
    // Try various server variables that might contain the original URL.
    if (!empty($_SERVER['REDIRECT_URL'])) {
        $url = $_SERVER['REDIRECT_URL'];
    } else if (!empty($_SERVER['REDIRECT_REQUEST_URI'])) {
        $url = $_SERVER['REDIRECT_REQUEST_URI'];
    } else if (!empty($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'error404.php') === false) {
        $url = $_SERVER['REQUEST_URI'];
    } else if (!empty($_SERVER['REDIRECT_SCRIPT_URL'])) {
        $url = $_SERVER['REDIRECT_SCRIPT_URL'];
    } else if (!empty($_SERVER['REDIRECT_QUERY_STRING'])) {
        // Parse from query string that Apache might pass.
        parse_str($_SERVER['REDIRECT_QUERY_STRING'], $query);
        if (isset($query['url'])) {
            $url = $query['url'];
        }
    }
}

// Clean up the URL - remove query parameters from this error page itself.
if (!empty($url) && strpos($url, '?') !== false) {
    $parts = explode('?', $url);
    // Only use the part before ? if it's not the error page.
    if (strpos($parts[0], 'error404.php') === false) {
        $url = $parts[0];
    }
}

// Last resort - mark as unknown but include debugging info.
if (empty($url) || strpos($url, 'error404.php') !== false) {
    $url = 'Unknown URL - Check configuration';
}

$referrer = $_SERVER['HTTP_REFERER'] ?? '';
$useragent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$ip = getremoteaddr();
$userid = $USER->id ?? 0;

// Check if 404 logging is enabled.
$config = tool_redirectplus_get_config();

if ($config->enable_404_logging) {
    try {
        $record = new stdClass();
        $record->url = $url;
        $record->referrer = $referrer;
        $record->userid = $userid;
        $record->timecreated = time();
        $record->ip = $ip;
        $record->useragent = $useragent;

        $DB->insert_record('tool_redirectplus_404', $record);
        
        // Prune old records to maintain the maximum limit.
        tool_redirectplus_prune_404_records();
        
        // Invalidate 404 report cache since we added a new record.
        $cache = cache::make('tool_redirectplus', 'report404');
        $cache->purge();
    } catch (Exception $exception) {
        // Silently fail - don't break the error page.
        debugging('tool_redirectplus: Failed to log 404 error - ' . $exception->getMessage(), DEBUG_DEVELOPER);
    }
}

// First, check for custom URL-specific redirects (unless user is admin and bypass is enabled).
if (!tool_redirectplus_should_bypass_redirect()) {
    $custom_redirect = tool_redirectplus_find_redirect($url);
    
    if ($custom_redirect) {
        // Get user context for conditional parameters.
        $is_logged_in = isloggedin() && !isguestuser();
        
        // Evaluate the redirect options to get destination URL.
        // Note: Language detection is now handled internally based on redirect's detection method settings
        $destination = tool_redirectplus_evaluate_redirect(
            $custom_redirect,
            $is_logged_in,
            ''
        );
        
        if ($destination) {
            redirect($destination);
            exit;
        }
    }
}

// Handle based on global behavior setting (config already loaded above).
if ($config->behavior === 'redirect' && !empty($config->redirect_url)) {
    redirect($config->redirect_url);
    exit;
}

// Otherwise, display custom message or default 404 page.
$PAGE->set_context(context_system::instance());
$PAGE->set_url('/admin/tool/redirectplus/error404.php');
$PAGE->set_title(get_string('error404title', 'tool_redirectplus'));
$PAGE->set_heading(get_string('error404heading', 'tool_redirectplus'));
$PAGE->set_pagelayout('standard');

echo $OUTPUT->header();

// Display custom message if set, otherwise default message.
if (!empty($config->custom_message)) {
    echo format_text($config->custom_message, FORMAT_HTML);
} else {
    // Default 404 message.
    echo html_writer::tag('div', 
        html_writer::tag('h2', get_string('error404heading', 'tool_redirectplus'), ['class' => 'text-danger']) .
        html_writer::tag('p', get_string('error404default', 'tool_redirectplus')) .
        html_writer::tag('p', get_string('error404url', 'tool_redirectplus', s($url))) .
        html_writer::tag('p', 
            html_writer::link($CFG->wwwroot, get_string('backtohome', 'tool_redirectplus'), ['class' => 'btn btn-primary'])
        ),
        ['class' => 'alert alert-warning']
    );
}

echo $OUTPUT->footer();
