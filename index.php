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

$tool_redirectplus_tab = optional_param('tab', 'report', PARAM_ALPHA);
$tool_redirectplus_page = optional_param('page', 0, PARAM_INT);
$tool_redirectplus_perpage = optional_param('perpage', 50, PARAM_INT);
$tool_redirectplus_delete = optional_param('delete', 0, PARAM_INT);
$tool_redirectplus_deleteall = optional_param('deleteall', 0, PARAM_BOOL);
$tool_redirectplus_confirm = optional_param('confirm', 0, PARAM_BOOL);
$tool_redirectplus_test = optional_param('test', 0, PARAM_BOOL);

$tool_redirectplus_context = context_system::instance();
require_capability('moodle/site:config', $tool_redirectplus_context);

$tool_redirectplus_baseurl = new moodle_url('/admin/tool/redirectplus/index.php');
$PAGE->set_url($tool_redirectplus_baseurl, ['tab' => $tool_redirectplus_tab, 'page' => $tool_redirectplus_page, 'perpage' => $tool_redirectplus_perpage]);
$PAGE->set_context($tool_redirectplus_context);
$PAGE->set_title(get_string('pluginname', 'tool_redirectplus'));
$PAGE->set_heading(get_string('pluginname', 'tool_redirectplus'));

// Ensure Bootstrap collapse JS is loaded for accordions.
$PAGE->requires->js_call_amd('core/tree', 'init');

// Handle settings form submission.
if ($tool_redirectplus_tab === 'settings' && data_submitted() && confirm_sesskey()) {
    $tool_redirectplus_redirect_url = optional_param('redirect_url', '', PARAM_TEXT);
    $tool_redirectplus_custom_message = optional_param('custom_message', '', PARAM_RAW);
    $tool_redirectplus_custom_message_format = optional_param('custom_message_format', FORMAT_HTML, PARAM_INT);

    // Only save redirect URL if it's actually a valid URL or empty.
    if (!empty($tool_redirectplus_redirect_url)) {
        // Validate URL format.
        if (filter_var($tool_redirectplus_redirect_url, FILTER_VALIDATE_URL)) {
            set_config('redirect_url', $tool_redirectplus_redirect_url, 'tool_redirectplus');
        } else {
            redirect($PAGE->url, get_string('invalidurl', 'tool_redirectplus'), null, \core\output\notification::NOTIFY_ERROR);
        }
    } else {
        set_config('redirect_url', '', 'tool_redirectplus');
    }

    set_config('custom_message', $tool_redirectplus_custom_message, 'tool_redirectplus');
    set_config('custom_message_format', $tool_redirectplus_custom_message_format, 'tool_redirectplus');

    redirect($PAGE->url, get_string('settingssaved', 'tool_redirectplus'), null, \core\output\notification::NOTIFY_SUCCESS);
}

// Handle delete single record.
if ($tool_redirectplus_delete && confirm_sesskey()) {
    if ($tool_redirectplus_confirm) {
        $DB->delete_records('tool_redirectplus_404', ['id' => $tool_redirectplus_delete]);
        redirect($PAGE->url, get_string('recorddeleted', 'tool_redirectplus'), null, \core\output\notification::NOTIFY_SUCCESS);
    } else {
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('deleterecord', 'tool_redirectplus'));
        $tool_redirectplus_confirmurl = new moodle_url($PAGE->url, [
            'delete' => $tool_redirectplus_delete,
            'confirm' => 1,
            'sesskey' => sesskey(),
        ]);
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
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('deleteallrecords', 'tool_redirectplus'));
        $tool_redirectplus_confirmurl = new moodle_url($PAGE->url, [
            'deleteall' => 1,
            'confirm' => 1,
            'sesskey' => sesskey(),
        ]);
        echo $OUTPUT->confirm(
            get_string('deleteallrecordsconfirm', 'tool_redirectplus'),
            $tool_redirectplus_confirmurl,
            $PAGE->url
        );
        echo $OUTPUT->footer();
        die();
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'tool_redirectplus'));

// Tab navigation.
$tool_redirectplus_tabs = [
    new tabobject('report', new moodle_url($tool_redirectplus_baseurl, ['tab' => 'report']), get_string('tabreport', 'tool_redirectplus')),
    new tabobject('settings', new moodle_url($tool_redirectplus_baseurl, ['tab' => 'settings']), get_string('tabsettings', 'tool_redirectplus')),
    new tabobject('setup', new moodle_url($tool_redirectplus_baseurl, ['tab' => 'setup']), get_string('tabsetup', 'tool_redirectplus')),
];

echo $OUTPUT->tabtree($tool_redirectplus_tabs, $tool_redirectplus_tab);

// Display content based on active tab.
if ($tool_redirectplus_tab === 'settings') {
    // SETTINGS TAB.
    echo html_writer::start_tag('form', [
        'method' => 'post',
        'action' => $PAGE->url->out(false),
        'class' => 'mform',
    ]);

    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'tab', 'value' => 'settings']);

    // Get current settings.
    $tool_redirectplus_redirect_url = get_config('tool_redirectplus', 'redirect_url');
    $tool_redirectplus_custom_message = get_config('tool_redirectplus', 'custom_message');

    echo html_writer::start_div('form-group');
    
    echo html_writer::tag('div',
        html_writer::tag('p', get_string('settingsintro', 'tool_redirectplus')),
        ['class' => 'alert alert-info']
    );

    echo html_writer::tag('h4', get_string('error404behavior', 'tool_redirectplus'));
    echo html_writer::tag('p', get_string('error404behavior_desc', 'tool_redirectplus'), ['class' => 'text-muted']);

    // Custom message (default behavior).
    echo html_writer::tag('h5', get_string('custommessage', 'tool_redirectplus'), ['class' => 'mt-4']);
    echo html_writer::tag('p', get_string('custommessage_desc', 'tool_redirectplus'), ['class' => 'text-muted']);

    echo html_writer::start_div('form-group mb-4');
    echo html_writer::tag('label', get_string('custommessage', 'tool_redirectplus'), ['for' => 'id_custom_message', 'class' => 'font-weight-bold']);
    
    // Use Moodle's editor for the custom message.
    $tool_redirectplus_editoroptions = [
        'maxfiles' => 0,
        'maxbytes' => 0,
        'trusttext' => false,
        'context' => $tool_redirectplus_context,
        'subdirs' => false,
    ];
    
    $tool_redirectplus_draftid_editor = file_get_submitted_draft_itemid('custom_message');
    $tool_redirectplus_currenttext = $tool_redirectplus_custom_message;
    
    // Prepare editor.
    $tool_redirectplus_editor = editors_get_preferred_editor(FORMAT_HTML);
    $tool_redirectplus_editor->use_editor('id_custom_message', $tool_redirectplus_editoroptions);
    
    echo html_writer::tag('textarea', s($tool_redirectplus_custom_message), [
        'name' => 'custom_message',
        'id' => 'id_custom_message',
        'rows' => 20,
        'style' => 'min-height: 400px;',
        'class' => 'form-control',
    ]);
    echo html_writer::empty_tag('input', [
        'type' => 'hidden',
        'name' => 'custom_message_format',
        'value' => FORMAT_HTML,
    ]);
    echo html_writer::tag('div', get_string('custommessage_help', 'tool_redirectplus'), ['class' => 'form-text mt-2']);
    echo html_writer::end_div();

    // Redirect option (alternative to custom message).
    echo html_writer::tag('h5', get_string('orredirect', 'tool_redirectplus'), ['class' => 'mt-4']);
    echo html_writer::tag('p', get_string('redirecturl_desc', 'tool_redirectplus'), ['class' => 'text-muted']);

    echo html_writer::start_div('form-group mb-3');
    echo html_writer::tag('label', get_string('redirecturl', 'tool_redirectplus'), ['for' => 'id_redirect_url', 'class' => 'font-weight-bold']);
    echo html_writer::empty_tag('input', [
        'type' => 'text',
        'name' => 'redirect_url',
        'id' => 'id_redirect_url',
        'value' => s($tool_redirectplus_redirect_url),
        'class' => 'form-control',
        'placeholder' => 'https://example.com/404-page',
    ]);
    echo html_writer::tag('div', get_string('redirecturl_help', 'tool_redirectplus'), ['class' => 'form-text']);
    echo html_writer::end_div();

    echo html_writer::end_div();

    // Submit button.
    echo html_writer::start_div('form-group mt-4');
    echo html_writer::empty_tag('input', [
        'type' => 'submit',
        'value' => get_string('savesettings', 'tool_redirectplus'),
        'class' => 'btn btn-primary',
    ]);
    echo html_writer::end_div();

    echo html_writer::end_tag('form');

} else if ($tool_redirectplus_tab === 'setup') {
    // SETUP TAB.
    echo html_writer::tag('h3', get_string('setupinstructions', 'tool_redirectplus'));

    $tool_redirectplus_error404_url = (new moodle_url('/admin/tool/redirectplus/error404.php'))->out(false);
    $tool_redirectplus_full_url = $CFG->wwwroot . '/admin/tool/redirectplus/error404.php';

    echo html_writer::tag('div',
        html_writer::tag('p', get_string('setupintro', 'tool_redirectplus')) .
        html_writer::tag('p', html_writer::tag('strong', get_string('your404url', 'tool_redirectplus')) . ' ' .
            html_writer::tag('code', $tool_redirectplus_full_url, ['class' => 'p-2 bg-light d-inline-block'])
        ),
        ['class' => 'alert alert-info']
    );

    // Test button.
    echo html_writer::start_div('mb-4');
    echo html_writer::tag('h4', get_string('testtracking', 'tool_redirectplus'));
    echo html_writer::tag('p', get_string('testtracking_desc', 'tool_redirectplus'));
    echo html_writer::link(
        new moodle_url('/admin/tool/redirectplus/error404.php', ['test' => 1]),
        get_string('test404page', 'tool_redirectplus'),
        ['class' => 'btn btn-secondary', 'target' => '_blank']
    );
    echo html_writer::tag('p', get_string('test404note', 'tool_redirectplus'), ['class' => 'text-muted small mt-2']);
    echo html_writer::end_div();

    // Troubleshooting section.
    echo html_writer::start_div('mb-4 p-3 bg-light border rounded');
    echo html_writer::tag('h5', get_string('troubleshooting_title', 'tool_redirectplus'));
    echo html_writer::tag('p', get_string('troubleshooting_desc', 'tool_redirectplus'), ['class' => 'small']);
    
    // Show a sample of what we'd see in a real 404.
    echo html_writer::tag('p', get_string('troubleshooting_variables', 'tool_redirectplus'), ['class' => 'small font-weight-bold']);
    echo html_writer::tag('pre', 
        'If the plugin shows "Unknown URL", your server is not passing REDIRECT_URL.' . "\n" .
        'Check your .htaccess file includes the SetEnvIf line shown above.' . "\n" .
        'You can also check your server error logs for more details.',
        ['class' => 'bg-white p-2 small border']
    );
    echo html_writer::end_div();

    // Server configuration instructions.
    echo html_writer::tag('h4', get_string('serverconfiguration', 'tool_redirectplus'), ['class' => 'mt-4']);

    // Apache/.htaccess - Default open.
    echo html_writer::start_div('card mb-3');
    echo html_writer::start_div('card-header');
    echo html_writer::tag('h5', 
        html_writer::link('#', get_string('apache_htaccess', 'tool_redirectplus') . ' ▼', [
            'class' => 'text-decoration-none text-dark',
            'data-toggle' => 'collapse',
            'data-target' => '#collapseApache',
            'aria-expanded' => 'true',
            'id' => 'apacheToggle',
        ]),
        ['class' => 'mb-0']
    );
    echo html_writer::end_div();
    echo html_writer::start_div('collapse show', ['id' => 'collapseApache']);
    echo html_writer::start_div('card-body');
    echo html_writer::tag('p', get_string('apache_instructions', 'tool_redirectplus'));
    
    // Apache config that passes URL as query parameter.
    $tool_redirectplus_apache_config = "ErrorDocument 404 " . $tool_redirectplus_error404_url . "?url=%{REQUEST_URI}";
    
    echo html_writer::tag('pre', $tool_redirectplus_apache_config, ['class' => 'bg-dark text-white p-3']);
    echo html_writer::tag('div', get_string('apache_note', 'tool_redirectplus'), ['class' => 'alert alert-warning mt-2']);
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();

    // Nginx - Collapsed by default.
    echo html_writer::start_div('card mb-3');
    echo html_writer::start_div('card-header');
    echo html_writer::tag('h5',
        html_writer::link('#', get_string('nginx', 'tool_redirectplus') . ' ▶', [
            'class' => 'text-decoration-none text-dark collapsed',
            'data-toggle' => 'collapse',
            'data-target' => '#collapseNginx',
            'aria-expanded' => 'false',
            'id' => 'nginxToggle',
        ]),
        ['class' => 'mb-0']
    );
    echo html_writer::end_div();
    echo html_writer::start_div('collapse', ['id' => 'collapseNginx']);
    echo html_writer::start_div('card-body');
    echo html_writer::tag('p', get_string('nginx_instructions', 'tool_redirectplus'));
    
    $tool_redirectplus_nginx_config = "error_page 404 " . $tool_redirectplus_error404_url . "?url=\$request_uri;";
    
    echo html_writer::tag('pre', $tool_redirectplus_nginx_config, ['class' => 'bg-dark text-white p-3']);
    echo html_writer::tag('div', get_string('nginx_note', 'tool_redirectplus'), ['class' => 'alert alert-info mt-2']);
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();

    // Plesk - Collapsed by default.
    echo html_writer::start_div('card mb-3');
    echo html_writer::start_div('card-header');
    echo html_writer::tag('h5',
        html_writer::link('#', get_string('plesk', 'tool_redirectplus') . ' ▶', [
            'class' => 'text-decoration-none text-dark collapsed',
            'data-toggle' => 'collapse',
            'data-target' => '#collapsePlesk',
            'aria-expanded' => 'false',
            'id' => 'pleskToggle',
        ]),
        ['class' => 'mb-0']
    );
    echo html_writer::end_div();
    echo html_writer::start_div('collapse', ['id' => 'collapsePlesk']);
    echo html_writer::start_div('card-body');
    echo html_writer::tag('p', get_string('plesk_instructions', 'tool_redirectplus'));
    echo html_writer::tag('ol',
        html_writer::tag('li', get_string('plesk_step1', 'tool_redirectplus')) .
        html_writer::tag('li', get_string('plesk_step2', 'tool_redirectplus')) .
        html_writer::tag('li', get_string('plesk_step3', 'tool_redirectplus')) .
        html_writer::tag('li', 'Add: ' . html_writer::tag('code', 'ErrorDocument 404 ' . $tool_redirectplus_error404_url . '?url=%{REQUEST_URI}'))
    );
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();

    // Add inline JavaScript to handle collapse toggle icons.
    $PAGE->requires->js_amd_inline("
    require(['jquery'], function($) {
        $('#apacheToggle').on('click', function(e) {
            e.preventDefault();
            $('#collapseApache').collapse('toggle');
            $(this).text($(this).text().indexOf('▼') > -1 ? 
                '" . get_string('apache_htaccess', 'tool_redirectplus') . " ▶' : 
                '" . get_string('apache_htaccess', 'tool_redirectplus') . " ▼');
        });
        $('#nginxToggle').on('click', function(e) {
            e.preventDefault();
            $('#collapseNginx').collapse('toggle');
            $(this).text($(this).text().indexOf('▼') > -1 ? 
                '" . get_string('nginx', 'tool_redirectplus') . " ▶' : 
                '" . get_string('nginx', 'tool_redirectplus') . " ▼');
        });
        $('#pleskToggle').on('click', function(e) {
            e.preventDefault();
            $('#collapsePlesk').collapse('toggle');
            $(this).text($(this).text().indexOf('▼') > -1 ? 
                '" . get_string('plesk', 'tool_redirectplus') . " ▶' : 
                '" . get_string('plesk', 'tool_redirectplus') . " ▼');
        });
    });
    ");

} else {
    // REPORT TAB (default).
    echo html_writer::tag('h3', get_string('report404', 'tool_redirectplus'));

    // Display introduction.
    echo html_writer::tag('p', get_string('report404desc', 'tool_redirectplus'));

    // Count total records.
    $tool_redirectplus_totalcount = $DB->count_records('tool_redirectplus_404');

    if ($tool_redirectplus_totalcount == 0) {
        echo $OUTPUT->notification(get_string('no404errors', 'tool_redirectplus'), \core\output\notification::NOTIFY_INFO);
        echo $OUTPUT->footer();
        die();
    }

    // Display delete all button.
    $tool_redirectplus_deleteallurl = new moodle_url($PAGE->url, [
        'deleteall' => 1,
        'sesskey' => sesskey(),
    ]);
    echo html_writer::tag('div',
        html_writer::link($tool_redirectplus_deleteallurl, get_string('deleteallrecords', 'tool_redirectplus'), [
            'class' => 'btn btn-danger mb-3',
        ]),
        ['class' => 'tool-redirectplus-actions']
    );

    // Create table.
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

// Get records.
$tool_redirectplus_records = $DB->get_records('tool_redirectplus_404', null, 'timecreated DESC',
    '*', $tool_redirectplus_page * $tool_redirectplus_perpage, $tool_redirectplus_perpage);

foreach ($tool_redirectplus_records as $tool_redirectplus_record) {
    $tool_redirectplus_row = [];
    
    // URL - truncate if too long.
    $tool_redirectplus_url = $tool_redirectplus_record->url;
    if (strlen($tool_redirectplus_url) > 80) {
        $tool_redirectplus_url = substr($tool_redirectplus_url, 0, 80) . '...';
    }
    $tool_redirectplus_row[] = html_writer::tag('span', s($tool_redirectplus_url), [
        'title' => s($tool_redirectplus_record->url),
    ]);
    
    // Referrer.
    $tool_redirectplus_referrer = $tool_redirectplus_record->referrer ?: '-';
    if (strlen($tool_redirectplus_referrer) > 50) {
        $tool_redirectplus_referrer = substr($tool_redirectplus_referrer, 0, 50) . '...';
    }
    $tool_redirectplus_row[] = html_writer::tag('span', s($tool_redirectplus_referrer), [
        'title' => s($tool_redirectplus_record->referrer),
    ]);
    
    // User.
    if ($tool_redirectplus_record->userid > 0) {
        try {
            $tool_redirectplus_user = $DB->get_record('user', ['id' => $tool_redirectplus_record->userid]);
            if ($tool_redirectplus_user) {
                $tool_redirectplus_userlink = html_writer::link(
                    new moodle_url('/user/profile.php', ['id' => $tool_redirectplus_user->id]),
                    fullname($tool_redirectplus_user)
                );
                $tool_redirectplus_row[] = $tool_redirectplus_userlink;
            } else {
                $tool_redirectplus_row[] = get_string('deleteduser', 'tool_redirectplus');
            }
        } catch (Exception $e) {
            $tool_redirectplus_row[] = get_string('unknownuser', 'tool_redirectplus');
        }
    } else {
        $tool_redirectplus_row[] = get_string('guest');
    }
    
    // IP Address.
    $tool_redirectplus_row[] = s($tool_redirectplus_record->ip);
    
    // User Agent - truncate if too long.
    $tool_redirectplus_useragent = $tool_redirectplus_record->useragent ?: '-';
    if (strlen($tool_redirectplus_useragent) > 60) {
        $tool_redirectplus_useragent = substr($tool_redirectplus_useragent, 0, 60) . '...';
    }
    $tool_redirectplus_row[] = html_writer::tag('span', s($tool_redirectplus_useragent), [
        'title' => s($tool_redirectplus_record->useragent),
    ]);
    
    // Time created.
    $tool_redirectplus_row[] = userdate($tool_redirectplus_record->timecreated);
    
    // Actions.
    $tool_redirectplus_deleteurl = new moodle_url($PAGE->url, [
        'delete' => $tool_redirectplus_record->id,
        'sesskey' => sesskey(),
    ]);
    $tool_redirectplus_row[] = html_writer::link($tool_redirectplus_deleteurl, get_string('delete'), [
        'class' => 'btn btn-sm btn-danger',
    ]);
    
    $tool_redirectplus_table->data[] = $tool_redirectplus_row;
}

    echo html_writer::table($tool_redirectplus_table);

    // Pagination.
    echo $OUTPUT->paging_bar($tool_redirectplus_totalcount, $tool_redirectplus_page, $tool_redirectplus_perpage, $PAGE->url);
}

echo $OUTPUT->footer();
