<?php
$page_title = "Manage Blog Posts";
require_once 'includes/header.php';
require_once '../includes/db.php'; // Main site's DB connection

// Handle Delete Action
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $post_id_to_delete = intval($_GET['id']);

    $sql_delete = "DELETE FROM posts WHERE id = ?";
    if ($stmt_delete = mysqli_prepare($link, $sql_delete)) {
        mysqli_stmt_bind_param($stmt_delete, "i", $post_id_to_delete);
        if (mysqli_stmt_execute($stmt_delete)) {
            $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'Post deleted successfully.'];
        } else {
            $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Error deleting post: ' . mysqli_error($link)];
        }
        mysqli_stmt_close($stmt_delete);
    } else {
        $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Database error: Could not prepare delete statement.'];
    }
    header("Location: manage_posts.php");
    exit;
}

// Fetch all posts with author username
$posts = [];
$sql = "SELECT p.id, p.title, p.status,
               DATE_FORMAT(p.published_at, '%Y-%m-%d %H:%i') AS published_at_formatted,
               u.username AS author_username
        FROM posts p
        LEFT JOIN users u ON p.author_id = u.id
        ORDER BY p.created_at DESC";
$result = mysqli_query($link, $sql);
if ($result) {
    $posts = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);
} else {
    echo "<div class='alert alert-danger'>Error fetching posts: " . mysqli_error($link) . "</div>";
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?php echo htmlspecialchars($page_title); ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="add_post.php" class="btn btn-sm btn-outline-primary">
            Add New Post
        </a>
    </div>
</div>

<?php if (empty($posts)): ?>
    <div class="alert alert-info">No posts found. <a href="add_post.php">Add one now!</a></div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped table-sm">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Published At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $post): ?>
                <tr>
                    <td><?php echo htmlspecialchars($post['id']); ?></td>
                    <td><?php echo htmlspecialchars($post['title']); ?></td>
                    <td><?php echo htmlspecialchars($post['author_username'] ?? 'N/A'); ?></td>
                    <td><span class="badge bg-<?php echo $post['status'] == 'published' ? 'success' : 'secondary'; ?>"><?php echo ucfirst(htmlspecialchars($post['status'])); ?></span></td>
                    <td><?php echo htmlspecialchars($post['published_at_formatted'] ?? 'Not Published'); ?></td>
                    <td class="table-actions">
                        <a href="../single_post.php?slug=<?php echo urlencode($post['slug'] ?? $post['id']); ?>" target="_blank" class="btn btn-sm btn-outline-info">View</a>
                        <a href="edit_post.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <a href="manage_posts.php?action=delete&id=<?php echo $post['id']; ?>"
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Are you sure you want to delete this post? This action cannot be undone.');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
