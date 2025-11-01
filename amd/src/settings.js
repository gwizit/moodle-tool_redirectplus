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

define(['jquery'], function($) {
    'use strict';

    /**
     * Initialize the settings page functionality.
     *
     * @param {Object} config Configuration object
     * @param {string} config.apacheText Text for Apache section
     * @param {string} config.nginxText Text for Nginx section
     * @param {string} config.pleskText Text for Plesk section
     */
    var init = function(config) {
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
            $(this).text($(this).text().indexOf('▼') > -1 ?
                config.apacheText + ' ▶' :
                config.apacheText + ' ▼');
        });

        // Handle collapse toggle icons for Nginx.
        $('#nginxToggle').on('click', function(e) {
            e.preventDefault();
            $('#collapseNginx').collapse('toggle');
            $(this).text($(this).text().indexOf('▼') > -1 ?
                config.nginxText + ' ▶' :
                config.nginxText + ' ▼');
        });

        // Handle collapse toggle icons for Plesk.
        $('#pleskToggle').on('click', function(e) {
            e.preventDefault();
            $('#collapsePlesk').collapse('toggle');
            $(this).text($(this).text().indexOf('▼') > -1 ?
                config.pleskText + ' ▶' :
                config.pleskText + ' ▼');
        });
    };

    return {
        init: init
    };
});
