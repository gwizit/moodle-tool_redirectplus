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
 * Plugin strings are defined here.
 *
 * @package     tool_redirectplus
 * @category    string
 * @copyright   2025 G Wiz IT Solutions <support@gwizit.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Redirect Plus';

// Tab names.
$string['tabreport'] = 'Error Report';
$string['tabsettings'] = 'Settings';
$string['tabsetup'] = 'Setup Instructions';

// Report tab.
$string['report404'] = '404 Error Report';
$string['report404desc'] = 'This report shows all 404 errors that have been recorded on your Moodle site. Use this information to identify broken links and fix them.';
$string['no404errors'] = 'No 404 errors have been recorded yet.';
$string['url'] = 'URL';
$string['referrer'] = 'Referrer';
$string['ipaddress'] = 'IP Address';
$string['useragent'] = 'User Agent';
$string['timecreated'] = 'Time Created';
$string['deleterecord'] = 'Delete Record';
$string['deleterecordconfirm'] = 'Are you sure you want to delete this 404 error record?';
$string['recorddeleted'] = 'Record deleted successfully.';
$string['deleteallrecords'] = 'Delete All Records';
$string['deleteallrecordsconfirm'] = 'Are you sure you want to delete ALL 404 error records? This action cannot be undone.';
$string['allrecordsdeleted'] = 'All records deleted successfully.';
$string['deleteduser'] = 'Deleted user';
$string['unknownuser'] = 'Unknown user';

// Settings tab.
$string['settingsintro'] = 'Configure how users see 404 errors. By default, users will see your custom message below. Alternatively, you can redirect them to another page.';
$string['error404behavior'] = '404 Error Page Behavior';
$string['error404behavior_desc'] = 'Users will see the custom message below by default. If you provide a redirect URL, that will be used instead (redirect takes priority).';
$string['custommessage'] = 'Custom 404 Message (Default Behavior)';
$string['custommessage_desc'] = 'This message will be shown to users when they encounter a 404 error. You can use HTML to style it.';
$string['custommessage_help'] = 'HTML is allowed. If left empty, a default "Page Not Found" message will be displayed. This is used unless you provide a redirect URL below.';
$string['custommessage_placeholder'] = '<h2>Page Not Found</h2><p>Sorry, the page you are looking for could not be found.</p>';
$string['orredirect'] = 'OR Redirect to Another Page';
$string['redirecturl'] = 'Redirect URL (Optional)';
$string['redirecturl_desc'] = 'If you prefer to send users to a different page instead of showing a custom message, enter the URL here. This takes priority over the custom message above.';
$string['redirecturl_help'] = 'If this field has a URL, users will be automatically redirected there instead of seeing the custom message. Leave empty to use the custom message.';
$string['savesettings'] = 'Save Settings';
$string['settingssaved'] = 'Settings saved successfully.';
$string['invalidurl'] = 'Invalid redirect URL format. Please enter a valid URL or leave it empty.';

// Setup tab.
$string['setupinstructions'] = 'Setup Instructions';
$string['setupintro'] = 'To enable 404 error tracking, you need to configure your web server to use this plugin\'s custom 404 error page. Follow the instructions below for your server type.';
$string['your404url'] = 'Your custom 404 URL:';
$string['testtracking'] = 'Test 404 Tracking';
$string['testtracking_desc'] = 'Click the button below to open the 404 error page in a new tab. This will create a test entry in your error log.';
$string['test404page'] = 'Test 404 Page';
$string['test404note'] = 'Note: This test button loads the error page directly, so it may not perfectly simulate a real 404 error. To properly test, try visiting a non-existent page on your site after configuring your server.';
$string['serverconfiguration'] = 'Server Configuration';
$string['troubleshooting_title'] = 'Troubleshooting URL Detection';
$string['troubleshooting_desc'] = 'If you see "Unknown URL" in your error log, your web server is not properly passing the original URL to the error handler.';
$string['troubleshooting_variables'] = 'What to check:';

// Server types.
$string['apache_htaccess'] = 'Apache / .htaccess';
$string['apache_instructions'] = 'Add this line to your .htaccess file in your Moodle root directory:';
$string['apache_note'] = 'Important: The %{REQUEST_URI} variable automatically passes the original URL to the error handler. Make sure you copy the line exactly as shown above, including the ?url= parameter.';
$string['nginx'] = 'Nginx';
$string['nginx_instructions'] = 'Add this line to your Nginx server configuration block:';
$string['nginx_note'] = 'Important: The $request_uri variable automatically passes the original URL to the error handler. Make sure you copy the line exactly as shown, including the ?url= parameter.';
$string['plesk'] = 'Plesk Control Panel';
$string['plesk_instructions'] = 'Follow these steps in Plesk:';
$string['plesk_step1'] = 'Log in to your Plesk control panel';
$string['plesk_step2'] = 'Go to Websites & Domains > your domain > Apache & nginx Settings';
$string['plesk_step3'] = 'Scroll down to "Additional directives for HTTP" or "Additional nginx directives"';
$string['plesk_step4'] = 'Add: ErrorDocument 404 {$a}?url=%{{REQUEST_URI}}';

// Error page.
$string['error404title'] = 'Page Not Found - 404';
$string['error404heading'] = 'Page Not Found';
$string['error404default'] = 'Sorry, the page you are looking for could not be found. It may have been moved, deleted, or the URL may be incorrect.';
$string['error404url'] = 'Requested URL: {$a}';
$string['backtohome'] = 'Return to Home Page';

// Privacy strings.
$string['privacy:metadata:tool_redirectplus_404'] = 'Information about 404 errors encountered by users.';
$string['privacy:metadata:tool_redirectplus_404:userid'] = 'The ID of the user who encountered the 404 error.';
$string['privacy:metadata:tool_redirectplus_404:url'] = 'The URL that generated the 404 error.';
$string['privacy:metadata:tool_redirectplus_404:referrer'] = 'The URL the user came from (referrer).';
$string['privacy:metadata:tool_redirectplus_404:ip'] = 'The IP address of the user.';
$string['privacy:metadata:tool_redirectplus_404:useragent'] = 'The user agent (browser) information.';
$string['privacy:metadata:tool_redirectplus_404:timecreated'] = 'The time when the 404 error was encountered.';
