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
$string['uselanguageparam_help'] = 'When enabled, redirect users to different URLs based on their browser language.';
$string['addlanguagerule'] = 'Add Language Rule';
$string['removelanguagerule'] = 'Remove';
$string['languagecode'] = 'Language Code';
$string['languagecode_help'] = 'ISO 639-1 language code (e.g., en, es, fr, de, ja)';
$string['languageurl'] = 'Redirect URL for this language';
$string['defaultlanguageurl'] = 'Default URL (if language not matched)';
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
