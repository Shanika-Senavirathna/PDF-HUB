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
$stmt = $pdo->prepare("SELECT * FROM files WHERE id = ? AND user_id = ?");
$stmt->execute([$file_id, $_SESSION['user_id']]);
$file = $stmt->fetch();

if (!$file) {
    header("Location: personal.php");
    exit();
}

// Delete file from storage
if (file_exists($file['filepath'])) {
    unlink($file['filepath']);
}

// Delete file record from database
$stmt = $pdo->prepare("DELETE FROM files WHERE id = ?");
$stmt->execute([$file_id]);

header("Location: personal.php");
exit(); 