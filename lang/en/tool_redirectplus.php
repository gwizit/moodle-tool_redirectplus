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
$string['tabreport'] = '404 Errors';
$string['tabredirects'] = 'Custom Redirects';
$string['tabsettings'] = 'Settings';
$string['tabsetup'] = 'Setup Instructions';
$string['viewsetupinstructions'] = 'View setup instructions';
$string['hidesetupinstructions'] = 'Hide setup instructions';

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
$string['error404behavior'] = '404 Error Page Behavior';
$string['behavior'] = 'Choose behavior';
$string['behavior_desc'] = 'Select how you want to handle 404 errors on your site.';
$string['behaviormessage'] = 'Show custom message';
$string['behaviorredirect'] = 'Redirect to another page';
$string['custommessage'] = 'Custom 404 Message';
$string['custommessage_help'] = 'Enter the HTML message to display to users when they encounter a 404 error. If left empty, a default "Page Not Found" message will be shown.';
$string['redirecturl'] = 'Redirect URL';
$string['redirecturl_help'] = 'Enter the full URL where users should be redirected when they encounter a 404 error.';
$string['disableredirectadmin'] = 'Disable redirect for administrators';
$string['disableredirectadmin_help'] = 'When enabled, any custom redirect will not work for administrators when they are signed in. This is useful for testing and troubleshooting.';
$string['savesettings'] = 'Save Settings';
$string['settingssaved'] = 'Settings saved successfully.';
$string['invalidurl'] = 'Invalid redirect URL format. Please enter a valid URL.';

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
$string['config_recommendation'] = 'Apache and Nginx methods are recommended for best performance and reliability. Use the .htaccess method only if you do not have access to server configuration files.';
$string['recommended'] = 'Recommended';
$string['fallback_only'] = 'Fallback Only';

$string['apache_method'] = 'Apache Server Configuration';
$string['apache_instructions'] = 'Add this line to your Apache server configuration or virtual host file:';
$string['apache_note'] = 'Important: The %{REQUEST_URI} variable automatically passes the original URL to the error handler. Make sure you copy the line exactly as shown above, including the ?url= parameter.';

$string['nginx_method'] = 'Nginx Server Configuration';
$string['nginx'] = 'Nginx';
$string['nginx_instructions'] = 'Add this line to your Nginx server configuration block:';
$string['nginx_note'] = 'Important: The $request_uri variable automatically passes the original URL to the error handler. Make sure you copy the line exactly as shown, including the ?url= parameter.';

$string['htaccess_method'] = '.htaccess Auto-Prepend Method';
$string['htaccess_warning_title'] = 'Warning: Often Does Not Work - Use Only as Last Resort';
$string['htaccess_warning'] = 'This method uses php_value auto_prepend_file which:<ul><li>Does NOT work with PHP-FPM or FastCGI (most modern hosting)</li><li>May cause 500 Internal Server Error</li><li>Requires mod_php and specific Apache settings</li><li>Has performance implications</li></ul>Only attempt this if you cannot configure Apache or Nginx directly.';
$string['htaccess_backup_title'] = 'Backup First!';
$string['htaccess_backup_warning'] = 'Before modifying your .htaccess file, create a backup copy. If you get a 500 error after making changes, restore the backup immediately.';
$string['htaccess_manual_instructions_title'] = 'Manual Configuration Steps';
$string['htaccess_manual_step1'] = '1. Create a backup of your .htaccess file (if it exists)';
$string['htaccess_manual_step2'] = '2. Add this line to the top of your .htaccess file in the Moodle root directory:';
$string['htaccess_manual_note'] = 'Note: This method often fails on modern hosting. If your site returns a 500 error after adding this line, immediately remove it and restore your backup. Use the Apache or Nginx configuration methods instead.';

$string['test_configuration'] = 'Test Your Configuration';
$string['test_configuration_desc'] = 'Use the automated test below or manually test by visiting a non-existent URL in a new browser tab.';
$string['test_404_tracking'] = 'Run Automated Test';
$string['testing'] = 'Testing...';
$string['test_success'] = 'Test Passed!';
$string['test_failure'] = 'Test Failed';
$string['test_error'] = 'An error occurred during testing';
$string['manual_test'] = 'Manual Test Alternative';
$string['manual_test_desc'] = 'Open a new browser tab and visit a non-existent URL like: {$a}. Then check the 404 Errors tab to see if it was logged.';

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

// Custom Redirects tab.
$string['customredirects'] = 'Custom URL Redirects';
$string['customredirects_desc'] = 'Create custom redirects for specific URLs with optional conditional parameters based on user login status and language. These redirects work for BOTH existing pages (like homepage or /faq/) and 404 errors automatically using Moodle\'s callback system.';
$string['addredirect'] = 'Add New Redirect';
$string['editredirect'] = 'Edit Redirect';
$string['addeditredirect'] = 'Add/Edit Redirect';
$string['deleteredirect'] = 'Delete Redirect';
$string['deleteredirectconfirm'] = 'Are you sure you want to delete this redirect?';
$string['redirectdeleted'] = 'Redirect deleted successfully.';
$string['redirectsaved'] = 'Redirect saved successfully.';
$string['noredirects'] = 'No custom redirects have been created yet.';
$string['sourceurl'] = 'Source URL';
$string['sourceurl_help'] = 'The URL path to match (without the domain). Example: / or /faq/ or /old-page. This works for BOTH existing pages and 404 errors automatically. The plugin intercepts requests early in the page load using Moodle\'s callback system.';
$string['destinationurl'] = 'Destination URL';
$string['destinationurl_help'] = 'The full URL where users should be redirected.';
$string['enableredirect'] = 'Enable this redirect';
$string['redirectparameters'] = 'Conditional Parameters';
$string['redirectparameters_help'] = 'Optional: Add conditions based on user login status or language.';
$string['useloginparam'] = 'Use login status parameter';
$string['useloginparam_help'] = 'When enabled, redirect users to different URLs based on whether they are logged in or not.';
$string['loggedin_url'] = 'URL for logged in users';
$string['loggedout_url'] = 'URL for guests/not logged in';
$string['uselanguageparam'] = 'Use language parameter';
$string['uselanguageparam_help'] = 'When enabled, redirect users to different URLs based on their language.';
$string['detectlanguageby'] = 'Detect language by';
$string['detectlanguageby_help'] = 'Choose how to detect the user\'s language. At least one method must be selected.';
$string['detectlanguagebrowser'] = 'Browser language (HTTP Accept-Language header)';
$string['detectlanguagemoodle'] = 'Moodle user language preference';
$string['detectlanguageerror'] = 'At least one language detection method must be selected!';
$string['addlanguagerule'] = 'Add Language Rule';
$string['removelanguagerule'] = 'Remove';
$string['languagecode'] = 'Language Code';
$string['languagecode_help'] = 'ISO 639-1 language code with wildcard support (e.g., en, en-us, pt-br, en*, pt*, en-*). Wildcards match multiple languages: pt* matches pt, pt-pt, pt-br; en-* matches en-us, en-gb, etc.';
$string['languageurl'] = 'Redirect URL for this language';
$string['defaultlanguageurl'] = 'Default URL (if no language rules match)';
$string['defaultlanguageurl_help'] = 'This URL will be used if none of the language rules above match the user\'s language. Leave blank to use the main destination URL.';
$string['language_priority_title'] = 'Language Priority & Wildcards';
$string['language_priority_desc'] = 'Language rules are evaluated in order from top to bottom. The first matching rule will be used. Use the up/down arrows to reorder rules.';
$string['language_wildcard_exact'] = '<strong>Exact match:</strong> <code>en-us</code> matches only en-us';
$string['language_wildcard_partial'] = '<strong>Prefix wildcard:</strong> <code>pt*</code> matches pt, pt-pt, pt-br, etc.';
$string['language_wildcard_all'] = '<strong>Suffix wildcard:</strong> <code>en-*</code> matches en-us, en-gb, en-au, etc.';
$string['moveup'] = 'Move Up';
$string['movedown'] = 'Move Down';
$string['delete'] = 'Delete';
$string['status'] = 'Status';
$string['enabled'] = 'Enabled';
$string['disabled'] = 'Disabled';
$string['lastmodified'] = 'Last Modified';
$string['saveredirect'] = 'Save Redirect';
$string['cancel'] = 'Cancel';
$string['parametersnote'] = 'Note: Parameters are evaluated in order: Login status first, then language. If both are enabled, the URL will be determined by both conditions.';
$string['redirectoptions'] = 'Redirect Options';
$string['basicredirect'] = 'Simple redirect to one URL';
$string['conditionalredirect'] = 'Conditional redirect with parameters';
$string['redirectexamples'] = 'Example Use Cases';
$string['redirectexamples_desc'] = '<ul>
<li><strong>Homepage language redirect:</strong> Source URL: <code>/</code> - Redirect Spanish speakers to <code>/es/</code>, French to <code>/fr/</code>, others to <code>/en/</code></li>
<li><strong>FAQ for guests:</strong> Source URL: <code>/faq/</code> - Redirect logged-out users to <code>/guest-faq.html</code>, logged-in users stay on page</li>
<li><strong>Members area:</strong> Source URL: <code>/members/</code> - Redirect logged-in users to dashboard, logged-out users to login page</li>
<li><strong>404 redirects:</strong> Source URL: <code>/old-page</code> - Redirect to <code>/new-page</code></li>
</ul>';

// Edit redirect form strings.
$string['howredirectswork'] = 'How Redirects Work';
$string['howredirectswork_desc'] = 'This plugin uses Moodle\'s after_config callback to intercept requests early in the page lifecycle, before any output is sent. This allows redirecting both existing pages and 404 errors based on your conditions.';
$string['worksfor'] = 'Works For';
$string['worksfor_desc'] = 'Any page (homepage /, /faq/, etc.) and 404 errors';
$string['conditions'] = 'Conditions';
$string['conditions_desc'] = 'Login status (logged in vs guest) and/or user language (browser or Moodle preference)';
$string['languagerule'] = 'Language Rule';

// 404 Error Page Configuration.
$string['error404_configuration'] = '404 Error Page Configuration';
$string['enable_404_logging'] = 'Enable 404 error logging';
$string['enable_404_logging_help'] = 'When enabled, all 404 errors will be logged to the database for tracking and analysis. When disabled, no 404 errors will be recorded and the 404 Errors tab will not show new entries.';
$string['max_404_records'] = 'Maximum 404 error records to keep';
$string['max_404_records_help'] = 'The maximum number of 404 error records to store in the database. When this limit is reached, the oldest records will be automatically deleted to make room for new ones. Default: 1000 records.';

// Donation strings.
$string['donation_title'] = 'Support Redirect Plus Development';
$string['donation_message'] = 'If you find Redirect Plus useful, please consider making a donation to support continued development and maintenance. Your contribution helps keep this plugin free and up-to-date!';
$string['donate_now'] = 'Donate Now';
$string['report_issue'] = 'Report an Issue';

// Redirect Configuration.
$string['redirect_configuration'] = 'Redirect Configuration';

// Welcome message.
$string['welcome_title'] = 'Welcome to Redirect Plus!';
$string['welcome_message'] = 'Redirect Plus is a powerful tool that gives you complete control over your Moodle site\'s redirects and 404 error handling. Create custom redirects with optional conditional logic based on user login status or language preference. The plugin also records 404 errors and allows you to customize how they are handled - either display a custom message or redirect users to a specific page. Get started by reviewing the setup instructions below and configuring your preferences.';

// Placeholder text for form fields.
$string['placeholder_sourceurl'] = '/old-page';
$string['placeholder_destinationurl'] = 'https://example.com/new-page';
$string['placeholder_loggedin_url'] = 'https://example.com/member-page';
$string['placeholder_loggedout_url'] = 'https://example.com/guest-page';
$string['placeholder_default_language_url'] = 'https://example.com/default-page';
$string['placeholder_404_redirect_url'] = 'https://example.com/404-page';

// Test section strings.
$string['test_automated_heading'] = 'Automated Browser Test';
$string['test_automated_description'] = 'Click the button below to open a test page in a new window. The test will verify that the 404 error was logged to the database:';
$string['test_manual_description'] = 'You can also manually test by clicking this link (opens in new tab):';
$string['test_manual_link'] = 'Open Manual Test Page';
$string['test_manual_check_notice'] = 'After clicking, check the "404 Errors" tab to see if it was logged.';

// JavaScript strings for settings.js.
$string['js_opening_test_page'] = 'Opening test page in new window...';
$string['js_popup_blocked'] = 'Popup Blocked';
$string['js_popup_blocked_message'] = 'Your browser blocked the popup. Please allow popups for this site and try again, or use the manual test link below.';
$string['js_test_page_opened'] = 'Test page opened in new window. Waiting for page to load and error to be logged...';
$string['js_test_url'] = 'Test URL: <code>{$a}</code>';
$string['js_checking_database'] = 'Checking database for logged error...';
$string['js_test_success_title'] = 'Test Passed!';
$string['js_test_success_message'] = 'The 404 error was successfully logged to the database!';
$string['js_details'] = 'Details:';
$string['js_user_agent'] = 'User Agent: <code>{$a}</code>';
$string['js_logged_at'] = 'Logged at: {$a}';
$string['js_tracking_working'] = 'Your 404 error tracking is working correctly!';
$string['js_test_failed_title'] = 'Test Failed - No Logging Detected';
$string['js_test_failed_message'] = 'The test page was opened, but the 404 error was NOT logged to the database.';
$string['js_this_means'] = 'This means:';
$string['js_server_not_configured'] = 'Your web server is NOT configured to use the error404.php file as the error handler';
$string['js_need_configure_server'] = 'You need to configure Apache or Nginx following the instructions above';
$string['js_next_steps'] = 'Next Steps:';
$string['js_expand_config_section'] = '1. Expand the Apache or Nginx configuration section above';
$string['js_add_error_directive'] = '2. Add the ErrorDocument directive to your server configuration';
$string['js_restart_server'] = '3. Restart your web server';
$string['js_run_test_again'] = '4. Run this test again';
$string['js_test_url_attempted'] = 'Test URL attempted: <code>{$a}</code>';
$string['js_test_error_checking'] = 'Failed to check database: {$a}';

// cache definitions.
$string['cachedef_report404'] = 'Cached 404 error report data to improve performance';
$string['cachedef_redirectslist'] = 'Cached list of custom redirects for faster lookup';
$string['cachedef_pluginconfig'] = 'Cached plugin configuration settings';
