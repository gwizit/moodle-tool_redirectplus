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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Redirect Plus - Main Management Page with Tabs
 *
 * @package     tool_redirectplus
 * @copyright   2025 G Wiz IT Solutions <support@gwizit.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/tablelib.php');

admin_externalpage_setup('tool_redirectplus_manage');

$tool_redirectplus_page = optional_param('page', 0, PARAM_INT);
$tool_redirectplus_perpage = optional_param('perpage', 50, PARAM_INT);
$tool_redirectplus_delete = optional_param('delete', 0, PARAM_INT);
$tool_redirectplus_deleteall = optional_param('deleteall', 0, PARAM_BOOL);
$tool_redirectplus_confirm = optional_param('confirm', 0, PARAM_BOOL);

$tool_redirectplus_context = context_system::instance();
require_capability('moodle/site:config', $tool_redirectplus_context);

$tool_redirectplus_baseurl = new moodle_url('/admin/tool/redirectplus/index.php');
$PAGE->set_url($tool_redirectplus_baseurl, ['page' => $tool_redirectplus_page, 'perpage' => $tool_redirectplus_perpage]);
$PAGE->set_context($tool_redirectplus_context);
$PAGE->set_title(get_string('pluginname', 'tool_redirectplus'));
$PAGE->set_heading(get_string('pluginname', 'tool_redirectplus'));

// Handle settings form submission.
if (data_submitted() && confirm_sesskey() && optional_param('tab', '', PARAM_ALPHA) === 'settings') {
    $tool_redirectplus_behavior = optional_param('behavior', 'message', PARAM_ALPHA);
    $tool_redirectplus_redirect_url = optional_param('redirect_url', '', PARAM_TEXT);
    $tool_redirectplus_custom_message = optional_param('custom_message', '', PARAM_RAW);
    $tool_redirectplus_custom_message_format = optional_param('custom_message_format', FORMAT_HTML, PARAM_INT);

    set_config('behavior', $tool_redirectplus_behavior, 'tool_redirectplus');

    if ($tool_redirectplus_behavior === 'redirect') {
        if (!empty($tool_redirectplus_redirect_url) && filter_var($tool_redirectplus_redirect_url, FILTER_VALIDATE_URL)) {
            set_config('redirect_url', $tool_redirectplus_redirect_url, 'tool_redirectplus');
        } else if (!empty($tool_redirectplus_redirect_url)) {
            redirect($PAGE->url, get_string('invalidurl', 'tool_redirectplus'), null, \core\output\notification::NOTIFY_ERROR);
        }
    } else {
        set_config('custom_message', $tool_redirectplus_custom_message, 'tool_redirectplus');
        set_config('custom_message_format', $tool_redirectplus_custom_message_format, 'tool_redirectplus');
    }

    redirect($PAGE->url, get_string('settingssaved', 'tool_redirectplus'), null, \core\output\notification::NOTIFY_SUCCESS);
}

// Handle delete single record.
if ($tool_redirectplus_delete && confirm_sesskey()) {
    if ($tool_redirectplus_confirm) {
        $DB->delete_records('tool_redirectplus_404', ['id' => $tool_redirectplus_delete]);
        redirect($PAGE->url, get_string('recorddeleted', 'tool_redirectplus'), null, \core\output\notification::NOTIFY_SUCCESS);
    } else {
        $tool_redirectplus_confirmurl = new moodle_url($PAGE->url, [
            'delete' => $tool_redirectplus_delete,
            'confirm' => 1,
            'sesskey' => sesskey(),
        ]);
        echo $OUTPUT->header();
        echo $OUTPUT->confirm(
            get_string('deleterecordconfirm', 'tool_redirectplus'),
            $tool_redirectplus_confirmurl,
            $PAGE->url
        );
        echo $OUTPUT->footer();
        die();
    }
}

// Handle delete all records.
if ($tool_redirectplus_deleteall && confirm_sesskey()) {
    if ($tool_redirectplus_confirm) {
        $DB->delete_records('tool_redirectplus_404');
        redirect($PAGE->url, get_string('allrecordsdeleted', 'tool_redirectplus'), null, \core\output\notification::NOTIFY_SUCCESS);
    } else {
        $tool_redirectplus_confirmurl = new moodle_url($PAGE->url, [
            'deleteall' => 1,
            'confirm' => 1,
            'sesskey' => sesskey(),
        ]);
        echo $OUTPUT->header();
        echo $OUTPUT->confirm(
            get_string('deleteallrecordsconfirm', 'tool_redirectplus'),
            $tool_redirectplus_confirmurl,
            $PAGE->url
        );
        echo $OUTPUT->footer();
        die();
    }
}

// Get renderer.
$tool_redirectplus_renderer = $PAGE->get_renderer('tool_redirectplus');

// REPORT TAB - Build data.
$tool_redirectplus_totalcount = $DB->count_records('tool_redirectplus_404');
$tool_redirectplus_has_errors = $tool_redirectplus_totalcount > 0;

if ($tool_redirectplus_has_errors) {
    $tool_redirectplus_table = new html_table();
    $tool_redirectplus_table->head = [
        get_string('url', 'tool_redirectplus'),
        get_string('referrer', 'tool_redirectplus'),
        get_string('user'),
        get_string('ipaddress', 'tool_redirectplus'),
        get_string('useragent', 'tool_redirectplus'),
        get_string('timecreated', 'tool_redirectplus'),
        get_string('actions'),
    ];
    $tool_redirectplus_table->attributes['class'] = 'admintable generaltable';
    $tool_redirectplus_table->id = 'tool_redirectplus_report';

    $tool_redirectplus_records = $DB->get_records('tool_redirectplus_404', null, 'timecreated DESC',
        '*', $tool_redirectplus_page * $tool_redirectplus_perpage, $tool_redirectplus_perpage);

    foreach ($tool_redirectplus_records as $tool_redirectplus_record) {
        $tool_redirectplus_row = [];

        $tool_redirectplus_row[] = html_writer::link(
            $CFG->wwwroot . $tool_redirectplus_record->url,
            s($tool_redirectplus_record->url),
            ['target' => '_blank']
        );

        $tool_redirectplus_referrer = $tool_redirectplus_record->referrer ?: '-';
        if ($tool_redirectplus_referrer !== '-') {
            $tool_redirectplus_row[] = html_writer::link(
                $tool_redirectplus_referrer,
                s($tool_redirectplus_referrer),
                ['target' => '_blank']
            );
        } else {
            $tool_redirectplus_row[] = '-';
        }

        if ($tool_redirectplus_record->userid > 0) {
            $tool_redirectplus_user = $DB->get_record('user', ['id' => $tool_redirectplus_record->userid]);
            if ($tool_redirectplus_user) {
                $tool_redirectplus_row[] = fullname($tool_redirectplus_user);
            } else {
                $tool_redirectplus_row[] = get_string('deleteduser', 'tool_redirectplus');
            }
        } else {
            $tool_redirectplus_row[] = get_string('guest');
        }

        $tool_redirectplus_row[] = s($tool_redirectplus_record->ip);

        $tool_redirectplus_useragent = $tool_redirectplus_record->useragent ?: '-';
        if (strlen($tool_redirectplus_useragent) > 60) {
            $tool_redirectplus_useragent = substr($tool_redirectplus_useragent, 0, 60) . '...';
        }
        $tool_redirectplus_row[] = html_writer::tag('span', s($tool_redirectplus_useragent), [
            'title' => s($tool_redirectplus_record->useragent),
        ]);

        $tool_redirectplus_row[] = userdate($tool_redirectplus_record->timecreated);

        $tool_redirectplus_deleteurl = new moodle_url($PAGE->url, [
            'delete' => $tool_redirectplus_record->id,
            'sesskey' => sesskey(),
        ]);
        $tool_redirectplus_row[] = html_writer::link($tool_redirectplus_deleteurl, get_string('delete'), [
            'class' => 'btn btn-sm btn-danger',
        ]);

        $tool_redirectplus_table->data[] = $tool_redirectplus_row;
    }

    $tool_redirectplus_table_html = html_writer::table($tool_redirectplus_table);
    $tool_redirectplus_paging_html = $OUTPUT->paging_bar($tool_redirectplus_totalcount, $tool_redirectplus_page, $tool_redirectplus_perpage, $PAGE->url);
} else {
    $tool_redirectplus_table_html = '';
    $tool_redirectplus_paging_html = '';
}

$tool_redirectplus_deleteall_url = new moodle_url($PAGE->url, [
    'deleteall' => 1,
    'sesskey' => sesskey(),
]);

$tool_redirectplus_report_data = [
    'has_errors' => $tool_redirectplus_has_errors,
    'strings' => [
        'report404' => get_string('report404', 'tool_redirectplus'),
        'report404desc' => get_string('report404desc', 'tool_redirectplus'),
        'deleteallrecords' => get_string('deleteallrecords', 'tool_redirectplus'),
        'no404errors' => get_string('no404errors', 'tool_redirectplus'),
    ],
    'table_html' => $tool_redirectplus_table_html,
    'paging_html' => $tool_redirectplus_paging_html,
    'deleteall_url' => $tool_redirectplus_deleteall_url->out(false),
];

$tool_redirectplus_report_tab = $tool_redirectplus_renderer->render_report_tab($tool_redirectplus_report_data);

// SETTINGS TAB - Build data.
$tool_redirectplus_behavior = get_config('tool_redirectplus', 'behavior') ?: 'message';
$tool_redirectplus_redirect_url = get_config('tool_redirectplus', 'redirect_url');
$tool_redirectplus_custom_message = get_config('tool_redirectplus', 'custom_message');

// Initialize TinyMCE editor.
$tool_redirectplus_editor = editors_get_preferred_editor(FORMAT_HTML);
$tool_redirectplus_editor->use_editor('id_custom_message', [
    'maxfiles' => 0,
    'maxbytes' => 0,
    'context' => $tool_redirectplus_context,
    'subdirs' => false,
]);

$tool_redirectplus_settings_data = [
    'form_action' => $PAGE->url->out(false),
    'sesskey' => sesskey(),
    'behavior' => $tool_redirectplus_behavior,
    'behavior_message' => $tool_redirectplus_behavior === 'message',
    'behavior_redirect' => $tool_redirectplus_behavior === 'redirect',
    'custom_message' => $tool_redirectplus_custom_message,
    'redirect_url' => $tool_redirectplus_redirect_url,
    'strings' => [
        'error404behavior' => get_string('error404behavior', 'tool_redirectplus'),
        'behavior' => get_string('behavior', 'tool_redirectplus'),
        'behavior_desc' => get_string('behavior_desc', 'tool_redirectplus'),
        'behaviormessage' => get_string('behaviormessage', 'tool_redirectplus'),
        'behaviorredirect' => get_string('behaviorredirect', 'tool_redirectplus'),
        'custommessage' => get_string('custommessage', 'tool_redirectplus'),
        'custommessage_help' => get_string('custommessage_help', 'tool_redirectplus'),
        'redirecturl' => get_string('redirecturl', 'tool_redirectplus'),
        'redirecturl_help' => get_string('redirecturl_help', 'tool_redirectplus'),
        'savesettings' => get_string('savesettings', 'tool_redirectplus'),
        'apache_htaccess' => get_string('apache_htaccess', 'tool_redirectplus'),
        'nginx' => get_string('nginx', 'tool_redirectplus'),
        'plesk' => get_string('plesk', 'tool_redirectplus'),
    ],
];

$tool_redirectplus_settings_tab = $tool_redirectplus_renderer->render_settings_tab($tool_redirectplus_settings_data);

// SETUP TAB - Build data.
$tool_redirectplus_error404_url = $CFG->wwwroot . '/admin/tool/redirectplus/error404.php';

$tool_redirectplus_setup_data = [
    'error404_url' => $tool_redirectplus_error404_url,
    'wwwroot' => $CFG->wwwroot,
    'strings' => [
        'setupinstructions' => get_string('setupinstructions', 'tool_redirectplus'),
        'your404url' => get_string('your404url', 'tool_redirectplus'),
        'serverconfiguration' => get_string('serverconfiguration', 'tool_redirectplus'),
        'config_recommendation' => get_string('config_recommendation', 'tool_redirectplus'),
        'recommended' => get_string('recommended', 'tool_redirectplus'),
        'fallback_only' => get_string('fallback_only', 'tool_redirectplus'),
        'apache_method' => get_string('apache_method', 'tool_redirectplus'),
        'apache_instructions' => get_string('apache_instructions', 'tool_redirectplus'),
        'apache_note' => get_string('apache_note', 'tool_redirectplus'),
        'nginx_method' => get_string('nginx_method', 'tool_redirectplus'),
        'nginx_instructions' => get_string('nginx_instructions', 'tool_redirectplus'),
        'nginx_note' => get_string('nginx_note', 'tool_redirectplus'),
        'htaccess_method' => get_string('htaccess_method', 'tool_redirectplus'),
        'htaccess_warning_title' => get_string('htaccess_warning_title', 'tool_redirectplus'),
        'htaccess_warning' => get_string('htaccess_warning', 'tool_redirectplus'),
        'htaccess_backup_title' => get_string('htaccess_backup_title', 'tool_redirectplus'),
        'htaccess_backup_warning' => get_string('htaccess_backup_warning', 'tool_redirectplus'),
        'htaccess_manual_instructions_title' => get_string('htaccess_manual_instructions_title', 'tool_redirectplus'),
        'htaccess_manual_step1' => get_string('htaccess_manual_step1', 'tool_redirectplus'),
        'htaccess_manual_step2' => get_string('htaccess_manual_step2', 'tool_redirectplus'),
        'htaccess_manual_note' => get_string('htaccess_manual_note', 'tool_redirectplus'),
        'test_configuration' => get_string('test_configuration', 'tool_redirectplus'),
        'test_configuration_desc' => get_string('test_configuration_desc', 'tool_redirectplus'),
        'test_404_tracking' => get_string('test_404_tracking', 'tool_redirectplus'),
        'testing' => get_string('testing', 'tool_redirectplus'),
        'test_success' => get_string('test_success', 'tool_redirectplus'),
        'test_failure' => get_string('test_failure', 'tool_redirectplus'),
        'test_error' => get_string('test_error', 'tool_redirectplus'),
    ],
];

$tool_redirectplus_setup_tab = $tool_redirectplus_renderer->render_setup_tab($tool_redirectplus_setup_data);

// Render main page with all tabs.
$tool_redirectplus_main_data = [
    'report_tab' => $tool_redirectplus_report_tab,
    'settings_tab' => $tool_redirectplus_settings_tab,
    'setup_tab' => $tool_redirectplus_setup_tab,
    'strings' => [
        'tabreport' => get_string('tabreport', 'tool_redirectplus'),
        'tabsettings' => get_string('tabsettings', 'tool_redirectplus'),
        'tabsetup' => get_string('tabsetup', 'tool_redirectplus'),
    ],
];

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'tool_redirectplus'));
echo $tool_redirectplus_renderer->render_main_page($tool_redirectplus_main_data);
echo $OUTPUT->footer();
