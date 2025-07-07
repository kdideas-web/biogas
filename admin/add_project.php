<?php
$page_title = "Add New Project";
require_once 'includes/header.php';
require_once '../includes/db.php'; // Main site's DB connection

$title = '';
$description = '';
$image_url = '';
$date_completed = '';
$location = '';
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and retrieve form data
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $image_url = trim($_POST['image_url']); // For now, simple text input
    $date_completed = trim($_POST['date_completed']);
    $location = trim($_POST['location']);

    // Validate form data
    if (empty($title)) {
        $errors['title'] = "Title is required.";
    }
    if (empty($description)) {
        $errors['description'] = "Description is required.";
    }
    // Optional fields: image_url, date_completed, location
    // Validate date format if provided
    if (!empty($date_completed)) {
        $d = DateTime::createFromFormat('Y-m-d', $date_completed);
        if (!$d || $d->format('Y-m-d') !== $date_completed) {
            $errors['date_completed'] = "Invalid date format. Please use YYYY-MM-DD.";
        }
    } else {
        $date_completed = null; // Ensure it's NULL if empty for DB
    }

    if (empty($image_url)) { // If image_url is optional and empty
        $image_url = null;
    }


    if (empty($errors)) {
        $sql = "INSERT INTO projects (title, description, image_url, date_completed, location) VALUES (?, ?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "sssss", $title, $description, $image_url, $date_completed, $location);

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'Project added successfully!'];
                header("Location: manage_projects.php");
                exit;
            } else {
                $errors['db'] = "Database error: Could not execute statement. " . mysqli_error($link);
            }
            mysqli_stmt_close($stmt);
        } else {
            $errors['db'] = "Database error: Could not prepare statement. " . mysqli_error($link);
        }
    }
}
// mysqli_close($link); // Close in footer
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?php echo htmlspecialchars($page_title); ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="manage_projects.php" class="btn btn-sm btn-outline-secondary">
            Back to Projects
        </a>
    </div>
</div>

<?php if (!empty($errors['db'])): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($errors['db']); ?></div>
<?php endif; ?>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" novalidate>
    <div class="mb-3">
        <label for="title" class="form-label">Project Title <span class="text-danger">*</span></label>
        <input type="text" class="form-control <?php echo isset($errors['title']) ? 'is-invalid' : ''; ?>" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
        <?php if (isset($errors['title'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['title']); ?></div><?php endif; ?>
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
        <textarea class="form-control <?php echo isset($errors['description']) ? 'is-invalid' : ''; ?>" id="description" name="description" rows="5" required><?php echo htmlspecialchars($description); ?></textarea>
        <?php if (isset($errors['description'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['description']); ?></div><?php endif; ?>
    </div>

    <div class="mb-3">
        <label for="image_url" class="form-label">Image URL (e.g., https://via.placeholder.com/400x250)</label>
        <input type="url" class="form-control <?php echo isset($errors['image_url']) ? 'is-invalid' : ''; ?>" id="image_url" name="image_url" value="<?php echo htmlspecialchars($image_url); ?>">
        <small class="form-text text-muted">Enter a full URL for the project image. File uploads will be a future enhancement.</small>
        <?php if (isset($errors['image_url'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['image_url']); ?></div><?php endif; ?>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="date_completed" class="form-label">Date Completed (YYYY-MM-DD)</label>
            <input type="date" class="form-control <?php echo isset($errors['date_completed']) ? 'is-invalid' : ''; ?>" id="date_completed" name="date_completed" value="<?php echo htmlspecialchars($date_completed); ?>">
            <?php if (isset($errors['date_completed'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['date_completed']); ?></div><?php endif; ?>
        </div>
        <div class="col-md-6 mb-3">
            <label for="location" class="form-label">Location</label>
            <input type="text" class="form-control <?php echo isset($errors['location']) ? 'is-invalid' : ''; ?>" id="location" name="location" value="<?php echo htmlspecialchars($location); ?>">
            <?php if (isset($errors['location'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['location']); ?></div><?php endif; ?>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Add Project</button>
    <a href="manage_projects.php" class="btn btn-secondary">Cancel</a>
</form>

<?php require_once 'includes/footer.php'; ?>
