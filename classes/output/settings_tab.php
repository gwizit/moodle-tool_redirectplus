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
 * Settings tab renderable for Redirect Plus plugin.
 *
 * @package    tool_redirectplus
 * @copyright  2025 G Wiz IT Solutions {@link https://gwizit.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_redirectplus\output;

use renderable;
use renderer_base;
use templatable;
use stdClass;

/**
 * Settings tab renderable class.
 *
 * @package    tool_redirectplus
 * @copyright  2025 G Wiz IT Solutions
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class settings_tab implements renderable, templatable {

    /** @var string Form action URL */
    protected $form_action;

    /** @var string Session key */
    protected $sesskey;

    /** @var string Behavior setting */
    protected $behavior;

    /** @var bool Behavior is message */
    protected $behavior_message;

    /** @var bool Behavior is redirect */
    protected $behavior_redirect;

    /** @var string Custom message */
    protected $custom_message;

    /** @var string Redirect URL */
    protected $redirect_url;

    /** @var bool Disable redirect for admin */
    protected $disable_redirect_admin;

    /** @var bool Enable 404 logging */
    protected $enable_404_logging;

    /** @var int Max 404 records */
    protected $max_404_records;

    /** @var string Error404 URL */
    protected $error404_url;

    /** @var string WWW root */
    protected $wwwroot;

    /**
     * Constructor.
     *
     * @param array $data Settings data
     */
    public function __construct($data) {
        $this->form_action = $data['form_action'];
        $this->sesskey = $data['sesskey'];
        $this->behavior = $data['behavior'];
        $this->behavior_message = $data['behavior_message'];
        $this->behavior_redirect = $data['behavior_redirect'];
        $this->custom_message = $data['custom_message'];
        $this->redirect_url = $data['redirect_url'];
        $this->disable_redirect_admin = $data['disable_redirect_admin'];
        $this->enable_404_logging = $data['enable_404_logging'];
        $this->max_404_records = $data['max_404_records'];
        $this->error404_url = $data['error404_url'];
        $this->wwwroot = $data['wwwroot'];
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output Renderer
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $data->form_action = $this->form_action;
        $data->sesskey = $this->sesskey;
        $data->behavior = $this->behavior;
        $data->behavior_message = $this->behavior_message;
        $data->behavior_redirect = $this->behavior_redirect;
        $data->custom_message = $this->custom_message;
        $data->redirect_url = $this->redirect_url;
        $data->disable_redirect_admin = $this->disable_redirect_admin;
        $data->enable_404_logging = $this->enable_404_logging;
        $data->max_404_records = $this->max_404_records;
        $data->error404_url = $this->error404_url;
        $data->wwwroot = $this->wwwroot;

        // Provide strings for JavaScript
        $data->js_strings = [
            'apache_method' => get_string('apache_method', 'tool_redirectplus'),
            'nginx_method' => get_string('nginx_method', 'tool_redirectplus'),
            'htaccess_method' => get_string('htaccess_method', 'tool_redirectplus'),
            'viewsetupinstructions' => get_string('viewsetupinstructions', 'tool_redirectplus'),
            'hidesetupinstructions' => get_string('hidesetupinstructions', 'tool_redirectplus'),
            'testing' => get_string('testing', 'tool_redirectplus'),
            'test_success' => get_string('test_success', 'tool_redirectplus'),
            'test_failure' => get_string('test_failure', 'tool_redirectplus'),
            'test_error' => get_string('test_error', 'tool_redirectplus'),
            'error404_configuration' => get_string('error404_configuration', 'tool_redirectplus'),
            'test_404_tracking' => get_string('test_404_tracking', 'tool_redirectplus'),
            'js_opening_test_page' => get_string('js_opening_test_page', 'tool_redirectplus'),
            'js_popup_blocked' => get_string('js_popup_blocked', 'tool_redirectplus'),
            'js_popup_blocked_message' => get_string('js_popup_blocked_message', 'tool_redirectplus'),
            'js_test_page_opened' => get_string('js_test_page_opened', 'tool_redirectplus'),
            'js_test_url' => get_string('js_test_url', 'tool_redirectplus'),
            'js_checking_database' => get_string('js_checking_database', 'tool_redirectplus'),
            'js_test_success_title' => get_string('js_test_success_title', 'tool_redirectplus'),
            'js_test_success_message' => get_string('js_test_success_message', 'tool_redirectplus'),
            'js_details' => get_string('js_details', 'tool_redirectplus'),
            'js_user_agent' => get_string('js_user_agent', 'tool_redirectplus'),
            'js_logged_at' => get_string('js_logged_at', 'tool_redirectplus'),
            'js_tracking_working' => get_string('js_tracking_working', 'tool_redirectplus'),
            'js_test_failed_title' => get_string('js_test_failed_title', 'tool_redirectplus'),
            'js_test_failed_message' => get_string('js_test_failed_message', 'tool_redirectplus'),
            'js_this_means' => get_string('js_this_means', 'tool_redirectplus'),
            'js_server_not_configured' => get_string('js_server_not_configured', 'tool_redirectplus'),
            'js_need_configure_server' => get_string('js_need_configure_server', 'tool_redirectplus'),
            'js_next_steps' => get_string('js_next_steps', 'tool_redirectplus'),
            'js_expand_config_section' => get_string('js_expand_config_section', 'tool_redirectplus'),
            'js_add_error_directive' => get_string('js_add_error_directive', 'tool_redirectplus'),
            'js_restart_server' => get_string('js_restart_server', 'tool_redirectplus'),
            'js_run_test_again' => get_string('js_run_test_again', 'tool_redirectplus'),
            'js_test_url_attempted' => get_string('js_test_url_attempted', 'tool_redirectplus'),
            'js_test_error_checking' => get_string('js_test_error_checking', 'tool_redirectplus'),
        ];

        return $data;
    }
}
