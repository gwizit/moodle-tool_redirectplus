# Moodle Plugin Packaging Script for Redirect Plus
# This script creates a ZIP file ready for upload to Moodle or the Moodle plugins directory
# The ZIP file name includes the plugin version number from version.php

# Set error action preference
$ErrorActionPreference = "Stop"

Write-Host "==================================" -ForegroundColor Cyan
Write-Host "Redirect Plus - Package Builder" -ForegroundColor Cyan
Write-Host "==================================" -ForegroundColor Cyan
Write-Host ""

# Get the script directory (plugin root)
$tool_redirectplus_scriptdir = $PSScriptRoot
$tool_redirectplus_plugindir = $tool_redirectplus_scriptdir

# Read version.php to extract version number
$tool_redirectplus_versionfile = Join-Path $tool_redirectplus_plugindir "version.php"

if (-not (Test-Path $tool_redirectplus_versionfile)) {
    Write-Host "ERROR: version.php not found!" -ForegroundColor Red
    exit 1
}

Write-Host "Reading version information..." -ForegroundColor Yellow

# Read the version.php file
$tool_redirectplus_versioncontent = Get-Content $tool_redirectplus_versionfile -Raw

# Extract version number using regex
if ($tool_redirectplus_versioncontent -match '\$plugin->version\s*=\s*(\d+);') {
    $tool_redirectplus_version = $Matches[1]
    Write-Host "Found version: $tool_redirectplus_version" -ForegroundColor Green
} else {
    Write-Host "ERROR: Could not extract version number from version.php" -ForegroundColor Red
    exit 1
}

# Extract release version if available
if ($tool_redirectplus_versioncontent -match '\$plugin->release\s*=\s*[''"]([^''"]+)[''"];') {
    $tool_redirectplus_release = $Matches[1]
    Write-Host "Found release: $tool_redirectplus_release" -ForegroundColor Green
} else {
    $tool_redirectplus_release = "unknown"
}

# Plugin name (folder name inside ZIP - just the plugin short name without tool_ prefix)
$tool_redirectplus_pluginname = "redirectplus"

# Create ZIP file name with version (can include full name for clarity)
$tool_redirectplus_zipname = "tool_redirectplus_${tool_redirectplus_version}.zip"
$tool_redirectplus_zippath = Join-Path $tool_redirectplus_scriptdir $tool_redirectplus_zipname

# Check if ZIP already exists
if (Test-Path $tool_redirectplus_zippath) {
    Write-Host ""
    Write-Host "WARNING: ZIP file already exists: $tool_redirectplus_zipname" -ForegroundColor Yellow
    $tool_redirectplus_response = Read-Host "Do you want to overwrite it? (y/n)"
    if ($tool_redirectplus_response -ne 'y' -and $tool_redirectplus_response -ne 'Y') {
        Write-Host "Cancelled by user." -ForegroundColor Yellow
        exit 0
    }
    Remove-Item $tool_redirectplus_zippath -Force
    Write-Host "Existing ZIP removed." -ForegroundColor Green
}

Write-Host ""
Write-Host "Creating package..." -ForegroundColor Yellow

# Define files and directories to exclude from the ZIP
$tool_redirectplus_exclude = @(
    ".git",
    ".gitignore",
    ".gitattributes",
    "*.zip",
    "package.ps1",
    ".vscode",
    ".idea",
    "*.log",
    "Thumbs.db",
    ".DS_Store",
    "node_modules",
    "package.json",
    "package-lock.json",
    "Gruntfile.js",
    "textplus",
    "*.png",
    "*.jpg",
    "*.jpeg",
    "*.gif",
    "*.bmp",
    "*.svg",
    "*.webp",
    "*.ico"
)

# Create the ZIP file
Write-Host "Creating ZIP archive..." -ForegroundColor Yellow

try {
    # Load ZIP assemblies
    Add-Type -Assembly System.IO.Compression
    Add-Type -Assembly System.IO.Compression.FileSystem
    
    # Create ZIP manually to ensure proper structure - Moodle expects: zipfile/redirectplus/version.php
    $tool_redirectplus_zip = [System.IO.Compression.ZipFile]::Open($tool_redirectplus_zippath, 'Create')
    
    # Add all files from plugin directory
    Write-Host "Adding files to ZIP..." -ForegroundColor Yellow
    $tool_redirectplus_files = Get-ChildItem -Path $tool_redirectplus_plugindir -Recurse -File
    $tool_redirectplus_filecount = 0
    
    foreach ($tool_redirectplus_file in $tool_redirectplus_files) {
        # Calculate relative path from plugin parent directory
        $tool_redirectplus_relativepath = $tool_redirectplus_file.FullName.Substring($tool_redirectplus_plugindir.Length + 1).Replace('\', '/')
        
        # Check if file should be excluded
        $tool_redirectplus_shouldexclude = $false
        foreach ($tool_redirectplus_pattern in $tool_redirectplus_exclude) {
            if ($tool_redirectplus_file.Name -like $tool_redirectplus_pattern -or 
                $tool_redirectplus_relativepath -like "*/$tool_redirectplus_pattern/*" -or
                $tool_redirectplus_relativepath -like "*/$tool_redirectplus_pattern" -or
                $tool_redirectplus_relativepath -like "$tool_redirectplus_pattern/*") {
                $tool_redirectplus_shouldexclude = $true
                break
            }
        }
        
        if (-not $tool_redirectplus_shouldexclude) {
            # Add with redirectplus/ prefix
            $tool_redirectplus_zippath_entry = "$tool_redirectplus_pluginname/$tool_redirectplus_relativepath"
            [System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile($tool_redirectplus_zip, $tool_redirectplus_file.FullName, $tool_redirectplus_zippath_entry, [System.IO.Compression.CompressionLevel]::Optimal) | Out-Null
            $tool_redirectplus_filecount++
        }
    }
    
    $tool_redirectplus_zip.Dispose()
    
    Write-Host "Added $tool_redirectplus_filecount files to ZIP" -ForegroundColor Green
    
    Write-Host ""
    Write-Host "==================================" -ForegroundColor Green
    Write-Host "SUCCESS!" -ForegroundColor Green
    Write-Host "==================================" -ForegroundColor Green
    Write-Host ""
    Write-Host "Plugin Details:" -ForegroundColor Cyan
    Write-Host "  Name:    $tool_redirectplus_pluginname" -ForegroundColor White
    Write-Host "  Version: $tool_redirectplus_version" -ForegroundColor White
    Write-Host "  Release: $tool_redirectplus_release" -ForegroundColor White
    Write-Host ""
    Write-Host "ZIP File Created:" -ForegroundColor Cyan
    Write-Host "  $tool_redirectplus_zippath" -ForegroundColor White
    Write-Host ""
    
    # Get file size
    $tool_redirectplus_filesize = (Get-Item $tool_redirectplus_zippath).Length
    $tool_redirectplus_filesizekb = [math]::Round($tool_redirectplus_filesize / 1KB, 2)
    $tool_redirectplus_filesizemb = [math]::Round($tool_redirectplus_filesize / 1MB, 2)
    
    if ($tool_redirectplus_filesizemb -ge 1) {
        Write-Host "  Size: $tool_redirectplus_filesizemb MB" -ForegroundColor White
    } else {
        Write-Host "  Size: $tool_redirectplus_filesizekb KB" -ForegroundColor White
    }
    Write-Host ""
    Write-Host "Ready to upload to:" -ForegroundColor Cyan
    Write-Host "  - Moodle site (Site administration > Plugins > Install plugins)" -ForegroundColor White
    Write-Host "  - Moodle plugins directory (https://moodle.org/plugins/)" -ForegroundColor White
    Write-Host ""
    
} catch {
    Write-Host ""
    Write-Host "ERROR: Failed to create ZIP file" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
    if ($tool_redirectplus_zip) {
        $tool_redirectplus_zip.Dispose()
    }
    if (Test-Path $tool_redirectplus_zippath) {
        Remove-Item $tool_redirectplus_zippath -Force
    }
    exit 1
}

Write-Host "Done!" -ForegroundColor Green
Write-Host ""
