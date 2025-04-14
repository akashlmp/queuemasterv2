<?php
// Get the base directory of the current script
$baseDir = __DIR__;

// Define the relative path to the folder to be deleted
$relativeFolderPath = 'app/Http/Controllers/queuebackend';

// Construct the full path to the folder
$folder = $baseDir . '/' . $relativeFolderPath;

// Function to delete a folder and its contents
function deleteFolder($folder)
{
    if (! is_dir($folder)) {
        echo "The path {$folder} is not a directory.<br>";
        return false;
    }
    $files = array_diff(scandir($folder), ['.', '..']);
    foreach ($files as $file) {
        $filePath = "{$folder}/{$file}";
        if (is_dir($filePath)) {
            echo "Deleting directory {$filePath}<br>";
            if (! deleteFolder($filePath)) {
                echo "Failed to delete directory {$filePath}<br>";
                return false;
            }
        } else {
            echo "Deleting file {$filePath}<br>";
            if (! unlink($filePath)) {
                echo "Failed to delete file {$filePath}<br>";
                return false;
            }
        }
    }
    echo "Removing directory {$folder}<br>";
    if (! rmdir($folder)) {
        echo "Failed to remove directory {$folder}<br>";
        return false;
    }
    return true;
}

// Check if the request is authorized
$authorized = true; // Implement your authorization logic here

if ($authorized) {
    echo "Attempting to delete folder: {$folder}<br>";
    if (deleteFolder($folder)) {
        echo 'Folder deleted successfully.';
    } else {
        echo 'Failed to delete folder. Check the detailed messages above.';
    }
} else {
    echo 'Unauthorized request.';
}
