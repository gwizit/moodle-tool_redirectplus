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
 * Setup page functionality for Redirect Plus plugin.
 *
 * @module     tool_redirectplus/setup
 * @copyright  2025 G Wiz IT Solutions {@link https://gwizit.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'core/notification'], function($, Ajax, Notification) {
    'use strict';

    var config = {};

    /**
     * Test 404 tracking
     */
    var test404Tracking = function() {
        $('#btn-test-404').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span> ' + config.strings.testing);
        $('#test-result-container').html('');
        
        // Generate random URL to test
        var randomUrl = '/test-404-' + Math.random().toString(36).substring(7);
        var testUrl = config.wwwroot + randomUrl;
        
        var request = Ajax.call([{
            methodname: 'tool_redirectplus_test_404',
            args: {
                testurl: testUrl
            }
        }])[0];

        request.done(function(response) {
            if (response.success) {
                $('#test-result-container').html('<div class="alert alert-success"><strong>' + config.strings.test_success + '</strong><br>' + response.message + '</div>');
            } else {
                $('#test-result-container').html('<div class="alert alert-danger"><strong>' + config.strings.test_failure + '</strong><br>' + response.message + '</div>');
            }
            $('#btn-test-404').prop('disabled', false).html('<i class="fa fa-flask"></i> Test 404 Tracking');
        }).fail(function(ex) {
            Notification.exception(ex);
            $('#test-result-container').html('<div class="alert alert-danger"><strong>' + config.strings.test_error + '</strong></div>');
            $('#btn-test-404').prop('disabled', false).html('<i class="fa fa-flask"></i> Test 404 Tracking');
        });
    };

    /**
     * Initialize the setup page functionality.
     *
     * @param {Object} cfg Configuration object
     */
    var init = function(cfg) {
        config = cfg;

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
    };

    return {
        init: init
    };
});
