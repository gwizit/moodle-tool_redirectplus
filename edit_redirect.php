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
 * Add/Edit Redirect Page
 *
 * @package     tool_redirectplus
 * @copyright   2025 G Wiz IT Solutions <support@gwizit.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

admin_externalpage_setup('tool_redirectplus_manage');

$redirectid = optional_param('id', 0, PARAM_INT);
$context = context_system::instance();
require_capability('moodle/site:config', $context);

$baseurl = new moodle_url('/admin/tool/redirectplus/index.php');
$PAGE->set_url(new moodle_url('/admin/tool/redirectplus/edit_redirect.php', ['id' => $redirectid]));
$PAGE->set_context($context);

// Initialize redirect object.
if ($redirectid) {
    $redirect = $DB->get_record('tool_redirectplus_redirects', ['id' => $redirectid], '*', MUST_EXIST);
    $pagetitle = get_string('editredirect', 'tool_redirectplus');
    $redirect->options = json_decode($redirect->redirect_options, true);
} else {
    $redirect = new stdClass();
    $redirect->id = 0;
    $redirect->source_url = '';
    $redirect->enabled = 1;
    $redirect->options = [
        'type' => 'simple',
        'destination_url' => '',
        'use_login_param' => 0,
        'use_language_param' => 0,
        'language_rules' => [],
    ];
    $pagetitle = get_string('addredirect', 'tool_redirectplus');
}

$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);

// Handle form submission.
if (data_submitted() && confirm_sesskey()) {
    $source_url = required_param('source_url', PARAM_TEXT);
    $enabled = optional_param('enabled', 1, PARAM_INT);
    $redirect_type = optional_param('redirect_type', 'simple', PARAM_ALPHA);
    
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
    
    if ($redirectid) {
        $record->id = $redirectid;
        $DB->update_record('tool_redirectplus_redirects', $record);
    } else {
        $record->timecreated = time();
        $DB->insert_record('tool_redirectplus_redirects', $record);
    }
    
    redirect($baseurl, get_string('redirectsaved', 'tool_redirectplus'), null, \core\output\notification::NOTIFY_SUCCESS);
}

echo $OUTPUT->header();
echo $OUTPUT->heading($pagetitle);

// Display informational alert about redirect types.
?>
<div class="alert alert-info">
    <h5><i class="fa fa-info-circle"></i> How Redirects Work</h5>
    <p>This plugin uses Moodle's <code>after_config</code> callback to intercept requests early in the page lifecycle, before any output is sent. This allows redirecting both existing pages and 404 errors based on your conditions.</p>
    <p><strong>Works For:</strong> Any page (homepage /, /faq/, etc.) and 404 errors</p>
    <p><strong>Conditions:</strong> Login status (logged in vs guest) and/or user language (browser or Moodle preference)</p>
</div>

<form method="post" action="<?php echo $PAGE->url->out(false); ?>" class="mform">
    <input type="hidden" name="sesskey" value="<?php echo sesskey(); ?>">
    <input type="hidden" name="id" value="<?php echo $redirect->id; ?>">
    
    <!-- Basic Settings -->
    <div class="card mb-3">
        <div class="card-header bg-primary text-white">
            <strong><?php echo get_string('sourceurl', 'tool_redirectplus'); ?></strong>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label for="id_source_url"><?php echo get_string('sourceurl', 'tool_redirectplus'); ?> *</label>
                <input type="text" name="source_url" id="id_source_url" value="<?php echo s($redirect->source_url); ?>" 
                       class="form-control" placeholder="/old-page" required>
                <small class="form-text text-muted"><?php echo get_string('sourceurl_help', 'tool_redirectplus'); ?></small>
            </div>
            
            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" name="enabled" id="id_enabled" value="1" class="custom-control-input" 
                           <?php echo $redirect->enabled ? 'checked' : ''; ?>>
                    <label for="id_enabled" class="custom-control-label"><?php echo get_string('enableredirect', 'tool_redirectplus'); ?></label>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Redirect Type -->
    <div class="card mb-3">
        <div class="card-header bg-info text-white">
            <strong><?php echo get_string('redirectoptions', 'tool_redirectplus'); ?></strong>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label for="id_redirect_type"><?php echo get_string('redirectoptions', 'tool_redirectplus'); ?></label>
                <select name="redirect_type" id="id_redirect_type" class="form-control">
                    <option value="simple" <?php echo ($redirect->options['type'] ?? 'simple') === 'simple' ? 'selected' : ''; ?>>
                        <?php echo get_string('basicredirect', 'tool_redirectplus'); ?>
                    </option>
                    <option value="conditional" <?php echo ($redirect->options['type'] ?? 'simple') === 'conditional' ? 'selected' : ''; ?>>
                        <?php echo get_string('conditionalredirect', 'tool_redirectplus'); ?>
                    </option>
                </select>
            </div>
            
            <!-- Simple Redirect -->
            <div id="simple_redirect_section" style="display: none;">
                <div class="form-group">
                    <label for="id_destination_url"><?php echo get_string('destinationurl', 'tool_redirectplus'); ?> *</label>
                    <input type="url" name="destination_url" id="id_destination_url" 
                           value="<?php echo s($redirect->options['destination_url'] ?? ''); ?>" 
                           class="form-control" placeholder="https://example.com/new-page">
                    <small class="form-text text-muted"><?php echo get_string('destinationurl_help', 'tool_redirectplus'); ?></small>
                </div>
            </div>
            
            <!-- Conditional Redirect -->
            <div id="conditional_redirect_section" style="display: none;">
                <p class="text-info"><?php echo get_string('parametersnote', 'tool_redirectplus'); ?></p>
                
                <!-- Login Parameter -->
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="use_login_param" id="id_use_login_param" value="1" 
                               class="custom-control-input" <?php echo !empty($redirect->options['use_login_param']) ? 'checked' : ''; ?>>
                        <label for="id_use_login_param" class="custom-control-label font-weight-bold">
                            <?php echo get_string('useloginparam', 'tool_redirectplus'); ?>
                        </label>
                    </div>
                    <small class="form-text text-muted"><?php echo get_string('useloginparam_help', 'tool_redirectplus'); ?></small>
                </div>
                
                <div id="login_param_section" style="display: none;">
                    <div class="form-group ml-4">
                        <label for="id_loggedin_url"><?php echo get_string('loggedin_url', 'tool_redirectplus'); ?></label>
                        <input type="url" name="loggedin_url" id="id_loggedin_url" 
                               value="<?php echo s($redirect->options['loggedin_url'] ?? ''); ?>" 
                               class="form-control" placeholder="https://example.com/member-page">
                    </div>
                    
                    <div class="form-group ml-4">
                        <label for="id_loggedout_url"><?php echo get_string('loggedout_url', 'tool_redirectplus'); ?></label>
                        <input type="url" name="loggedout_url" id="id_loggedout_url" 
                               value="<?php echo s($redirect->options['loggedout_url'] ?? ''); ?>" 
                               class="form-control" placeholder="https://example.com/guest-page">
                    </div>
                </div>
                
                <hr>
                
                <!-- Language Parameter -->
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="use_language_param" id="id_use_language_param" value="1" 
                               class="custom-control-input" <?php echo !empty($redirect->options['use_language_param']) ? 'checked' : ''; ?>>
                        <label for="id_use_language_param" class="custom-control-label font-weight-bold">
                            <?php echo get_string('uselanguageparam', 'tool_redirectplus'); ?>
                        </label>
                    </div>
                    <small class="form-text text-muted"><?php echo get_string('uselanguageparam_help', 'tool_redirectplus'); ?></small>
                </div>
                
                <div id="language_param_section" style="display: none;">
                    <div id="language_rules_container" class="ml-4">
                        <?php
                        $language_rules = $redirect->options['language_rules'] ?? [];
                        if (empty($language_rules)) {
                            $language_rules = [['lang' => '', 'url' => '']];
                        }
                        foreach ($language_rules as $index => $rule) {
                            ?>
                            <div class="language-rule-row form-row mb-2">
                                <div class="col-md-3">
                                    <input type="text" name="language_code[]" value="<?php echo s($rule['lang'] ?? ''); ?>" 
                                           class="form-control" placeholder="<?php echo get_string('languagecode', 'tool_redirectplus'); ?>">
                                    <small class="form-text text-muted"><?php echo get_string('languagecode_help', 'tool_redirectplus'); ?></small>
                                </div>
                                <div class="col-md-8">
                                    <input type="url" name="language_url[]" value="<?php echo s($rule['url'] ?? ''); ?>" 
                                           class="form-control" placeholder="<?php echo get_string('languageurl', 'tool_redirectplus'); ?>">
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-danger btn-sm remove-language-rule" style="display: <?php echo $index > 0 ? 'inline-block' : 'none'; ?>;">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    
                    <div class="ml-4 mb-3">
                        <button type="button" id="add_language_rule" class="btn btn-sm btn-secondary">
                            <i class="fa fa-plus"></i> <?php echo get_string('addlanguagerule', 'tool_redirectplus'); ?>
                        </button>
                    </div>
                    
                    <div class="form-group ml-4">
                        <label for="id_default_language_url"><?php echo get_string('defaultlanguageurl', 'tool_redirectplus'); ?></label>
                        <input type="url" name="default_language_url" id="id_default_language_url" 
                               value="<?php echo s($redirect->options['default_language_url'] ?? ''); ?>" 
                               class="form-control" placeholder="https://example.com/default-page">
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Actions -->
    <div class="form-group">
        <button type="submit" class="btn btn-primary"><?php echo get_string('saveredirect', 'tool_redirectplus'); ?></button>
        <a href="<?php echo $baseurl->out(false); ?>" class="btn btn-secondary"><?php echo get_string('cancel'); ?></a>
    </div>
</form>

<script>
// Toggle redirect type sections
document.getElementById('id_redirect_type').addEventListener('change', function() {
    var simpleSection = document.getElementById('simple_redirect_section');
    var conditionalSection = document.getElementById('conditional_redirect_section');
    
    if (this.value === 'simple') {
        simpleSection.style.display = 'block';
        conditionalSection.style.display = 'none';
    } else {
        simpleSection.style.display = 'none';
        conditionalSection.style.display = 'block';
    }
});

// Toggle login parameter section
document.getElementById('id_use_login_param').addEventListener('change', function() {
    document.getElementById('login_param_section').style.display = this.checked ? 'block' : 'none';
});

// Toggle language parameter section
document.getElementById('id_use_language_param').addEventListener('change', function() {
    document.getElementById('language_param_section').style.display = this.checked ? 'block' : 'none';
});

// Add language rule
document.getElementById('add_language_rule').addEventListener('click', function() {
    var container = document.getElementById('language_rules_container');
    var newRow = document.createElement('div');
    newRow.className = 'language-rule-row form-row mb-2';
    newRow.innerHTML = `
        <div class="col-md-3">
            <input type="text" name="language_code[]" class="form-control" placeholder="<?php echo get_string('languagecode', 'tool_redirectplus'); ?>">
            <small class="form-text text-muted"><?php echo get_string('languagecode_help', 'tool_redirectplus'); ?></small>
        </div>
        <div class="col-md-8">
            <input type="url" name="language_url[]" class="form-control" placeholder="<?php echo get_string('languageurl', 'tool_redirectplus'); ?>">
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-danger btn-sm remove-language-rule">
                <i class="fa fa-times"></i>
            </button>
        </div>
    `;
    container.appendChild(newRow);
    
    // Attach remove handler
    newRow.querySelector('.remove-language-rule').addEventListener('click', function() {
        newRow.remove();
    });
});

// Remove language rule
document.querySelectorAll('.remove-language-rule').forEach(function(btn) {
    btn.addEventListener('click', function() {
        btn.closest('.language-rule-row').remove();
    });
});

// Initialize display on page load
document.addEventListener('DOMContentLoaded', function() {
    // Trigger redirect type change to show correct section
    var redirectTypeSelect = document.getElementById('id_redirect_type');
    var event = new Event('change');
    redirectTypeSelect.dispatchEvent(event);
    
    // Trigger login param change
    var loginCheckbox = document.getElementById('id_use_login_param');
    if (loginCheckbox.checked) {
        document.getElementById('login_param_section').style.display = 'block';
    }
    
    // Trigger language param change
    var languageCheckbox = document.getElementById('id_use_language_param');
    if (languageCheckbox.checked) {
        document.getElementById('language_param_section').style.display = 'block';
    }
});
</script>

<?php
echo $OUTPUT->footer();
