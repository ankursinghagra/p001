<?php
error_reporting(E_ERROR | E_PARSE); 
require_once('lib.php');
if(isset($_POST['foldername'])){
    $zipdir = $_POST['foldername'];
}

$folderpath = "output/".$_POST['foldername'];

$rootPath = realpath($folderpath);

$zname = $_POST['foldername'].".zip";

// Initialize archive object
$zip = new ZipArchive();
$zip->open($zname, ZipArchive::CREATE | ZipArchive::OVERWRITE);

// Create recursive directory iterator
/** @var SplFileInfo[] $files */
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootPath),
    RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($files as $name => $file)
{
    // Skip directories (they would be added automatically)
    if (!$file->isDir())
    {
        // Get real and relative path for current file
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($rootPath) + 1);

        // Add current file to archive
        $zip->addFile($filePath, $relativePath);
    }
}

// Zip archive will be created only after closing object
$zip->close();


$file_name = $zname;
$file_url = $zname;
header('Content-Type: application/octet-stream');
header("Content-Transfer-Encoding: Binary"); 
header("Content-disposition: attachment; filename=\"".$zname."\""); 
readfile($file_url);
unlink($zname);
exit;

