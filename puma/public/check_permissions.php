<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Directory Permissions Check</h2>";

// Check images directory
$imagesDir = "images";
echo "<h3>Checking '$imagesDir' directory</h3>";

if (is_dir($imagesDir)) {
    echo "<p>Directory exists.</p>";
    echo "<p>Absolute path: " . realpath($imagesDir) . "</p>";
    
    // Check permissions
    $perms = fileperms($imagesDir);
    echo "<p>Permissions (octal): " . substr(sprintf('%o', $perms), -4) . "</p>";
    echo "<p>Permissions (human readable): ";
    
    // Owner
    echo (($perms & 0x0100) ? 'r' : '-');
    echo (($perms & 0x0080) ? 'w' : '-');
    echo (($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x' ) : (($perms & 0x0800) ? 'S' : '-'));
    
    // Group
    echo (($perms & 0x0020) ? 'r' : '-');
    echo (($perms & 0x0010) ? 'w' : '-');
    echo (($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x' ) : (($perms & 0x0400) ? 'S' : '-'));
    
    // World
    echo (($perms & 0x0004) ? 'r' : '-');
    echo (($perms & 0x0002) ? 'w' : '-');
    echo (($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x' ) : (($perms & 0x0200) ? 'T' : '-'));
    echo "</p>";
    
    // Check if directory is writable
    if (is_writable($imagesDir)) {
        echo "<p style='color:green'>Directory is writable.</p>";
    } else {
        echo "<p style='color:red'>Directory is NOT writable.</p>";
    }
    
    // Try to create a test file
    $testFile = $imagesDir . "/test_" . time() . ".txt";
    echo "<p>Trying to create a test file: $testFile</p>";
    
    $handle = @fopen($testFile, 'w');
    if ($handle) {
        fwrite($handle, "This is a test file.");
        fclose($handle);
        echo "<p style='color:green'>Test file created successfully.</p>";
        
        // Try to delete the test file
        if (unlink($testFile)) {
            echo "<p style='color:green'>Test file deleted successfully.</p>";
        } else {
            echo "<p style='color:red'>Failed to delete test file.</p>";
        }
    } else {
        echo "<p style='color:red'>Failed to create test file.</p>";
        echo "<p>Error: " . error_get_last()['message'] . "</p>";
    }
    
    // List files in directory
    $files = scandir($imagesDir);
    echo "<h4>Files in directory:</h4>";
    echo "<ul>";
    foreach ($files as $file) {
        if ($file != "." && $file != "..") {
            echo "<li>$file</li>";
        }
    }
    echo "</ul>";
} else {
    echo "<p style='color:red'>Directory does not exist.</p>";
    
    // Try to create the directory
    echo "<p>Attempting to create directory...</p>";
    if (mkdir($imagesDir, 0777, true)) {
        echo "<p style='color:green'>Directory created successfully.</p>";
        
        // Check permissions after creation
        $perms = fileperms($imagesDir);
        echo "<p>Permissions (octal): " . substr(sprintf('%o', $perms), -4) . "</p>";
    } else {
        echo "<p style='color:red'>Failed to create directory.</p>";
        echo "<p>Error: " . error_get_last()['message'] . "</p>";
    }
}

// Check PHP configuration
echo "<h3>PHP Configuration</h3>";
echo "<p>PHP version: " . PHP_VERSION . "</p>";
echo "<p>upload_max_filesize: " . ini_get('upload_max_filesize') . "</p>";
echo "<p>post_max_size: " . ini_get('post_max_size') . "</p>";
echo "<p>max_file_uploads: " . ini_get('max_file_uploads') . "</p>";
echo "<p>memory_limit: " . ini_get('memory_limit') . "</p>";
echo "<p>max_execution_time: " . ini_get('max_execution_time') . " seconds</p>";

// Check server information
echo "<h3>Server Information</h3>";
echo "<p>Server software: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p>Document root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p>Current script: " . $_SERVER['SCRIPT_FILENAME'] . "</p>";
echo "<p>Current user: " . get_current_user() . "</p>";
echo "<p>Current working directory: " . getcwd() . "</p>";

// Check if we can create a file in the current directory
$testFile = "test_" . time() . ".txt";
echo "<h3>Testing File Creation in Current Directory</h3>";
echo "<p>Trying to create a test file: $testFile</p>";

$handle = @fopen($testFile, 'w');
if ($handle) {
    fwrite($handle, "This is a test file.");
    fclose($handle);
    echo "<p style='color:green'>Test file created successfully.</p>";
    
    // Try to delete the test file
    if (unlink($testFile)) {
        echo "<p style='color:green'>Test file deleted successfully.</p>";
    } else {
        echo "<p style='color:red'>Failed to delete test file.</p>";
    }
} else {
    echo "<p style='color:red'>Failed to create test file.</p>";
    echo "<p>Error: " . error_get_last()['message'] . "</p>";
}
?>
