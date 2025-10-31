# Redirect Plus - Installation Guide

## Quick Start

This guide will help you install the Redirect Plus plugin on your Moodle site in just a few minutes.

## Prerequisites

Before you begin, ensure you have:
- Moodle 4.3 or later (tested with 4.3, 4.4, 4.5, 5.0, and 5.1)
- Administrator access to your Moodle site
- PHP 7.4 or later

## Installation Steps

### Option 1: Via Moodle Interface (Recommended)

1. **Download the Plugin**
   - Obtain the plugin ZIP file or download it from the Moodle plugins directory

2. **Navigate to Plugin Installer**
   - Log in to your Moodle site as an administrator
   - Go to: `Site administration > Plugins > Install plugins`

3. **Upload the Plugin**
   - Click "Choose a file" or drag and drop the ZIP file
   - Click "Install plugin from the ZIP file"

4. **Validate and Install**
   - Review the validation report
   - Click "Continue" to proceed with installation
   - The plugin will create the necessary database table automatically

5. **Complete Installation**
   - Click "Upgrade Moodle database now"
   - You're done! The plugin is now active and logging 404 errors

### Option 2: Manual Installation

1. **Extract the Plugin**
   - Unzip the plugin file on your computer

2. **Upload to Moodle**
   - Using FTP, SFTP, or file manager, upload the `redirectplus` folder to:
     ```
     {moodle-root}/admin/tool/redirectplus
     ```
   - Ensure the folder structure is correct:
     ```
     admin/tool/redirectplus/
         classes/
         db/
         lang/
         index.php
         lib.php
         settings.php
         version.php
         ...
     ```

3. **Run the Installer**
   - Log in to your Moodle site as an administrator
   - Go to: `Site administration > Notifications`
   - Moodle will detect the new plugin
   - Click "Upgrade Moodle database now"

4. **Confirm Installation**
   - The database table `mdl_tool_redirectplus_404` will be created
   - Installation complete!

### Option 3: Command Line Installation

For advanced users with command-line access:

1. **Upload the Plugin**
   - Place the plugin folder in `{moodle-root}/admin/tool/redirectplus`

2. **Run Upgrade Script**
   ```bash
   cd {moodle-root}
   php admin/cli/upgrade.php
   ```

3. **Confirm When Prompted**
   - Follow the on-screen instructions
   - The database will be updated automatically

## Post-Installation

### IMPORTANT: Server Configuration Required

After installation, you **MUST** configure your web server to use the plugin's custom 404 page.

### Step-by-Step Setup:

1. **Verify Plugin Installation**
   - Go to: `Site administration > Plugins > Plugins overview`
   - Find "Redirect Plus" under "Admin tools"
   - Status should show as "Enabled"

2. **Access the Plugin**
   - Go to: `Site administration > Redirect Plus` (under General section)
   - You should see three tabs: Error Report, Settings, Setup Instructions

3. **Configure Your Web Server**
   - Click on the "Setup Instructions" tab
   - Copy your custom 404 URL (displayed on the page)
   - Follow the instructions for your server type:

   **For Apache (.htaccess):**
   ```apache
   ErrorDocument 404 /admin/tool/redirectplus/error404.php
   ```
   Add this to your `.htaccess` file in Moodle root directory.

   **For Nginx:**
   ```nginx
   error_page 404 /admin/tool/redirectplus/error404.php;
   ```
   Add this to your Nginx server configuration block.

   **For Plesk:**
   - Log in to Plesk
   - Go to Websites & Domains > your domain > Apache & nginx Settings
   - Add to "Additional directives": `ErrorDocument 404 /admin/tool/redirectplus/error404.php`

4. **Test the Setup**
   - In the Setup Instructions tab, click "Test 404 Page" button
   - This opens the error page in a new tab
   - Return to the Error Report tab
   - You should see the test entry logged

5. **Configure Behavior (Optional)**
   - Go to the Settings tab
   - Choose to either:
     - Redirect users to another page, OR
     - Display a custom HTML message
   - Save your settings

### Features:
- ✅ Server-level 404 error detection
- ✅ All variables use frankenstyle naming (`tool_redirectplus_`)
- ✅ Works with Moodle 4.3 through 5.1
- ✅ Privacy API implemented for GDPR compliance
- ✅ Customizable error messages or redirects

## Accessing the Report

Once installed, you can access the 404 error report:

1. Log in as an administrator
2. Navigate to: `Site administration > Tools > Redirect Plus`
3. View all recorded 404 errors with full details

## Permissions

Only users with the `moodle/site:config` capability (typically site administrators) can:
- View the 404 error report
- Delete individual error records
- Delete all error records

## Database Table

The plugin creates one database table:

**Table Name:** `mdl_tool_redirectplus_404` (prefix may vary)

**Structure:**
- `id` - Primary key
- `url` - The URL that generated the 404 error
- `referrer` - Where the user came from
- `userid` - Which user encountered the error
- `timecreated` - When the error occurred
- `ip` - User's IP address
- `useragent` - Browser/device information

## Troubleshooting

### Plugin Not Appearing in Admin Menu

**Problem:** Can't find "Redirect Plus" in the Tools menu

**Solution:**
1. Clear all caches: `Site administration > Development > Purge all caches`
2. Log out and log back in
3. Check that you have the `moodle/site:config` capability

### 404 Errors Not Being Logged

**Problem:** The report page is empty even after visiting non-existent pages

**Solution:**
1. Verify the database table exists:
   - Check your database for the table `mdl_tool_redirectplus_404`
2. Enable debugging:
   - Go to: `Site administration > Development > Debugging`
   - Set "Debug messages" to "DEVELOPER"
   - Check for any error messages
3. Check file permissions:
   - Ensure the plugin files have correct permissions (readable by web server)
4. Verify the callback is being called:
   - Check that `lib.php` exists and contains `tool_redirectplus_after_http_headers()`

### Database Table Not Created

**Problem:** Installation completed but table wasn't created

**Solution:**
1. Check the database manually for the table
2. Try reinstalling:
   - Go to: `Site administration > Plugins > Plugins overview`
   - Find "Redirect Plus"
   - Click "Uninstall" and confirm
   - Reinstall using the steps above
3. Run the upgrade script manually:
   ```bash
   php admin/cli/upgrade.php
   ```

### Permission Denied When Accessing Report

**Problem:** Error message when trying to access the report page

**Solution:**
- Ensure you're logged in as an administrator
- Verify you have the `moodle/site:config` capability
- Check that the plugin is properly installed and enabled

## Upgrading

To upgrade to a newer version:

1. **Backup First** (Important!)
   - Backup your Moodle database
   - Backup the plugin folder

2. **Replace Plugin Files**
   - Delete the old `admin/tool/redirectplus` folder
   - Upload the new version

3. **Run the Upgrade**
   - Go to: `Site administration > Notifications`
   - Follow the upgrade prompts

The database table and all existing data will be preserved.

## Uninstalling

To remove the plugin:

1. **Export Data (Optional)**
   - If you want to keep the 404 error records, export them first from the report page

2. **Uninstall via Interface**
   - Go to: `Site administration > Plugins > Plugins overview`
   - Find "Redirect Plus" under "Admin tools"
   - Click "Uninstall"
   - Confirm the uninstallation

3. **Database Cleanup**
   - The database table will be automatically removed
   - All 404 error records will be deleted

4. **Remove Files**
   - Delete the `admin/tool/redirectplus` folder if still present

## Support

If you encounter any issues during installation:

- Email: support@gwizit.com
- Check the README.md file for additional information
- Enable debugging to see detailed error messages

## Summary

The Redirect Plus plugin is designed for easy installation with zero configuration required. After installation:

✅ Works automatically - no setup needed
✅ All function names use frankenstyle format
✅ Compatible with Moodle 4.3, 4.4, 4.5, 5.0, and 5.1
✅ Privacy API compliant (GDPR)
✅ Automatically logs all 404 errors
✅ Easy-to-use admin interface

Start identifying and fixing broken links on your Moodle site today!
