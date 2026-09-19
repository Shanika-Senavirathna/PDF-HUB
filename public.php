<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get shared files
$stmt = $pdo->prepare("
    SELECT f.*, u.username as owner 
    FROM files f 
    JOIN users u ON f.user_id = u.id 
    WHERE f.is_public = 1 
    ORDER BY f.created_at DESC
");
$stmt->execute();
$files = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FileShare - Public Files</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-brand">
            <i class="fas fa-file-share"></i> FileShare
        </div>
        <div class="nav-links">
            <a href="personal.php"><i class="fas fa-user"></i> Personal</a>
            <a href="public.php" class="active"><i class="fas fa-globe"></i> Public</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </nav>

    <div class="dashboard-container">
        <div class="files-section">
            <h2>Shared Files</h2>
            <div class="files-grid">
                <?php foreach ($files as $file): ?>
                    <div class="file-card">
                        <i class="fas <?php echo getFileIcon($file['filetype']); ?> file-icon"></i>
                        <div class="file-name"><?php echo htmlspecialchars($file['filename']); ?></div>
                        <div class="file-info">
                            <small>Shared by: <?php echo htmlspecialchars($file['owner']); ?></small>
                            <br>
                            <small><?php echo formatFileSize($file['filesize']); ?></small>
                        </div>
                        <div class="file-actions">
                            <a href="download.php?id=<?php echo $file['id']; ?>" class="btn-icon" title="Download">
                                <i class="fas fa-download"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>

<?php
function getFileIcon($filetype) {
    $icons = [
        'image' => 'fa-image',
        'video' => 'fa-video',
        'audio' => 'fa-music',
        'pdf' => 'fa-file-pdf',
        'text' => 'fa-file-alt',
        'default' => 'fa-file'
    ];

    $type = explode('/', $filetype)[0];
    return $icons[$type] ?? $icons['default'];
}

function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, 2) . ' ' . $units[$pow];
}
?> 