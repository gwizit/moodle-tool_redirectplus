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
    if (!empty($options['use_language_param']) && !empty($userlang)) {
        // Normalize language code (take first 2 characters).
        $userlang = strtolower(substr($userlang, 0, 2));
        
        if (!empty($options['language_rules']) && is_array($options['language_rules'])) {
            foreach ($options['language_rules'] as $rule) {
                if (isset($rule['lang']) && strtolower($rule['lang']) === $userlang) {
                    $destinationurl = $rule['url'];
                    break;
                }
            }
            
            // If no match found, use default language URL if set.
            if (!$destinationurl && !empty($options['default_language_url'])) {
                $destinationurl = $options['default_language_url'];
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
    $disableredirectadmin = get_config('tool_redirectplus', 'disable_redirect_admin');
    
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
 * Get the user's preferred language from browser headers.
 *
 * @return string The language code (e.g., 'en', 'es', 'fr')
 */
function tool_redirectplus_get_user_language() {
    // First try to get from Moodle user preference if logged in.
    global $USER;
    
    if (isloggedin() && !isguestuser() && !empty($USER->lang)) {
        return $USER->lang;
    }
    
    // Otherwise get from browser Accept-Language header.
    if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        $langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        if (!empty($langs[0])) {
            // Extract the language code (first 2 characters before any dash or semicolon).
            $lang = strtok($langs[0], '-;');
            return strtolower(trim($lang));
        }
    }
    
    return 'en'; // Default fallback.
}
