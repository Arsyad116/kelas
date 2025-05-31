<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>File Upload Test</h2>";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "<h3>Form Submitted</h3>";
    
    // Debug information
    echo "<h4>POST Data:</h4>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    echo "<h4>FILES Data:</h4>";
    echo "<pre>";
    print_r($_FILES);
    echo "</pre>";
    
    // Check if file was uploaded
    if (isset($_FILES['test_file']) && $_FILES['test_file']['error'] == 0) {
        echo "<p style='color:green'>File uploaded successfully.</p>";
        
        // Get file information
        $fileName = basename($_FILES['test_file']['name']);
        $fileSize = $_FILES['test_file']['size'];
        $fileTmpPath = $_FILES['test_file']['tmp_name'];
        $fileType = $_FILES['test_file']['type'];
        
        echo "<p>File Name: $fileName</p>";
        echo "<p>File Size: $fileSize bytes</p>";
        echo "<p>File Type: $fileType</p>";
        echo "<p>Temporary Path: $fileTmpPath</p>";
        
        // Check if images directory exists
        $uploadDir = "images/";
        if (is_dir($uploadDir)) {
            echo "<p style='color:green'>Directory '$uploadDir' exists.</p>";
            
            // Check if directory is writable
            if (is_writable($uploadDir)) {
                echo "<p style='color:green'>Directory '$uploadDir' is writable.</p>";
                
                // Try to move the file
                $targetFile = $uploadDir . $fileName;
                if (move_uploaded_file($fileTmpPath, $targetFile)) {
                    echo "<p style='color:green'>File moved to: $targetFile</p>";
                    echo "<p>File URL: <a href='$targetFile' target='_blank'>$targetFile</a></p>";
                    
                    // Display the image
                    echo "<img src='$targetFile' style='max-width:300px; max-height:300px;'>";
                } else {
                    echo "<p style='color:red'>Error moving file to $targetFile</p>";
                    echo "<p>Error code: " . error_get_last()['message'] . "</p>";
                }
            } else {
                echo "<p style='color:red'>Directory '$uploadDir' is not writable.</p>";
                echo "<p>Current permissions: " . substr(sprintf('%o', fileperms($uploadDir)), -4) . "</p>";
            }
        } else {
            echo "<p style='color:red'>Directory '$uploadDir' does not exist.</p>";
            
            // Try to create the directory
            echo "<p>Attempting to create directory...</p>";
            if (mkdir($uploadDir, 0777, true)) {
                echo "<p style='color:green'>Directory created successfully.</p>";
            } else {
                echo "<p style='color:red'>Failed to create directory.</p>";
                echo "<p>Error: " . error_get_last()['message'] . "</p>";
            }
        }
    } else {
        echo "<p style='color:red'>File upload failed.</p>";
        
        if (isset($_FILES['test_file'])) {
            echo "<p>Error code: " . $_FILES['test_file']['error'] . "</p>";
            
            // Explain error code
            switch ($_FILES['test_file']['error']) {
                case UPLOAD_ERR_INI_SIZE:
                    echo "<p>The uploaded file exceeds the upload_max_filesize directive in php.ini.</p>";
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    echo "<p>The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.</p>";
                    break;
                case UPLOAD_ERR_PARTIAL:
                    echo "<p>The uploaded file was only partially uploaded.</p>";
                    break;
                case UPLOAD_ERR_NO_FILE:
                    echo "<p>No file was uploaded.</p>";
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    echo "<p>Missing a temporary folder.</p>";
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    echo "<p>Failed to write file to disk.</p>";
                    break;
                case UPLOAD_ERR_EXTENSION:
                    echo "<p>A PHP extension stopped the file upload.</p>";
                    break;
                default:
                    echo "<p>Unknown upload error.</p>";
            }
        } else {
            echo "<p>No file was selected.</p>";
        }
    }
}

// Check PHP configuration
echo "<h3>PHP Configuration</h3>";
echo "<p>upload_max_filesize: " . ini_get('upload_max_filesize') . "</p>";
echo "<p>post_max_size: " . ini_get('post_max_size') . "</p>";
echo "<p>max_file_uploads: " . ini_get('max_file_uploads') . "</p>";
echo "<p>memory_limit: " . ini_get('memory_limit') . "</p>";
echo "<p>max_execution_time: " . ini_get('max_execution_time') . " seconds</p>";

// Check server information
echo "<h3>Server Information</h3>";
echo "<p>Server software: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p>PHP version: " . PHP_VERSION . "</p>";
echo "<p>Document root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p>Current script: " . $_SERVER['SCRIPT_FILENAME'] . "</p>";

// Check images directory
echo "<h3>Images Directory Check</h3>";
$uploadDir = "images/";
if (is_dir($uploadDir)) {
    echo "<p style='color:green'>Directory '$uploadDir' exists.</p>";
    echo "<p>Absolute path: " . realpath($uploadDir) . "</p>";
    echo "<p>Permissions: " . substr(sprintf('%o', fileperms($uploadDir)), -4) . "</p>";
    
    // List files in directory
    echo "<h4>Files in directory:</h4>";
    $files = scandir($uploadDir);
    echo "<ul>";
    foreach ($files as $file) {
        if ($file != "." && $file != "..") {
            echo "<li>$file</li>";
        }
    }
    echo "</ul>";
} else {
    echo "<p style='color:red'>Directory '$uploadDir' does not exist.</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        form {
            margin: 20px 0;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .submit-btn {
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h2>Upload Test Form</h2>
    <form method="POST" enctype="multipart/form-data">
        <div>
            <label for="test_file">Select a file:</label>
            <input type="file" name="test_file" id="test_file" required>
        </div>
        <button type="submit" class="submit-btn">Upload File</button>
    </form>
</body>
</html>
