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

// Generate share ID if not exists
if (!$file['share_id']) {
    $share_id = bin2hex(random_bytes(16));
    $stmt = $pdo->prepare("UPDATE files SET share_id = ? WHERE id = ?");
    $stmt->execute([$share_id, $file_id]);
} else {
    $share_id = $file['share_id'];
}

// Toggle public status
$is_public = !$file['is_public'];
$stmt = $pdo->prepare("UPDATE files SET is_public = ? WHERE id = ?");
$stmt->execute([$is_public, $file_id]);

// Generate share URL
$share_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . 
             "://$_SERVER[HTTP_HOST]/public.php?file=" . $share_id;

// Return JSON response
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'share_url' => $share_url,
    'is_public' => $is_public
]); 