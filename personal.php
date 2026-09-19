<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $errors = [];

    // Validate file
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Error uploading file";
    }

    // Create uploads directory if it doesn't exist
    $upload_dir = 'uploads/' . $_SESSION['user_id'] . '/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Generate unique filename
    $filename = uniqid() . '_' . basename($file['name']);
    $filepath = $upload_dir . $filename;

    if (empty($errors) && move_uploaded_file($file['tmp_name'], $filepath)) {
        // Save file info to database
        $stmt = $pdo->prepare("INSERT INTO files (user_id, filename, filepath, filetype, filesize) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $_SESSION['user_id'],
            $file['name'],
            $filepath,
            $file['type'],
            $file['size']
        ]);
    }
}

// Get user's files
$stmt = $pdo->prepare("SELECT * FROM files WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$files = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FileShare - Personal Files</title>
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
            <a href="personal.php" class="active"><i class="fas fa-user"></i> Personal</a>
            <a href="public.php"><i class="fas fa-globe"></i> Public</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </nav>

    <div class="dashboard-container">
        <div class="upload-section">
            <h2>Upload Files</h2>
            <form method="POST" enctype="multipart/form-data" class="upload-form">
                <div class="upload-area" id="dropZone">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p>Drag and drop files here or</p>
                    <input type="file" name="file" id="fileInput" required>
                    <button type="submit" class="btn-primary">Upload File</button>
                </div>
            </form>
        </div>

        <div class="files-section">
            <h2>Your Files</h2>
            <div class="files-grid">
                <?php foreach ($files as $file): ?>
                    <div class="file-card">
                        <i class="fas <?php echo getFileIcon($file['filetype']); ?> file-icon"></i>
                        <div class="file-name"><?php echo htmlspecialchars($file['filename']); ?></div>
                        <div class="file-info">
                            <small><?php echo formatFileSize($file['filesize']); ?></small>
                        </div>
                        <div class="file-actions">
                            <a href="download.php?id=<?php echo $file['id']; ?>" class="btn-icon" title="Download">
                                <i class="fas fa-download"></i>
                            </a>
                            <a href="share.php?id=<?php echo $file['id']; ?>" class="btn-icon" title="Share">
                                <i class="fas fa-share-alt"></i>
                            </a>
                            <a href="delete.php?id=<?php echo $file['id']; ?>" class="btn-icon" title="Delete" 
                               onclick="return confirm('Are you sure you want to delete this file?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
        // Drag and drop functionality
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileInput');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            dropZone.classList.add('highlight');
        }

        function unhighlight(e) {
            dropZone.classList.remove('highlight');
        }

        dropZone.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            fileInput.files = files;
        }
    </script>
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