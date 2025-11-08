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

$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 50, PARAM_INT);
$delete = optional_param('delete', 0, PARAM_INT);
$deleteall = optional_param('deleteall', 0, PARAM_BOOL);
$confirm = optional_param('confirm', 0, PARAM_BOOL);
$action = optional_param('action', '', PARAM_ALPHA);
$editid = optional_param('editid', 0, PARAM_INT);

$context = context_system::instance();
require_capability('moodle/site:config', $context);

$baseurl = new moodle_url('/admin/tool/redirectplus/index.php');
$PAGE->set_url($baseurl, ['page' => $page, 'perpage' => $perpage]);
$PAGE->set_context($context);
$PAGE->set_title(get_string('pluginname', 'tool_redirectplus'));
$PAGE->set_heading(get_string('pluginname', 'tool_redirectplus'));

// Handle save redirect form submission.
if (data_submitted() && confirm_sesskey() && $action === 'saveredirect') {
    $source_url = required_param('source_url', PARAM_TEXT);
    $enabled = optional_param('enabled', 0, PARAM_INT);
    $redirect_type = optional_param('redirect_type', 'simple', PARAM_ALPHA);
    $redirect_id = optional_param('id', 0, PARAM_INT);
    
    // Build options array.
    $options = ['type' => $redirect_type];
    
    if ($redirect_type === 'simple') {
        $options['destination_url'] = required_param('destination_url', PARAM_URL);
    } else {
        // Conditional redirect.
        $options['destination_url'] = optional_param('destination_url', '', PARAM_URL);
        $options['use_login_param'] = optional_param('use_login_param', 0, PARAM_INT);
        
        if ($options['use_login_param']) {
            $options['loggedin_url'] = required_param('loggedin_url', PARAM_URL);
            $options['loggedout_url'] = required_param('loggedout_url', PARAM_URL);
        }
        
        $options['use_language_param'] = optional_param('use_language_param', 0, PARAM_INT);
        
        if ($options['use_language_param']) {
            // Get language detection methods
            $detect_browser = optional_param('detect_language_browser', 0, PARAM_INT);
            $detect_moodle = optional_param('detect_language_moodle', 0, PARAM_INT);
            
            // Ensure at least one detection method is selected (default to browser if none selected)
            if (!$detect_browser && !$detect_moodle) {
                $detect_browser = 1;
            }
            
            $options['language_detection_methods'] = [
                'browser' => $detect_browser ? true : false,
                'moodle' => $detect_moodle ? true : false,
            ];
            
            $lang_codes = optional_param_array('language_code', [], PARAM_TEXT);
            $lang_urls = optional_param_array('language_url', [], PARAM_URL);
            $language_rules = [];
            
            for ($i = 0; $i < count($lang_codes); $i++) {
                if (!empty($lang_codes[$i]) && !empty($lang_urls[$i])) {
                    $language_rules[] = [
                        'lang' => strtolower(trim($lang_codes[$i])),
                        'url' => $lang_urls[$i],
                    ];
                }
            }
            
            $options['language_rules'] = $language_rules;
            $options['default_language_url'] = optional_param('default_language_url', '', PARAM_URL);
        }
    }
    
    // Save to database.
    $record = new stdClass();
    $record->source_url = $source_url;
    $record->redirect_options = json_encode($options);
    $record->enabled = $enabled;
    $record->timemodified = time();
    
    if ($redirect_id) {
        $record->id = $redirect_id;
        $DB->update_record('tool_redirectplus_redirects', $record);
    } else {
        $record->timecreated = time();
        $DB->insert_record('tool_redirectplus_redirects', $record);
    }
    
    // Invalidate redirects list cache.
    $cache = cache::make('tool_redirectplus', 'redirectslist');
    $cache->purge();
    
    // Redirect back to the redirects tab
    $redirect_url = new moodle_url($baseurl, [], 'redirects');
    redirect($redirect_url, get_string('redirectsaved', 'tool_redirectplus'), null, \core\output\notification::NOTIFY_SUCCESS);
}

// Handle settings form submission (includes both 404 config and behavior settings).
if (data_submitted() && confirm_sesskey() && optional_param('tab', '', PARAM_ALPHA) === 'settings') {
    // 404 logging settings.
    $enable_404_logging = optional_param('enable_404_logging', 0, PARAM_INT);
    $max_404_records = optional_param('max_404_records', 1000, PARAM_INT);
    
    set_config('enable_404_logging', $enable_404_logging, 'tool_redirectplus');
    set_config('max_404_records', $max_404_records, 'tool_redirectplus');
    
    // Prune 404 records if the new limit is lower than current count.
    tool_redirectplus_prune_404_records();
    
    // 404 behavior settings.
    $behavior = optional_param('behavior', 'message', PARAM_ALPHA);
    $redirect_url = optional_param('redirect_url', '', PARAM_URL);
    $custom_message = optional_param('custom_message', '', PARAM_CLEANHTML);
    $custom_message_format = optional_param('custom_message_format', FORMAT_HTML, PARAM_INT);
    $disable_redirect_admin = optional_param('disable_redirect_admin', 0, PARAM_INT);

    set_config('behavior', $behavior, 'tool_redirectplus');
    set_config('disable_redirect_admin', $disable_redirect_admin, 'tool_redirectplus');

    if ($behavior === 'redirect') {
        if (!empty($redirect_url)) {
            set_config('redirect_url', $redirect_url, 'tool_redirectplus');
        }
    } else {
        set_config('custom_message', $custom_message, 'tool_redirectplus');
        set_config('custom_message_format', $custom_message_format, 'tool_redirectplus');
    }

    // Invalidate plugin config cache.
    $config_cache = cache::make('tool_redirectplus', 'pluginconfig');
    $config_cache->purge();

    // Redirect back to settings tab.
    $settings_url = new moodle_url($baseurl, [], 'settings');
    redirect($settings_url, get_string('settingssaved', 'tool_redirectplus'), null, \core\output\notification::NOTIFY_SUCCESS);
}

// Handle delete redirect.
$deleteredirect = optional_param('deleteredirect', 0, PARAM_INT);
if ($deleteredirect && confirm_sesskey()) {
    $DB->delete_records('tool_redirectplus_redirects', ['id' => $deleteredirect]);
    
    // Invalidate redirects list cache.
    $cache = cache::make('tool_redirectplus', 'redirectslist');
    $cache->purge();
    
    $redirects_url = new moodle_url($baseurl, [], 'redirects');
    redirect($redirects_url, get_string('redirectdeleted', 'tool_redirectplus'), null, \core\output\notification::NOTIFY_SUCCESS);
}

// Handle delete single 404 record.
if ($delete && confirm_sesskey()) {
    $DB->delete_records('tool_redirectplus_404', ['id' => $delete]);
    
    // Invalidate 404 report cache.
    $cache = cache::make('tool_redirectplus', 'report404');
    $cache->purge();
    
    $report_url = new moodle_url($baseurl, [], 'report');
    redirect($report_url, get_string('recorddeleted', 'tool_redirectplus'), null, \core\output\notification::NOTIFY_SUCCESS);
}

// Handle delete all records.
if ($deleteall && confirm_sesskey()) {
    $DB->delete_records('tool_redirectplus_404');
    
    // Invalidate 404 report cache.
    $cache = cache::make('tool_redirectplus', 'report404');
    $cache->purge();
    
    $report_url = new moodle_url($baseurl, [], 'report');
    redirect($report_url, get_string('allrecordsdeleted', 'tool_redirectplus'), null, \core\output\notification::NOTIFY_SUCCESS);
}

// Get renderer.
$renderer = $PAGE->get_renderer('tool_redirectplus');

// REPORT TAB - Render using output class (handles all data processing).
$report_tab = $renderer->render_report_tab(
    $page,
    $perpage,
    $baseurl
);

// REDIRECTS TAB - Build data.
$show_edit_form = ($action === 'add' || $action === 'edit' || $editid > 0);
$edit_form_html = '';

if ($show_edit_form) {
    // Render edit form - the output class handles all data processing.
    $redirect_id = $editid > 0 ? $editid : null;
    
    // If adding a new redirect with a pre-filled source URL.
    $prefill_source_url = optional_param('source_url', '', PARAM_TEXT);
    if ($action === 'add' && !empty($prefill_source_url) && !$redirect_id) {
        // Create a redirect object with the pre-filled source URL.
        $redirect_obj = new stdClass();
        $redirect_obj->id = 0;
        $redirect_obj->source_url = $prefill_source_url;
        $redirect_obj->enabled = 1;
        $redirect_obj->options = [
            'type' => 'simple',
            'destination_url' => '',
        ];
        $edit_form_html = $renderer->render_edit_redirect_form($redirect_obj, $baseurl);
    } else {
        $edit_form_html = $renderer->render_edit_redirect_form($redirect_id, $baseurl);
    }
}

// Initialize cache for redirects list.
$redirects_cache = cache::make('tool_redirectplus', 'redirectslist');
$cache_key = 'redirects_table_html_v3'; // Increment version to invalidate old cache (v3: icon instead of checkmark)

// Try to get cached redirects data.
$cached_data = $redirects_cache->get($cache_key);

if ($cached_data === false) {
    // Cache miss - build the redirects list from database.
    $redirects = $DB->get_records('tool_redirectplus_redirects', null, 'timecreated DESC');
    $has_redirects = count($redirects) > 0;

    if ($has_redirects) {
        $redirects_table = new html_table();
    $redirects_table->head = [
        get_string('sourceurl', 'tool_redirectplus'),
        get_string('redirectoptions', 'tool_redirectplus'),
        get_string('status', 'tool_redirectplus'),
        get_string('lastmodified', 'tool_redirectplus'),
        get_string('actions'),
    ];
    $redirects_table->attributes['class'] = 'admintable generaltable';
    $redirects_table->id = 'tool_redirectplus_redirects';

    foreach ($redirects as $redirect) {
        $redirect_row = [];
        
        // Source URL - make it clickable.
        $source_url_link = html_writer::link(
            $CFG->wwwroot . $redirect->source_url,
            s($redirect->source_url),
            ['target' => '_blank', 'rel' => 'noopener noreferrer']
        );
        $redirect_row[] = html_writer::tag('code', $source_url_link, ['class' => 'd-inline-block']);
        
        // Redirect options summary.
        $redirect_opts = json_decode($redirect->redirect_options, true);
        $opts_summary = '';
        
        if (isset($redirect_opts['type']) && $redirect_opts['type'] === 'simple') {
            $opts_summary = get_string('basicredirect', 'tool_redirectplus') . '<br>' .
                html_writer::tag('small', s($redirect_opts['destination_url'] ?? ''));
        } else {
            $opts_summary = get_string('conditionalredirect', 'tool_redirectplus');
            if (!empty($redirect_opts['use_login_param'])) {
                $opts_summary .= '<br><small><i class="fa fa-check text-success"></i> ' . get_string('useloginparam', 'tool_redirectplus') . '</small>';
            }
            if (!empty($redirect_opts['use_language_param'])) {
                $opts_summary .= '<br><small><i class="fa fa-check text-success"></i> ' . get_string('uselanguageparam', 'tool_redirectplus') . '</small>';
            }
        }
        
        $redirect_row[] = $opts_summary;
        
        // Status.
        if ($redirect->enabled) {
            $redirect_row[] = html_writer::tag('span', get_string('enabled', 'tool_redirectplus'), 
                ['class' => 'badge badge-success']);
        } else {
            $redirect_row[] = html_writer::tag('span', get_string('disabled', 'tool_redirectplus'), 
                ['class' => 'badge badge-secondary']);
        }
        
        // Last modified.
        $redirect_row[] = userdate($redirect->timemodified);
        
        // Actions - DON'T add hash here since this HTML is cached
        $edit_url = new moodle_url($baseurl, [
            'action' => 'edit',
            'editid' => $redirect->id,
        ]);
        $delete_url = new moodle_url($baseurl, [
            'deleteredirect' => $redirect->id,
            'sesskey' => sesskey(),
        ]);
        
        $actions = html_writer::link($edit_url->out(false), get_string('edit'), 
            ['class' => 'btn btn-sm btn-secondary redirect-edit-btn']) . ' ' .
            html_writer::link($delete_url->out(false), get_string('delete'), 
            ['class' => 'btn btn-sm btn-danger redirect-delete-btn', 'onclick' => 'return confirm(\'' . get_string('deleteredirectconfirm', 'tool_redirectplus') . '\');']);
        
        $redirect_row[] = $actions;
        
        $redirects_table->data[] = $redirect_row;
    }
    
    $redirects_table_html = html_writer::table($redirects_table);
    } else {
        $redirects_table_html = '';
    }
    
    // Store in cache.
    $cached_data = [
        'has_redirects' => $has_redirects,
        'table_html' => $redirects_table_html,
    ];
    $redirects_cache->set($cache_key, $cached_data);
} else {
    // Cache hit - use cached data.
    $has_redirects = $cached_data['has_redirects'];
    $redirects_table_html = $cached_data['table_html'];
}

$add_redirect_url = new moodle_url($baseurl, ['action' => 'add']);

$redirects_tab = $renderer->render_redirects_tab(
    $has_redirects,
    $show_edit_form,
    $edit_form_html,
    $redirects_table_html,
    $add_redirect_url->out(false),
    sesskey()
);

// SETTINGS TAB - Build data (combined with setup instructions).
$config = tool_redirectplus_get_config();
$error404_url = $CFG->wwwroot . '/admin/tool/redirectplus/error404.php';

// Initialize TinyMCE editor.
$editor = editors_get_preferred_editor(FORMAT_HTML);
$editor->use_editor('id_custom_message', [
    'maxfiles' => 0,
    'maxbytes' => 0,
    'context' => $context,
    'subdirs' => false,
]);

$settings_data = [
    'form_action' => $baseurl->out(false),
    'sesskey' => sesskey(),
    'behavior' => $config->behavior,
    'behavior_message' => $config->behavior === 'message',
    'behavior_redirect' => $config->behavior === 'redirect',
    'custom_message' => $config->custom_message,
    'redirect_url' => $config->redirect_url,
    'disable_redirect_admin' => $config->disable_redirect_admin,
    'enable_404_logging' => $config->enable_404_logging,
    'max_404_records' => $config->max_404_records,
    'error404_url' => $error404_url,
    'wwwroot' => $CFG->wwwroot,
];

$settings_tab = $renderer->render_settings_tab($settings_data);

// Render main page with tabs.
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'tool_redirectplus'));
echo $renderer->render_main_page(
    $report_tab,
    $redirects_tab,
    $settings_tab
);
echo $OUTPUT->footer();
