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

define(['jquery'], function($) {
    'use strict';

    return {
        /**
         * Initialize the redirects tab functionality.
         */
        init: function() {
            // Add click handler to "Add New Redirect" button
            $('#btn-add-redirect').on('click', function(e) {
                e.preventDefault();
                var url = $(this).attr('href') + '#redirects';
                window.location.href = url;
            });
            
            // Add click handler to all edit buttons
            $(document).on('click', 'a[href*="action=edit"]', function(e) {
                e.preventDefault();
                var url = $(this).attr('href') + '#redirects';
                window.location.href = url;
            });
        }
    };
});
