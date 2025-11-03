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
 * Settings page functionality for Redirect Plus plugin.
 *
 * @module     tool_redirectplus/settings
 * @copyright  2025 G Wiz IT Solutions {@link https://gwizit.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'core/notification'], function($, Ajax, Notification) {
    'use strict';

    var config = {};

    /**
     * Test 404 tracking by opening a new tab
     */
    var test404Tracking = function() {
        var buttonText = (config.strings && config.strings.test_404_tracking) ? 
            config.strings.test_404_tracking : 'Run Automated Test';
        
        $('#btn-test-404').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span> ' + config.strings.testing);
        $('#test-result-container').html('<div class="alert alert-info">Opening test page in new window...</div>');
        
        // Generate unique URL to test with timestamp
        var timestamp = Date.now();
        var randomPath = '/does-not-exist-' + timestamp;
        var testUrl = config.wwwroot + randomPath;
        
        // Open the URL in a new window (removed noopener,noreferrer to avoid popup blocking)
        var testWindow = window.open(testUrl, '_blank');
        
        if (!testWindow) {
            $('#test-result-container').html(
                '<div class="alert alert-danger">' +
                '<strong>Popup Blocked</strong><br>' +
                'Your browser blocked the popup. Please allow popups for this site and try again, ' +
                'or use the manual test link below.' +
                '</div>'
            );
            $('#btn-test-404').prop('disabled', false).html('<i class="fa fa-flask"></i> ' + buttonText);
            return;
        }
        
        $('#test-result-container').html(
            '<div class="alert alert-info">' +
            'Test page opened in new window. Waiting for page to load and error to be logged...<br>' +
            '<small>Test URL: <code>' + randomPath + '</code></small>' +
            '</div>'
        );
        
        // Wait for the page to load and the error to be logged (3 seconds should be plenty)
        setTimeout(function() {
            // Close the test window
            if (testWindow && !testWindow.closed) {
                testWindow.close();
            }
            
            $('#test-result-container').html('<div class="alert alert-info">Checking database for logged error...</div>');
            
            // Check if the specific URL was logged
            var checkRequest = Ajax.call([{
                methodname: 'tool_redirectplus_check_404_url',
                args: {
                    url: randomPath
                }
            }])[0];

            checkRequest.done(function(response) {
                if (response.found) {
                    // Success! The 404 was logged
                    $('#test-result-container').html(
                        '<div class="alert alert-success">' +
                        '<strong><i class="fa fa-check-circle"></i> ' + config.strings.test_success + '</strong><br>' +
                        'The 404 error was successfully logged to the database!<br><br>' +
                        '<strong>Details:</strong><br>' +
                        'Test URL: <code>' + randomPath + '</code><br>' +
                        'User Agent: <code>' + response.useragent.substring(0, 60) + '...</code><br>' +
                        'Logged at: ' + response.time + '<br><br>' +
                        '<i class="fa fa-check text-success"></i> Your 404 error tracking is working correctly!' +
                        '</div>'
                    );
                } else {
                    // Failed - no record found
                    $('#test-result-container').html(
                        '<div class="alert alert-warning">' +
                        '<strong><i class="fa fa-exclamation-triangle"></i> Test Failed - No Logging Detected</strong><br><br>' +
                        'The test page was opened, but the 404 error was NOT logged to the database.<br><br>' +
                        '<strong>This means:</strong><br>' +
                        '• Your web server is NOT configured to use the error404.php file as the error handler<br>' +
                        '• You need to configure Apache or Nginx following the instructions above<br><br>' +
                        '<strong>Next Steps:</strong><br>' +
                        '1. Expand the Apache or Nginx configuration section above<br>' +
                        '2. Add the ErrorDocument directive to your server configuration<br>' +
                        '3. Restart your web server<br>' +
                        '4. Run this test again<br><br>' +
                        'Test URL attempted: <code>' + randomPath + '</code>' +
                        '</div>'
                    );
                }
                
                $('#btn-test-404').prop('disabled', false).html('<i class="fa fa-flask"></i> ' + buttonText);
            }).fail(function(ex) {
                Notification.exception(ex);
                $('#test-result-container').html(
                    '<div class="alert alert-danger">' +
                    '<strong>' + config.strings.test_error + '</strong><br>' +
                    'Failed to check database: ' + (ex.message || 'Unknown error') +
                    '</div>'
                );
                $('#btn-test-404').prop('disabled', false).html('<i class="fa fa-flask"></i> ' + buttonText);
            });
        }, 3000); // Wait 3 seconds for page load + database insert + buffer
    };

    /**
     * Initialize the settings page functionality.
     *
     * @param {Object} cfg Configuration object
     * @param {string} cfg.wwwroot Moodle root URL
     * @param {string} cfg.error404url Full URL to error404.php
     * @param {string} cfg.apacheText Text for Apache section
     * @param {string} cfg.nginxText Text for Nginx section
     * @param {string} cfg.htaccessText Text for htaccess section
     * @param {Object} cfg.strings Language strings
     */
    var init = function(cfg) {
        config = cfg;

        // Handle setup instructions toggle button
        $('#toggleSetupInstructions').on('click', function(e) {
            e.preventDefault();
            $('#setupInstructionsContent').collapse('toggle');
            var icon = $('#setupIcon');
            var isExpanded = $('#setupInstructionsContent').hasClass('show');
            
            if (isExpanded) {
                // Currently expanded, about to collapse
                icon.removeClass('fa-chevron-down').addClass('fa-chevron-right');
                $(this).find('strong').text(config.strings.viewsetupinstructions);
            } else {
                // Currently collapsed, about to expand
                icon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
                $(this).find('strong').text(config.strings.hidesetupinstructions);
            }
        });

        // Handle behavior dropdown toggle.
        $('#id_behavior').on('change', function() {
            if ($(this).val() === 'message') {
                $('#message_section').show();
                $('#redirect_section').hide();
            } else {
                $('#message_section').hide();
                $('#redirect_section').show();
            }
        });

        // Handle collapse toggle icons for Apache.
        $('#apacheToggle').on('click', function(e) {
            e.preventDefault();
            $('#collapseApache').collapse('toggle');
            var icon = $(this).text().indexOf('▼') > -1 ? ' ▶' : ' ▼';
            $(this).html('<strong>' + config.apacheText + '</strong> (Recommended)' + icon);
        });

        // Handle collapse toggle icons for Nginx.
        $('#nginxToggle').on('click', function(e) {
            e.preventDefault();
            $('#collapseNginx').collapse('toggle');
            var icon = $(this).text().indexOf('▼') > -1 ? ' ▶' : ' ▼';
            $(this).html('<strong>' + config.nginxText + '</strong> (Recommended)' + icon);
        });

        // Handle collapse toggle icons for .htaccess.
        $('#htaccessToggle').on('click', function(e) {
            e.preventDefault();
            $('#collapseHtaccess').collapse('toggle');
            var icon = $(this).text().indexOf('▼') > -1 ? ' ▶' : ' ▼';
            $(this).html('<strong>' + config.htaccessText + '</strong> (Fallback Only)' + icon);
        });

        // Handle test button
        $('#btn-test-404').on('click', function(e) {
            e.preventDefault();
            test404Tracking();
        });

        // Handle manual test link
        $('#manual-test-link').on('click', function(e) {
            e.preventDefault();
            var timestamp = Date.now();
            var testPath = '/page-does-not-exist-' + timestamp;
            var testUrl = config.wwwroot + testPath;
            window.open(testUrl, '_blank');
        });
    };

    return {
        init: init
    };
});
