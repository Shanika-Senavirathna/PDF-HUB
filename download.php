<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: personal.php");
    exit();
}

$file_id = $_GET['id'];

// Get file information
$stmt = $pdo->prepare("SELECT * FROM files WHERE id = ?");
$stmt->execute([$file_id]);
$file = $stmt->fetch();

if (!$file) {
    header("Location: personal.php");
    exit();
}

// Check if user has permission to download
if ($file['user_id'] !== $_SESSION['user_id'] && !$file['is_public']) {
    header("Location: personal.php");
    exit();
}

// Check if file exists
if (!file_exists($file['filepath'])) {
    die("File not found");
}

// Set headers for download
header('Content-Type: ' . $file['filetype']);
header('Content-Disposition: attachment; filename="' . $file['filename'] . '"');
header('Content-Length: ' . $file['filesize']);
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Output file
readfile($file['filepath']);
exit(); 