<?php

require_once '../app/bootstrap.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

$db = (new \App\Core\Database())->getConnection();

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status_text = $_POST['status_text'];
    $image_path_db = null; 

    if (isset($_FILES['status_image']) && $_FILES['status_image']['error'] == 0) {
        $upload_dir = 'uploads/'; 
        
        $file_name = uniqid() . '-' . basename($_FILES['status_image']['name']);
        $target_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['status_image']['tmp_name'], $target_path)) {
            $image_path_db = $file_name; 
        }
    }

    if (!empty($status_text) || $image_path_db) {
        $sql = "INSERT INTO posts (user_id, status_text, image_path) VALUES (?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$user_id, $status_text, $image_path_db]);
    }

    header('Location: dashboard.php');
    exit;
}


$sql = "SELECT 
            posts.status_text, 
            posts.image_path, 
            posts.created_at, 
            users.username 
        FROM posts
        JOIN users ON posts.user_id = users.id
        ORDER BY posts.created_at DESC";

$stmt = $db->prepare($sql);
$stmt->execute();
$posts = $stmt->fetchAll();

require_once '../app/views/templates/header.php';
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">AuthBoard</a>
        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                <a class="nav-link" href="logout.php">Logout</a> 
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            
            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="card-title">Create a Post</h2>
                    <form action="dashboard.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="status_text" class="form-label">What's on your mind?</label>
                            <textarea class="form-control" name="status_text" id="status_text" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="status_image" class="form-label">Add an image (Optional)</label>
                            <input class="form-control" type="file" name="status_image" id="status_image">
                        </div>
                        <button type="submit" class="btn btn-primary">Post</button>
                    </form>
                </div>
            </div>

            <hr class="my-4">

            <h2 class="mb-3">Feed</h2>
            
            <?php if (empty($posts)): ?>
                <div class="card">
                    <div class="card-body text-center">
                        <p class="mb-0">No posts yet. Be the first!</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title mb-0"><?php echo htmlspecialchars($post['username']); ?></h5>
                            <small class="text-muted"><?php echo date('d M Y \a\t H:i', strtotime($post['created_at'])); ?></small>
                            
                            <p class="card-text mt-2"><?php echo nl2br(htmlspecialchars($post['status_text'])); ?></p>
                            
                            <?php if ($post['image_path']): ?>
                                <img src="uploads/<?php echo htmlspecialchars($post['image_path']); ?>" class="img-fluid rounded" alt="Post image">
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php
require__once '../app/views/templates/footer.php';
?>