# Changelog

All notable changes to the Redirect Plus plugin will be documented in this file.

## [1.5.0] - 2025-11-03

### Major Feature - Custom URL Redirects with Conditional Parameters

**NEW: Redirect ANY page based on user conditions!**

This release adds powerful custom redirect functionality that works for BOTH existing pages and 404 errors.

### Features Added

1. **Custom URL Redirects**
   - Create redirects for any URL (homepage `/`, `/faq/`, or any path)
   - Works for existing pages AND 404 errors
   - New "Custom Redirects" tab in admin interface
   - Enable/disable individual redirects
   - Full management UI (add, edit, delete)

2. **Conditional Parameters**
   - **Login Status**: Different URLs for logged-in users vs. guests
   - **User Language**: Redirect based on browser language or Moodle preference
   - **Combined Conditions**: Use both login and language together
   - Parameters stored as flexible JSON for future expansion

3. **Administrator Bypass**
   - New setting: "Disable redirect for administrators" (enabled by default)
   - Admins bypass all custom redirects when signed in
   - Perfect for testing and troubleshooting

### Technical Implementation

- **Proper Moodle Integration**: Uses `tool_redirectplus_after_config()` callback in lib.php
- **Works with All PHP Setups**: PHP-FPM, FastCGI, mod_php - no special configuration needed
- **Early Interception**: Redirects happen before page rendering
- **Database Schema**: New `tool_redirectplus_redirects` table with JSON options field
- **Helper Functions**: Complete API for redirect matching and evaluation

### Example Use Cases

- Homepage language redirect: Send Spanish speakers to `/es/`, French to `/fr/`
- Member area: Redirect logged-out users to login page
- FAQ for guests: Different FAQ pages for members vs. visitors
- Legacy URL redirects: Old pages to new locations with conditions

### Database Changes

- New table: `tool_redirectplus_redirects`
- Fields: source_url, redirect_options (JSON), enabled, timestamps
- Indexes on source_url and enabled for performance

### Files Added

- `edit_redirect.php` - Add/edit redirect form with JavaScript UI
- `templates/redirects_tab.mustache` - Redirects management interface
- Extensive language strings for all new features

### Upgrade Notes

- Database upgrade automatic on plugin update
- `disable_redirect_admin` config set to 1 (enabled) by default
- All existing functionality preserved
- No action required - works immediately after upgrade

### Removed

- Old backup files (`index_backup.php`, `index_old.php`)
- Development zip files
- Unused hook callback approach (in favor of simpler after_config callback)

## [1.3.0] - 2025-10-31

### Changed - Enhanced Settings Interface
- **Behavior Dropdown**: Settings page now uses a dropdown to select between "Show custom message" or "Redirect to another page"
- **Cleaner Interface**: Removed blue info box and redundant descriptive text
- **Dynamic Display**: Only the relevant field (message editor or URL input) is shown based on selected behavior
- **Better UX**: Simplified workflow makes it clearer how to configure 404 handling

### Improved
- Settings form now saves behavior preference in config
- JavaScript toggles between message and redirect sections dynamically
- More intuitive for administrators to configure

### Technical
- **AMD Modules**: JavaScript moved to proper AMD module structure (amd/src and amd/build)
- **Gruntfile**: Added Gruntfile.js for automated minification with grunt
- **Build Process**: Use `npm install` then `grunt amd` to rebuild JavaScript from source
- Package script updated to exclude node_modules and development files

## [1.2.0] - 2025-10-30

### CRITICAL FIX - URL Tracking Now Works!

**Problem:** URL tracking was failing with "Unknown URL" even with correct server configuration.

**Solution:** Changed approach to pass URL as query parameter instead of relying on server variables.

### Changed - BREAKING (Requires Configuration Update)
- **Apache Configuration**: Now uses `ErrorDocument 404 /admin/tool/redirectplus/error404.php?url=%{REQUEST_URI}`
- **Nginx Configuration**: Now uses `error_page 404 /admin/tool/redirectplus/error404.php?url=$request_uri;`
- **Simpler & More Reliable**: URL is passed directly as a parameter, works on all server configurations

### What You Need to Do:
1. **Update your .htaccess or Nginx config** with the NEW configuration shown in the Setup Instructions tab
2. The old configuration will show "Unknown URL" - you MUST update to the new format
3. Test by visiting a non-existent page - it should now log the correct URL

### Technical Details:
- URL now passed via `?url=` query parameter
- Falls back to server variables if parameter not present
- More reliable across different server configurations and hosting environments
- Works with Apache, Nginx, Plesk, and other control panels

## [1.1.3] - 2025-10-30

### Fixed
- **Accordion Dropdowns**: Fixed collapsible sections in Setup Instructions tab (Apache/Nginx/Plesk)
- Now uses Moodle's standard Bootstrap collapse functionality with jQuery
- Added visual indicators (▶/▼) that toggle when sections expand/collapse
- Apache section is open by default, others are collapsed

### Improved
- **Custom Message Editor**: Significantly increased textarea height (400px minimum, 20 rows)
- Much better editing experience for longer custom messages
- TinyMCE editor has more vertical space

## [1.1.2] - 2025-10-30

### Fixed
- **URL Detection Improved**: Added multiple fallback methods to detect the original 404 URL
- Now checks: REDIRECT_URL, REDIRECT_REQUEST_URI, REQUEST_URI, REDIRECT_SCRIPT_URL, ORIGINAL_REQUEST_URI
- Better handling when server variables are not available
- Fixed settings form validation - redirect URL is now optional and not required

### Added
- **TinyMCE Editor**: Custom message field now uses Moodle's built-in HTML editor
- **Enhanced Apache Instructions**: Added SetEnvIf directive to ensure URL is properly passed
- **Troubleshooting Section**: Added helpful troubleshooting info in Setup tab
- Better validation for redirect URL (validates format when provided)

### Changed
- Changed redirect URL input from type="url" to type="text" to avoid browser validation issues
- Improved error messages and help text
- Apache configuration now includes two lines for better URL detection

## [1.1.1] - 2025-10-30

### Fixed
- **Critical Bug**: Fixed URL recording - now correctly logs the actual 404 URL instead of the error page itself
- Error page now properly reads REDIRECT_URL server variable set by Apache/Nginx
- Added fallback URL detection methods for various server configurations

### Changed
- **Simplified Settings**: Removed confusing enable checkbox - tracking is always enabled
- **Clearer Settings Logic**: Custom message is now the default behavior with redirect as an optional alternative
- Redirect URL now takes priority over custom message when both are set
- Improved settings page layout and descriptions
- Added helpful notes in setup instructions about how URLs are passed by servers

### Improved
- Better user experience in settings page
- Clearer language strings explaining the default behavior
- More informative setup instructions with server-specific notes

## [1.1.0] - 2025-10-30

### Major Changes
- **New Architecture**: Changed from automatic callback to server-configured custom 404 page
- **Unified Interface**: Combined report and settings into single page with tabbed navigation
- **Better Admin Placement**: Moved from Tools to General section in Site Administration

### Added
- Custom 404 error page (`error404.php`) that logs errors before displaying content
- Settings tab with configuration options:
  - Enable/disable 404 tracking
  - Redirect to custom URL option
  - Custom HTML message option
- Setup Instructions tab with:
  - Server-specific configuration instructions (Apache, Nginx, Plesk)
  - Test button to verify 404 tracking is working
  - Copy-ready configuration snippets
- Tabbed interface for better organization
- Comprehensive server configuration documentation

### Changed
- Removed `tool_redirectplus_after_http_headers()` callback function
- Main page now accessible from Site administration > Redirect Plus (General section)
- Updated all documentation to reflect new setup process

### Technical Changes
- Plugin now requires server-level 404 error page configuration
- More reliable 404 detection (server-level vs PHP-level)
- Improved user experience with custom messages or redirects
- Better frankenstyle naming consistency

## [1.0.0] - 2025-10-30

### Added
- Initial release of Redirect Plus
- Automatic 404 error logging functionality
- Comprehensive 404 error report page with pagination
- Database table `tool_redirectplus_404` for storing error records
- Delete individual error records functionality
- Delete all error records functionality
- Privacy API implementation for GDPR compliance
- Logger class for managing 404 error records
- Full frankenstyle naming convention compliance
- Support for Moodle 4.3, 4.4, 4.5, 5.0, and 5.1
- User-friendly admin interface in Tools menu
- IP address tracking (IPv4 and IPv6 support)
- User agent tracking
- Referrer URL tracking
- User association with 404 errors
- Timestamp recording
- Comprehensive documentation (README.md and INSTALL.md)
- Custom CSS styling for report page
- Language strings (English)
- Installation and upgrade scripts

### Features
- Zero configuration required - works immediately after installation
- Automatic detection of 404 HTTP responses
- Callback function `tool_redirectplus_after_http_headers()` for error capture
- Admin-only access (requires `moodle/site:config` capability)
- Responsive table design
- Pagination support (50 records per page by default)
- Database indexes for optimal performance
- Foreign key relationship to user table
- Error handling with silent failure to avoid breaking pages
- Debugging support for troubleshooting

### Technical Details
- All variables and functions use `tool_redirectplus_` prefix
- Implements `\core_privacy\local\metadata\provider`
- Implements `\core_privacy\local\request\core_userlist_provider`
- Implements `\core_privacy\local\request\plugin\provider`
- Database table indexes on `timecreated` and `userid` fields
- Supports up to 45 characters for IP addresses (IPv6 compatible)
- Text fields for URL, referrer, and user agent (no length limits)

### Security
- Admin capability check on report page
- Session key validation for delete actions
- Confirmation dialogs for destructive operations
- Escaped output to prevent XSS attacks
- Foreign key constraint to user table

### Compatibility
- Minimum Moodle version: 4.3 (2023100900)
- Tested with Moodle 4.3, 4.4, 4.5, 5.0, and 5.1
- Minimum PHP version: 7.4
- Database support: MySQL, MariaDB, PostgreSQL (all Moodle-supported databases)

---

## Future Enhancements (Planned)

Potential features for future versions:
- Export 404 errors to CSV
- Filter/search functionality on report page
- Custom retention policies for old records
- Email notifications for repeated 404 errors
- Statistics dashboard with charts
- Automatic redirect suggestions
- Integration with course restoration
- API for external access
- Bulk redirect management
- Pattern matching for similar URLs

---

## Version Numbering

This plugin follows [Semantic Versioning](https://semver.org/):
- MAJOR version for incompatible API changes
- MINOR version for backwards-compatible functionality additions
- PATCH version for backwards-compatible bug fixes

## Support

For bug reports, feature requests, or questions:
- Email: support@gwizit.com
- Copyright: 2025 G Wiz IT Solutions

## License

GNU General Public License v3.0 or later
See LICENSE.md for full license text
