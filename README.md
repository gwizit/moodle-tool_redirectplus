# Redirect Plus #

A powerful Moodle admin tool plugin for managing custom redirects and 404 error handling.

---

## 💝 Support Development ##

**If you find Redirect Plus useful, please consider supporting its continued development!**

While not required, donations help keep this plugin free, up-to-date, and feature-rich. Every little bit helps! 🙏

#### 💳 Donate via Square
**Quick and easy - any amount appreciated!**

[**👉 Click Here to Donate**](https://square.link/u/C0Xn8NZw)

**Your support makes a difference!** It helps me dedicate more time to:
- Adding new features
- Fixing bugs promptly
- Maintaining compatibility with new Moodle versions
- Providing support to users
- Creating documentation and tutorials

Thank you for using Redirect Plus! 🎉

---

## Description ##

Redirect Plus is a comprehensive solution that gives you complete control over your Moodle site's redirects and 404 error handling. Create custom redirects with optional conditional logic based on user login status or language preference. The plugin also records 404 errors and allows you to customize how they are handled - either display a custom message or redirect users to a specific page.

### Key Capabilities:

**Custom Redirects:**
- Create redirects for any URL (existing pages or 404 errors)
- Add conditional logic based on user login status (logged in vs. logged out)
- Configure language-based redirects for multilingual sites
- Redirect existing pages like homepage, /faq/, or any other page
- Enable/disable individual redirects without deleting them

**404 Error Management:**
The plugin captures comprehensive information about each 404 error including:

- The URL that generated the 404 error
- The referrer (where the user came from)
- User information (who encountered the error)
- IP address
- User agent (browser/device information)
- Timestamp

This information is invaluable for:
- Identifying and fixing broken links
- Improving site navigation
- Understanding user behavior
- Maintaining a better user experience

## Features ##

- **Automatic 404 Error Logging**: No configuration needed - works automatically after installation
- **Custom URL Redirects**: Create conditional redirects for ANY page (existing or 404)
- **Conditional Parameters**: Redirect based on login status and user language
- **Redirect Existing Pages**: Redirect homepage, /faq/, or any existing page based on conditions
- **Administrator Bypass**: Disable redirects for administrators (useful for testing)
- **Comprehensive Error Report**: View all 404 errors in an easy-to-read table format
- **User-Friendly Interface**: Access the report from Site administration > Tools > Redirect Plus
- **Record Management**: Delete individual records or clear all records at once
- **Pagination Support**: Easily browse through large numbers of errors
- **Full User Context**: See which users encountered errors and when
- **Compatible with Moodle 4.3, 4.4, 4.5, 5.0, and 5.1**

## Requirements ##

- Moodle 4.3 or later (compatible with 4.3, 4.4, 4.5, 5.0, and 5.1)
- PHP 7.4 or later

## Installation ##

### Method 1: Installing via uploaded ZIP file ###

1. Download or create a ZIP file of the plugin
2. Log in to your Moodle site as an admin and go to **Site administration > Plugins > Install plugins**
3. Upload the ZIP file with the plugin code
4. Check the plugin validation report and finish the installation
5. The database table `tool_redirectplus_404` will be created automatically

### Method 2: Installing manually ###

1. Extract the plugin files
2. Copy the `redirectplus` folder to `{your/moodle/dirroot}/admin/tool/redirectplus`
3. Log in to your Moodle site as an admin and go to **Site administration > Notifications**
4. Follow the on-screen instructions to complete the installation

### Method 3: Installing via command line ###

1. Copy the plugin files to `{your/moodle/dirroot}/admin/tool/redirectplus`
2. Run the upgrade script:
   ```bash
   php admin/cli/upgrade.php
   ```

## Setup ##

**Important:** After installation, you must configure your web server to enable 404 error logging.

### 🔴 Server Configuration Requirement

**HIGHLY RECOMMENDED:** Access to your Apache or Nginx server configuration files is strongly preferred for optimal performance and reliability. The `.htaccess` method should only be used as a last resort fallback.

**Why server configuration is preferred:**
- **Reliability**: Works consistently across all PHP configurations (PHP-FPM, FastCGI, mod_php)
- **Performance**: More efficient than `.htaccess` file-based configuration
- **Compatibility**: The `.htaccess` auto-prepend method does NOT work with PHP-FPM or FastCGI (most modern hosting)
- **Stability**: Avoids potential 500 Internal Server Errors
- **Best Practice**: Follows Moodle and industry security standards

### Quick Setup Steps:

1. **Install the plugin** (see Installation section above)
2. **Navigate to the plugin:** Site administration > Redirect Plus
3. **Go to the "Setup Instructions" tab**
4. **Copy your custom 404 URL** (shown on the page)
5. **Configure your web server** using Apache or Nginx server configuration (RECOMMENDED)
6. **Test the setup** using the automated test button
7. **Configure behavior** in the Settings tab (optional)

### Detailed Server Configuration:

#### Apache Server Configuration (RECOMMENDED)

Add this line to your Apache server configuration or virtual host file:

```apache
ErrorDocument 404 /admin/tool/redirectplus/error404.php?url=%{REQUEST_URI}
```

**Important:** The `?url=%{REQUEST_URI}` parameter passes the original URL to the error handler.

**Why this method is recommended:**
- Works with all PHP configurations (mod_php, PHP-FPM, FastCGI)
- Best performance and reliability
- Industry standard approach

#### Nginx Server Configuration (RECOMMENDED)

Add this line to your Nginx server configuration block:

```nginx
error_page 404 /admin/tool/redirectplus/error404.php?url=$request_uri;
```

**Important:** The `?url=$request_uri` parameter passes the original URL to the error handler.

**Why this method is recommended:**
- Works with all PHP configurations
- Best performance and reliability
- Industry standard approach

#### .htaccess Method (FALLBACK ONLY - NOT RECOMMENDED)

**⚠️ WARNING:** This method is provided as a last resort fallback only. It often does NOT work on modern hosting environments.

**Known Issues:**
- Does NOT work with PHP-FPM or FastCGI (most modern hosting uses these)
- May cause 500 Internal Server Error
- Requires mod_php and specific Apache settings
- Has performance implications
- Not supported on many hosting providers

**Only use this method if:**
- You cannot access your Apache server configuration
- You have confirmed you are using mod_php (not PHP-FPM or FastCGI)
- You have made a backup of your `.htaccess` file

Add this line to your `.htaccess` file in Moodle root directory:

```apache
ErrorDocument 404 /admin/tool/redirectplus/error404.php?url=%{REQUEST_URI}
```

**If you get a 500 error after adding this line:**
1. Immediately remove the line
2. Restore your `.htaccess` backup
3. Contact your hosting provider to request access to Apache or Nginx server configuration
4. Use the Apache or Nginx server configuration method instead

#### Plesk Control Panel

1. Log in to your Plesk control panel
2. Go to Websites & Domains > your domain > Apache & nginx Settings
3. Scroll down to "Additional directives for HTTP"
4. Add: `ErrorDocument 404 /admin/tool/redirectplus/error404.php?url=%{REQUEST_URI}`
5. Click "Apply" and "OK"

## Usage ##

### Accessing the Plugin ###

1. Log in as an administrator
2. Navigate to **Site administration > Redirect Plus** (under General section)
3. You'll see three tabs: Error Report, Settings, and Setup Instructions

### Error Report Tab ###

View all recorded 404 errors with:
- URL that generated the error
- Referrer (where the user came from)
- User who encountered the error
- IP address
- User agent (browser/device)
- Date and time

**Managing Records:**
- **Delete a single record**: Click the "Delete" button next to any record
- **Delete all records**: Click the "Delete All Records" button
- Pagination: 50 records per page by default

### Settings Tab ###

Configure how 404 errors are handled:

**Choose Behavior:**
- Use the dropdown to select either:
  - **Show custom message** (default): Display a custom HTML message on the error page
  - **Redirect to another page**: Automatically redirect users to a different URL

**Depending on your choice:**
- **Custom Message**: A rich text editor appears where you can create your 404 message with HTML formatting
- **Redirect URL**: A simple URL field appears where you enter the destination page

**Save your settings** when done.

### Custom Redirects Tab ###

Create intelligent redirects with conditional parameters:

**Example Use Cases:**

1. **Homepage Language Redirect:**
   - Source URL: `/`
   - Condition: User language
   - Spanish speakers → `/es/`
   - French speakers → `/fr/`
   - Others → `/en/`

2. **FAQ for Guests:**
   - Source URL: `/faq/`
   - Condition: Login status
   - Logged out → `/guest-faq.html`
   - Logged in → stay on /faq/

3. **Members Area:**
   - Source URL: `/members/`
   - Condition: Login status
   - Logged in → `/dashboard/`
   - Logged out → `/login/`

4. **Old Page Redirect:**
   - Source URL: `/old-page`
   - Simple redirect → `/new-page`

**Redirect Types:**
- **Simple Redirect**: One URL for everyone
- **Conditional Redirect**: Different URLs based on:
  - Login status (logged in vs. logged out)
  - User language (browser or Moodle preference)
  - Both conditions combined

**Important Notes:**
- Works automatically for both existing pages and 404 errors using Moodle's callback system
- No special PHP configuration required (works with PHP-FPM, FastCGI, mod_php, etc.)
- Admin bypass option prevents redirects for administrators when enabled

### Settings Tab ###

Configure global 404 error handling and plugin options:

**Choose Behavior:**
- **Show custom message** (default): Display a custom HTML message on the error page
- **Redirect to another page**: Automatically redirect users to a different URL

**Disable Redirect for Administrators:**
- Checkbox option (enabled by default)
- When enabled, administrators bypass ALL custom redirects when signed in
- Useful for testing and troubleshooting

**Save your settings** when done.

### Setup Instructions Tab ###

Complete server configuration guidance:
- View your custom 404 URL
- **Test button**: Click to verify 404 tracking is working (opens error page in new tab)
- **Server-specific instructions**: Expandable sections for Apache, Nginx, and .htaccess
- Copy/paste ready configuration snippets
- **Note**: Custom redirects work automatically via Moodle's callback system (no special setup needed)

## How It Works ##

The plugin uses a custom 404 error page (`error404.php`) that must be configured at your web server level. When a 404 error occurs:

1. The web server redirects to the custom error page
2. The page logs the error details (URL, referrer, user, IP, user agent, timestamp)
3. Based on your settings, it either:
   - Redirects to another page, OR
   - Displays a custom message

**Important:** You must configure your web server to use the custom 404 page. See the Setup section below.

All function and variable names follow Moodle's frankenstyle naming convention (prefixed with `tool_redirectplus_`).

## Database Schema ##

The plugin creates one table: `tool_redirectplus_404`

| Field | Type | Description |
|-------|------|-------------|
| id | int(10) | Primary key |
| url | text | The URL that generated the 404 error |
| referrer | text | The referrer URL (nullable) |
| userid | int(10) | Foreign key to user table (0 for guests) |
| timecreated | int(10) | Unix timestamp |
| ip | varchar(45) | IP address (supports IPv6) |
| useragent | text | User agent string (nullable) |

## Permissions ##

Access to the 404 Error Report requires the `moodle/site:config` capability (site administrators only).

## Privacy ##

This plugin stores the following user data:
- User ID of who encountered the 404 error
- IP address
- User agent (browser information)

This data is used solely for site administration and debugging purposes. Site administrators should be aware of this when considering privacy policies and GDPR compliance.

## Troubleshooting ##

**404 errors are not being logged:**
1. **Verify server configuration**: Use the Test button in the Setup tab
2. **Check web server config**: Ensure ErrorDocument directive is correctly set
3. **Check file permissions**: Make sure `error404.php` is readable by the web server
4. **Check plugin enabled**: Go to Settings tab and ensure tracking is enabled
5. **Database table exists**: Verify `tool_redirectplus_404` table was created
6. **Enable debugging**: Site administration > Development > Debugging (set to DEVELOPER)
7. **Check logs**: Review web server error logs and Moodle logs

**Cannot access the plugin:**
- Ensure you're logged in as a site administrator
- Verify you have the `moodle/site:config` capability
- Look under Site administration > Redirect Plus (in the General section, not Tools)

**Custom 404 page not showing:**
- Clear your browser cache
- Verify the server configuration is correct
- Check that mod_rewrite (Apache) or similar module is enabled
- Test with a definitely non-existent URL

**Performance concerns with large datasets:**
- The plugin automatically uses pagination (50 records per page)
- Consider periodically deleting old records using the "Delete All Records" feature
- Records are indexed by `timecreated` and `userid` for optimal query performance

## Development ##

### JavaScript Development ###

This plugin uses AMD modules for JavaScript. To modify JavaScript files:

1. **Install Node.js** (https://nodejs.org/)
2. **Install dependencies**: `npm install`
3. **Edit source files** in `amd/src/` directory
4. **Build minified versions**: `grunt amd`

The build process:
- Source files: `amd/src/*.js`
- Minified output: `amd/build/*.min.js`
- Grunt automatically minifies and optimizes the code

To watch for changes and auto-build:
```bash
grunt watch
```

## 💝 Love This Plugin? ##

**Help keep Redirect Plus free and actively maintained!**

If this plugin has saved you time or made your Moodle site better, please consider showing your appreciation:

### 💳 [**Donate via Square**](https://square.link/u/C0Xn8NZw)

Your support helps fund:
- ✨ New features and improvements
- 🐛 Bug fixes and maintenance
- 📚 Documentation and tutorials
- 💬 Community support
- ⚡ Faster response times

**Every contribution matters - thank you!** 🙏

## Support & Contact ##

For issues, questions, or contributions, please contact:
- **Email:** support@gwizit.com
- **Copyright:** 2025 G Wiz IT Solutions
- **Website:** [gwizit.com](https://gwizit.com)

## License ##

Copyright 2025 G Wiz IT Solutions <support@gwizit.com>

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <https://www.gnu.org/licenses/>.
