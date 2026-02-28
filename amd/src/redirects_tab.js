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
 * Redirects tab functionality for Redirect Plus plugin.
 *
 * @module     tool_redirectplus/redirects_tab
 * @copyright  2025 G Wiz IT Solutions {@link https://gwizit.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'core/notification'], function($, Ajax, Notification) {
    'use strict';

    return {
        /**
         * Initialize the redirects tab functionality.
         */
        init: function() {
            // Add click handler to "Add New Redirect" button.
            $('#btn-add-redirect').on('click', function(e) {
                e.preventDefault();
                var url = $(this).attr('href') + '#redirects';
                window.location.href = url;
            });

            // Add click handler to all edit buttons.
            $(document).on('click', 'a[href*="action=edit"]', function(e) {
                e.preventDefault();
                var url = $(this).attr('href') + '#redirects';
                window.location.href = url;
            });

            // Toggle redirect enabled/disabled via AJAX.
            $(document).on('change', '.redirect-toggle', function() {
                var checkbox = $(this);
                var redirectId = checkbox.data('redirect-id');
                var badge = $('#redirect-status-badge-' + redirectId);

                // Disable the toggle while the request is in flight.
                checkbox.prop('disabled', true);

                Ajax.call([{
                    methodname: 'tool_redirectplus_toggle_redirect',
                    args: {id: redirectId}
                }])[0].done(function(response) {
                    if (response.success) {
                        // Update badge text and class to reflect the new status.
                        badge.text(response.statustext);
                        if (response.enabled) {
                            badge.removeClass('text-bg-secondary').addClass('text-bg-success');
                        } else {
                            badge.removeClass('text-bg-success').addClass('text-bg-secondary');
                        }
                    }
                    checkbox.prop('disabled', false);
                }).fail(function(error) {
                    // Revert the checkbox on failure.
                    checkbox.prop('checked', !checkbox.prop('checked'));
                    checkbox.prop('disabled', false);
                    Notification.exception(error);
                });
            });
        }
    };
});
