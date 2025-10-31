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

// Log the 404 error first - capture the ORIGINAL requested URL, not this error page.
// The URL should be passed as a query parameter from the ErrorDocument directive.
$tool_redirectplus_url = optional_param('url', '', PARAM_RAW);

// If not in query string, try server variables.
if (empty($tool_redirectplus_url)) {
    // Try various server variables that might contain the original URL.
    if (!empty($_SERVER['REDIRECT_URL'])) {
        $tool_redirectplus_url = $_SERVER['REDIRECT_URL'];
    } else if (!empty($_SERVER['REDIRECT_REQUEST_URI'])) {
        $tool_redirectplus_url = $_SERVER['REDIRECT_REQUEST_URI'];
    } else if (!empty($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'error404.php') === false) {
        $tool_redirectplus_url = $_SERVER['REQUEST_URI'];
    } else if (!empty($_SERVER['REDIRECT_SCRIPT_URL'])) {
        $tool_redirectplus_url = $_SERVER['REDIRECT_SCRIPT_URL'];
    } else if (!empty($_SERVER['REDIRECT_QUERY_STRING'])) {
        // Parse from query string that Apache might pass.
        parse_str($_SERVER['REDIRECT_QUERY_STRING'], $tool_redirectplus_query);
        if (isset($tool_redirectplus_query['url'])) {
            $tool_redirectplus_url = $tool_redirectplus_query['url'];
        }
    }
}

// Clean up the URL - remove query parameters from this error page itself.
if (!empty($tool_redirectplus_url) && strpos($tool_redirectplus_url, '?') !== false) {
    $tool_redirectplus_parts = explode('?', $tool_redirectplus_url);
    // Only use the part before ? if it's not the error page.
    if (strpos($tool_redirectplus_parts[0], 'error404.php') === false) {
        $tool_redirectplus_url = $tool_redirectplus_parts[0];
    }
}

// Last resort - mark as unknown but include debugging info.
if (empty($tool_redirectplus_url) || strpos($tool_redirectplus_url, 'error404.php') !== false) {
    $tool_redirectplus_url = 'Unknown URL - Check configuration';
}

$tool_redirectplus_referrer = $_SERVER['HTTP_REFERER'] ?? '';
$tool_redirectplus_useragent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$tool_redirectplus_ip = getremoteaddr();
$tool_redirectplus_userid = $USER->id ?? 0;

try {
    $tool_redirectplus_record = new stdClass();
    $tool_redirectplus_record->url = $tool_redirectplus_url;
    $tool_redirectplus_record->referrer = $tool_redirectplus_referrer;
    $tool_redirectplus_record->userid = $tool_redirectplus_userid;
    $tool_redirectplus_record->timecreated = time();
    $tool_redirectplus_record->ip = $tool_redirectplus_ip;
    $tool_redirectplus_record->useragent = $tool_redirectplus_useragent;

    $DB->insert_record('tool_redirectplus_404', $tool_redirectplus_record);
} catch (Exception $tool_redirectplus_exception) {
    // Silently fail - don't break the error page.
    debugging('tool_redirectplus: Failed to log 404 error - ' . $tool_redirectplus_exception->getMessage(), DEBUG_DEVELOPER);
}

// Get plugin configuration.
$tool_redirectplus_redirect_url = get_config('tool_redirectplus', 'redirect_url');
$tool_redirectplus_custom_message = get_config('tool_redirectplus', 'custom_message');

// If redirect URL is set, redirect immediately (takes priority over custom message).
if (!empty($tool_redirectplus_redirect_url)) {
    redirect($tool_redirectplus_redirect_url);
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
if (!empty($tool_redirectplus_custom_message)) {
    echo format_text($tool_redirectplus_custom_message, FORMAT_HTML);
} else {
    // Default 404 message.
    echo html_writer::tag('div', 
        html_writer::tag('h2', get_string('error404heading', 'tool_redirectplus'), ['class' => 'text-danger']) .
        html_writer::tag('p', get_string('error404default', 'tool_redirectplus')) .
        html_writer::tag('p', get_string('error404url', 'tool_redirectplus', s($tool_redirectplus_url))) .
        html_writer::tag('p', 
            html_writer::link($CFG->wwwroot, get_string('backtohome', 'tool_redirectplus'), ['class' => 'btn btn-primary'])
        ),
        ['class' => 'alert alert-warning']
    );
}

echo $OUTPUT->footer();
