<?php
$page_title = "Manage Projects";
require_once 'includes/header.php';
require_once '../includes/db.php'; // Main site's DB connection

// Handle Delete Action
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $project_id_to_delete = intval($_GET['id']);
    // It's good practice to confirm deletion, but for simplicity, direct delete here.
    // In a real app, add a JavaScript confirmation or a separate confirmation page.

    $sql_delete = "DELETE FROM projects WHERE id = ?";
    if ($stmt_delete = mysqli_prepare($link, $sql_delete)) {
        mysqli_stmt_bind_param($stmt_delete, "i", $project_id_to_delete);
        if (mysqli_stmt_execute($stmt_delete)) {
            $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'Project deleted successfully.'];
        } else {
            $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Error deleting project: ' . mysqli_error($link)];
        }
        mysqli_stmt_close($stmt_delete);
    } else {
        $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Database error: Could not prepare delete statement.'];
    }
    // Redirect to the same page to clear GET params and show flash message
    header("Location: manage_projects.php");
    exit;
}


// Fetch all projects
$projects = [];
$sql = "SELECT id, title, location, DATE_FORMAT(date_completed, '%Y-%m-%d') AS date_completed_formatted FROM projects ORDER BY created_at DESC";
$result = mysqli_query($link, $sql);
if ($result) {
    $projects = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);
} else {
    // Handle error - though flash message might be better if it's critical
    echo "<div class='alert alert-danger'>Error fetching projects: " . mysqli_error($link) . "</div>";
}
// mysqli_close($link); // Close link in footer.php
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?php echo htmlspecialchars($page_title); ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="add_project.php" class="btn btn-sm btn-outline-primary">
            Add New Project
        </a>
    </div>
</div>

<?php if (empty($projects)): ?>
    <div class="alert alert-info">No projects found. <a href="add_project.php">Add one now!</a></div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped table-sm">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Location</th>
                    <th>Date Completed</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($projects as $project): ?>
                <tr>
                    <td><?php echo htmlspecialchars($project['id']); ?></td>
                    <td><?php echo htmlspecialchars($project['title']); ?></td>
                    <td><?php echo htmlspecialchars($project['location'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($project['date_completed_formatted'] ?? 'N/A'); ?></td>
                    <td class="table-actions">
                        <a href="edit_project.php?id=<?php echo $project['id']; ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <a href="manage_projects.php?action=delete&id=<?php echo $project['id']; ?>"
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Are you sure you want to delete this project? This action cannot be undone.');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
