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
 * Library functions for tool_redirectplus.
 *
 * @package     tool_redirectplus
 * @copyright   2025 G Wiz IT Solutions <support@gwizit.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Get plugin configuration with caching.
 *
 * @return stdClass Object containing all plugin settings
 */
function tool_redirectplus_get_config() {
    $cache = cache::make('tool_redirectplus', 'pluginconfig');
    $config = $cache->get('settings');
    
    if ($config === false) {
        // Cache miss - load from database.
        $config = new stdClass();
        $config->behavior = get_config('tool_redirectplus', 'behavior') ?: 'message';
        $config->redirect_url = get_config('tool_redirectplus', 'redirect_url');
        $config->custom_message = get_config('tool_redirectplus', 'custom_message');
        $config->custom_message_format = get_config('tool_redirectplus', 'custom_message_format') ?: FORMAT_HTML;
        $config->disable_redirect_admin = get_config('tool_redirectplus', 'disable_redirect_admin');
        if ($config->disable_redirect_admin === false) {
            $config->disable_redirect_admin = 1; // Default to enabled.
        }
        $config->enable_404_logging = get_config('tool_redirectplus', 'enable_404_logging');
        if ($config->enable_404_logging === false) {
            $config->enable_404_logging = 1; // Default to enabled.
        }
        $config->max_404_records = get_config('tool_redirectplus', 'max_404_records') ?: 1000;
        
        $cache->set('settings', $config);
    }
    
    return $config;
}

/**
 * Find a matching custom redirect for the given URL.
 *
 * @param string $url The URL to match (without domain)
 * @return object|false The redirect record if found, false otherwise
 */
function tool_redirectplus_find_redirect($url) {
    global $DB;

    // Remove query string if present for matching.
    $cleanurl = strtok($url, '?');
    
    // Try to find an exact match first.
    $redirects = $DB->get_records('tool_redirectplus_redirects', ['enabled' => 1]);
    
    foreach ($redirects as $redirect) {
        $sourceurl = strtok($redirect->source_url, '?');
        
        // Exact match.
        if ($cleanurl === $sourceurl) {
            return $redirect;
        }
        
        // Pattern match with wildcards.
        $pattern = str_replace(['*', '/'], ['.*', '\/'], $sourceurl);
        if (preg_match('/^' . $pattern . '$/', $cleanurl)) {
            return $redirect;
        }
    }
    
    return false;
}

/**
 * Evaluate redirect options and determine the appropriate destination URL.
 *
 * @param object $redirect The redirect record from database
 * @param bool $isloggedin Whether the user is logged in
 * @param string $userlang The user's language code
 * @return string|false The destination URL or false if no match
 */
function tool_redirectplus_evaluate_redirect($redirect, $isloggedin, $userlang) {
    // Parse the redirect options JSON.
    $options = json_decode($redirect->redirect_options, true);
    
    if (!$options || !is_array($options)) {
        return false;
    }
    
    // Check if this is a simple redirect or conditional.
    if (isset($options['type']) && $options['type'] === 'simple') {
        return $options['destination_url'] ?? false;
    }
    
    // Conditional redirect - evaluate parameters.
    $destinationurl = false;
    
    // First check login status parameter if enabled.
    if (!empty($options['use_login_param'])) {
        if ($isloggedin && !empty($options['loggedin_url'])) {
            $destinationurl = $options['loggedin_url'];
        } else if (!$isloggedin && !empty($options['loggedout_url'])) {
            $destinationurl = $options['loggedout_url'];
        }
    }
    
    // Then check language parameter if enabled (can override login-based URL).
    if (!empty($options['use_language_param'])) {
        // Get language detection methods, default to browser only for backward compatibility
        $detection_methods = $options['language_detection_methods'] ?? ['browser' => true, 'moodle' => false];
        
        // Get user language using configured detection methods
        $userlang = tool_redirectplus_get_user_language($detection_methods);
        
        if (!empty($userlang)) {
            $userlang = strtolower($userlang);
            
            if (!empty($options['language_rules']) && is_array($options['language_rules'])) {
                // Process rules in order - stop at first match.
                foreach ($options['language_rules'] as $rule) {
                    if (isset($rule['lang']) && !empty($rule['lang'])) {
                        if (tool_redirectplus_match_language($rule['lang'], $userlang)) {
                            $destinationurl = $rule['url'];
                            break; // Stop at first match.
                        }
                    }
                }
                
                // If no match found, use default language URL if set.
                if (!$destinationurl && !empty($options['default_language_url'])) {
                    $destinationurl = $options['default_language_url'];
                }
            }
        }
    }
    
    // Fallback to simple destination URL if nothing matched.
    if (!$destinationurl && !empty($options['destination_url'])) {
        $destinationurl = $options['destination_url'];
    }
    
    return $destinationurl;
}

/**
 * Check if the current user should bypass redirects (administrator check).
 *
 * @return bool True if redirects should be bypassed for this user
 */
function tool_redirectplus_should_bypass_redirect() {
    global $USER;
    
    // Check if the admin bypass setting is enabled.
    $config = tool_redirectplus_get_config();
    $disableredirectadmin = $config->disable_redirect_admin;
    
    if (!$disableredirectadmin) {
        return false; // Setting is disabled, don't bypass.
    }
    
    // Check if user is logged in and is an admin.
    if (isloggedin() && !isguestuser()) {
        return is_siteadmin();
    }
    
    return false;
}

/**
 * Get the user's language code for redirect evaluation.
 *
 * @param array $detection_methods Array with 'browser' and/or 'moodle' keys set to true
 * @return string The user's language code (e.g., 'en', 'en-us', 'pt-br')
 */
function tool_redirectplus_get_user_language($detection_methods = ['browser' => true, 'moodle' => false]) {
    global $USER;
    
    // Ensure we have valid detection methods
    if (empty($detection_methods)) {
        $detection_methods = ['browser' => true, 'moodle' => false];
    }
    
    $detected_lang = '';
    
    // Try Moodle user language first if enabled
    if (!empty($detection_methods['moodle'])) {
        // Use current_language() which gets the active language for the current page/user
        $moodle_lang = current_language();
        if (!empty($moodle_lang)) {
            $detected_lang = strtolower($moodle_lang);
            // If only Moodle detection is enabled, return immediately
            if (empty($detection_methods['browser'])) {
                return $detected_lang;
            }
        }
    }
    
    // Try browser language if enabled
    if (!empty($detection_methods['browser'])) {
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            if (!empty($langs[0])) {
                // Extract the full language code (before any semicolon).
                $browser_lang = trim(strtok($langs[0], ';'));
                $detected_lang = strtolower($browser_lang);
            }
        }
    }
    
    // If we got a Moodle lang but browser detection is also enabled, prefer browser
    // (since browser was checked last in the logic above)
    
    return $detected_lang ?: 'en'; // Default fallback.
}

/**
 * Match a language code against a pattern with wildcard support.
 *
 * @param string $pattern The pattern to match (e.g., 'en', 'en-us', 'pt*', 'en-*')
 * @param string $language The language code to test (e.g., 'en-us', 'pt-br')
 * @return bool True if the language matches the pattern
 */
function tool_redirectplus_match_language($pattern, $language) {
    $pattern = strtolower(trim($pattern));
    $language = strtolower(trim($language));
    
    // Exact match.
    if ($pattern === $language) {
        return true;
    }
    
    // Wildcard matching.
    if (strpos($pattern, '*') !== false) {
        // Convert wildcard pattern to regex.
        $regex = '/^' . str_replace(['\*'], ['.*'], preg_quote($pattern, '/')) . '$/';
        return preg_match($regex, $language) === 1;
    }
    
    return false;
}

/**
 * Prune old 404 error records to maintain the maximum limit.
 * Deletes the oldest records when the count exceeds the configured maximum.
 *
 * @return void
 */
function tool_redirectplus_prune_404_records() {
    global $DB;
    
    $config = tool_redirectplus_get_config();
    $max_records = $config->max_404_records;
    $current_count = $DB->count_records('tool_redirectplus_404');
    
    if ($current_count > $max_records) {
        $to_delete = $current_count - $max_records;
        $old_records = $DB->get_records('tool_redirectplus_404', null, 'timecreated ASC', 'id', 0, $to_delete);
        foreach ($old_records as $old_record) {
            $DB->delete_records('tool_redirectplus_404', ['id' => $old_record->id]);
        }
    }
}
