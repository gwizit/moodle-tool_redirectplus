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
 * Main page tab navigation functionality for Redirect Plus plugin.
 *
 * @module     tool_redirectplus/main
 * @copyright  2025 G Wiz IT Solutions {@link https://gwizit.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery'], function($) {
    'use strict';

    return {
        /**
         * Initialize the main page tab navigation.
         */
        init: function() {
            // Function to activate tab based on hash
            var activateTabFromHash = function() {
                var hash = window.location.hash;
                if (hash) {
                    // Remove the # and find the corresponding tab
                    var tabId = hash.substring(1);
                    var tabLink = $('#' + tabId + '-tab');
                    
                    if (tabLink.length) {
                        // Remove active class from all tabs
                        $('.nav-link').removeClass('active');
                        $('.tab-pane').removeClass('show active');
                        
                        // Activate the target tab
                        tabLink.addClass('active');
                        $('#' + tabId).addClass('show active');
                    }
                }
            };
            
            // Activate tab on page load
            $(document).ready(function() {
                activateTabFromHash();
            });
            
            // Update hash when tab is clicked
            $('.nav-link[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                var hash = $(e.target).attr('href');
                if (history.pushState) {
                    history.pushState(null, null, hash);
                } else {
                    window.location.hash = hash;
                }
            });
            
            // Listen for hash changes (back/forward buttons)
            $(window).on('hashchange', function() {
                activateTabFromHash();
            });
        }
    };
});
