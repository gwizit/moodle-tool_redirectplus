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
 * Edit redirect form functionality for Redirect Plus plugin.
 *
 * @module     tool_redirectplus/edit_redirect_form
 * @copyright  2025 G Wiz IT Solutions {@link https://gwizit.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery'], function($) {
    'use strict';

    var config = {};

    return {
        /**
         * Initialize the edit redirect form functionality.
         * 
         * @param {Object} options Configuration options
         * @param {string} options.cancel_url URL to redirect to on cancel
         * @param {Object} options.strings Localized strings
         */
        init: function(options) {
            config = options || {};

            // Handle cancel button click
            $('#btn-cancel-redirect').on('click', function(e) {
                e.preventDefault();
                window.location.href = config.cancel_url + '#redirects';
            });
            
            // Toggle redirect type sections
            $('#id_redirect_type').on('change', function() {
                var simpleSection = $('#simple_redirect_section');
                var conditionalSection = $('#conditional_redirect_section');
                
                if ($(this).val() === 'simple') {
                    simpleSection.show();
                    conditionalSection.hide();
                } else {
                    simpleSection.hide();
                    conditionalSection.show();
                }
            });

            // Toggle login parameter section
            $('#id_use_login_param').on('change', function() {
                $('#login_param_section').toggle(this.checked);
            });

            // Toggle language parameter section
            $('#id_use_language_param').on('change', function() {
                $('#language_param_section').toggle(this.checked);
            });

            // Language detection method validation - prevent both from being unchecked
            $('.language-detection-method').on('change', function() {
                var browserChecked = $('#id_detect_language_browser').is(':checked');
                var moodleChecked = $('#id_detect_language_moodle').is(':checked');
                
                // If trying to uncheck the last checked box, prevent it and show error
                if (!browserChecked && !moodleChecked) {
                    $(this).prop('checked', true);
                    alert(config.strings.detectlanguageerror);
                }
            });

            // Add language rule
            $('#add_language_rule').on('click', function() {
                var container = $('#language_rules_container');
                var ruleCount = container.find('.language-rule-row').length + 1;
                var newRow = $('<div class="language-rule-row mb-3 pb-3" style="border-bottom: 1px solid #dee2e6;"></div>');
                
                newRow.html(
                    '<div class="mb-2 d-flex justify-content-between align-items-center">' +
                        '<strong>' + config.strings.languagerule + ' #<span class="rule-number">' + ruleCount + '</span></strong>' +
                        '<div class="btn-group btn-group-sm" role="group">' +
                            '<button type="button" class="btn btn-outline-secondary move-rule-up" title="' + config.strings.moveup + '">' +
                                '<i class="fa fa-arrow-up"></i>' +
                            '</button>' +
                            '<button type="button" class="btn btn-outline-secondary move-rule-down" title="' + config.strings.movedown + '">' +
                                '<i class="fa fa-arrow-down"></i>' +
                            '</button>' +
                        '</div>' +
                    '</div>' +
                    '<div class="row g-3">' +
                        '<div class="col-md-6">' +
                            '<input type="text" name="language_code[]" class="form-control" placeholder="' + config.strings.languagecode + '">' +
                            '<small class="form-text text-muted">' + config.strings.languagecode_help + '</small>' +
                        '</div>' +
                        '<div class="col-md-6">' +
                            '<input type="url" name="language_url[]" class="form-control" placeholder="' + config.strings.languageurl + '">' +
                            '<button type="button" class="btn btn-danger btn-sm remove-language-rule mt-2">' + config.strings.delete + '</button>' +
                        '</div>' +
                    '</div>'
                );
                
                container.append(newRow);
                attachRuleHandlers(newRow);
            });

            // Remove language rule
            $(document).on('click', '.remove-language-rule', function() {
                $(this).closest('.language-rule-row').remove();
                updateRuleNumbers();
            });

            // Move rule up
            $(document).on('click', '.move-rule-up', function() {
                var row = $(this).closest('.language-rule-row');
                var prev = row.prev('.language-rule-row');
                if (prev.length) {
                    row.insertBefore(prev);
                    updateRuleNumbers();
                }
            });

            // Move rule down
            $(document).on('click', '.move-rule-down', function() {
                var row = $(this).closest('.language-rule-row');
                var next = row.next('.language-rule-row');
                if (next.length) {
                    row.insertAfter(next);
                    updateRuleNumbers();
                }
            });

            // Function to attach handlers to new rows
            var attachRuleHandlers = function(row) {
                row.find('.remove-language-rule').on('click', function() {
                    row.remove();
                    updateRuleNumbers();
                });
            };

            // Function to update rule numbers
            var updateRuleNumbers = function() {
                $('.language-rule-row').each(function(index) {
                    $(this).find('.rule-number').text(index + 1);
                });
            };

            // Initialize display on page load
            $(document).ready(function() {
                // Trigger redirect type change to show correct section
                $('#id_redirect_type').trigger('change');
                
                // Show sections based on checkbox states
                if ($('#id_use_login_param').is(':checked')) {
                    $('#login_param_section').show();
                }
                
                if ($('#id_use_language_param').is(':checked')) {
                    $('#language_param_section').show();
                }
            });
        }
    };
});
