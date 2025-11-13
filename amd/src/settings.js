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
        var buttonText = config.strings.test_404_tracking;
        
        $('#btn-test-404').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span> ' + config.strings.testing);
        $('#test-result-container').html('<div class="alert alert-info">' + config.strings.js_opening_test_page + '</div>');
        
        // Generate unique URL to test with timestamp
        var timestamp = Date.now();
        var randomPath = '/does-not-exist-' + timestamp;
        var testUrl = config.wwwroot + randomPath;
        
        // Open the URL in a new window (removed noopener,noreferrer to avoid popup blocking)
        var testWindow = window.open(testUrl, '_blank');
        
        if (!testWindow) {
            $('#test-result-container').html(
                '<div class="alert alert-danger">' +
                '<strong>' + config.strings.js_popup_blocked + '</strong><br>' +
                config.strings.js_popup_blocked_message +
                '</div>'
            );
            $('#btn-test-404').prop('disabled', false).html('<i class="fa fa-flask"></i> ' + buttonText);
            return;
        }
        
        var testUrlString = config.strings.js_test_url.replace('{$a}', randomPath);
        $('#test-result-container').html(
            '<div class="alert alert-info">' +
            config.strings.js_test_page_opened + '<br>' +
            '<small>' + testUrlString + '</small>' +
            '</div>'
        );
        
        // Wait for the page to load and the error to be logged (3 seconds should be plenty)
        setTimeout(function() {
            // Close the test window
            if (testWindow && !testWindow.closed) {
                testWindow.close();
            }
            
            $('#test-result-container').html('<div class="alert alert-info">' + config.strings.js_checking_database + '</div>');
            
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
                    var testUrlString = config.strings.js_test_url.replace('{$a}', randomPath);
                    var userAgentString = config.strings.js_user_agent.replace('{$a}', response.useragent.substring(0, 60) + '...');
                    var loggedAtString = config.strings.js_logged_at.replace('{$a}', response.time);
                    
                    $('#test-result-container').html(
                        '<div class="alert alert-success">' +
                        '<strong><i class="fa fa-check-circle"></i> ' + config.strings.js_test_success_title + '</strong><br>' +
                        config.strings.js_test_success_message + '<br><br>' +
                        '<strong>' + config.strings.js_details + '</strong><br>' +
                        testUrlString + '<br>' +
                        userAgentString + '<br>' +
                        loggedAtString + '<br><br>' +
                        '<i class="fa fa-check text-success"></i> ' + config.strings.js_tracking_working +
                        '</div>'
                    );
                } else {
                    // Failed - no record found
                    var testUrlAttemptedString = config.strings.js_test_url_attempted.replace('{$a}', randomPath);
                    
                    $('#test-result-container').html(
                        '<div class="alert alert-warning">' +
                        '<strong><i class="fa fa-exclamation-triangle"></i> ' + config.strings.js_test_failed_title + '</strong><br><br>' +
                        config.strings.js_test_failed_message + '<br><br>' +
                        '<strong>' + config.strings.js_this_means + '</strong><br>' +
                        '• ' + config.strings.js_server_not_configured + '<br>' +
                        '• ' + config.strings.js_need_configure_server + '<br><br>' +
                        '<strong>' + config.strings.js_next_steps + '</strong><br>' +
                        config.strings.js_expand_config_section + '<br>' +
                        config.strings.js_add_error_directive + '<br>' +
                        config.strings.js_restart_server + '<br>' +
                        config.strings.js_run_test_again + '<br><br>' +
                        testUrlAttemptedString +
                        '</div>'
                    );
                }
                
                $('#btn-test-404').prop('disabled', false).html('<i class="fa fa-flask"></i> ' + buttonText);
            }).fail(function(ex) {
                Notification.exception(ex);
                var errorMessage = config.strings.js_test_error_checking.replace('{$a}', ex.message || 'Unknown error');
                $('#test-result-container').html(
                    '<div class="alert alert-danger">' +
                    '<strong>' + config.strings.test_error + '</strong><br>' +
                    errorMessage +
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

        // Handle collapse toggle icons for Redirect Configuration.
        $('#redirectConfigToggle').on('click', function(e) {
            e.preventDefault();
            $('#collapseRedirectConfig').collapse('toggle');
            var icon = $('#redirectConfigIcon');
            var isExpanded = $('#collapseRedirectConfig').hasClass('show');
            
            if (isExpanded) {
                // Currently expanded, about to collapse
                icon.removeClass('fa-chevron-down').addClass('fa-chevron-right');
            } else {
                // Currently collapsed, about to expand
                icon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
            }
        });

        // Handle collapse toggle icons for 404 Error Configuration.
        $('#error404Toggle').on('click', function(e) {
            e.preventDefault();
            $('#collapseError404Config').collapse('toggle');
            var icon = $('#error404Icon');
            var isExpanded = $('#collapseError404Config').hasClass('show');
            
            if (isExpanded) {
                // Currently expanded, about to collapse
                icon.removeClass('fa-chevron-down').addClass('fa-chevron-right');
            } else {
                // Currently collapsed, about to expand
                icon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
            }
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
