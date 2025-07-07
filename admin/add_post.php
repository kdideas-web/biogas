<?php
$page_title = "Add New Blog Post";
require_once 'includes/header.php';
require_once '../includes/db.php';

$title = '';
$slug = '';
$content = '';
$image_url = '';
$status = 'draft'; // Default status
$published_at_date = ''; // For date input
$published_at_time = ''; // For time input
$errors = [];

// Function to generate a unique slug
function generateUniqueSlug($link, $title, $original_slug = null, $id_to_exclude = 0) {
    $slug = $original_slug ? $original_slug : $title;
    // Sanitize slug: lowercase, replace non-alphanumeric with dash, remove multiple dashes
    $slug = strtolower(trim($slug));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    $slug = trim($slug, '-');

    if (empty($slug)) { // If title was all special chars, slug might be empty
        $slug = 'post-' . time(); // Fallback to a time-based slug
    }

    $query = "SELECT id FROM posts WHERE slug = ? AND id != ?";
    $stmt = mysqli_prepare($link, $query);
    $count = 0;
    $baseSlug = $slug;

    do {
        $currentSlug = ($count > 0) ? $baseSlug . '-' . $count : $baseSlug;
        mysqli_stmt_bind_param($stmt, "si", $currentSlug, $id_to_exclude);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $is_taken = mysqli_stmt_num_rows($stmt) > 0;
        if ($is_taken) {
            $count++;
        }
    } while ($is_taken);

    mysqli_stmt_close($stmt);
    return $currentSlug;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $slug_provided = trim($_POST['slug']);
    $content = trim($_POST['content']); // Content can be HTML/Markdown, further sanitization on display if needed
    $image_url = trim($_POST['image_url']);
    $status = in_array($_POST['status'], ['draft', 'published']) ? $_POST['status'] : 'draft';
    $published_at_date = trim($_POST['published_at_date']);
    $published_at_time = trim($_POST['published_at_time']);
    $author_id = $_SESSION['admin_id']; // Assign logged-in admin as author

    // Validate form data
    if (empty($title)) $errors['title'] = "Title is required.";
    if (empty($content)) $errors['content'] = "Content is required.";

    // Slug generation/validation
    $slug = generateUniqueSlug($link, $title, $slug_provided);
    if (empty($slug)) $errors['slug'] = "Slug could not be generated. Provide a valid title or slug.";

    // Published At datetime combination
    $published_at = null;
    if ($status == 'published') {
        if (!empty($published_at_date) && !empty($published_at_time)) {
            $published_at_datetime_str = $published_at_date . ' ' . $published_at_time;
            $d = DateTime::createFromFormat('Y-m-d H:i', $published_at_datetime_str);
            if ($d && $d->format('Y-m-d H:i') === $published_at_datetime_str) {
                $published_at = $d->format('Y-m-d H:i:s');
            } else {
                $errors['published_at'] = "Invalid date or time format for published date.";
            }
        } elseif (!empty($published_at_date) && empty($published_at_time)) { // Date provided, time not
             $published_at_datetime_str = $published_at_date . ' 00:00'; // Default to midnight
             $d = DateTime::createFromFormat('Y-m-d H:i', $published_at_datetime_str);
             if ($d && $d->format('Y-m-d H:i') === $published_at_datetime_str) {
                $published_at = $d->format('Y-m-d H:i:s');
            } else {
                 $errors['published_at'] = "Invalid date format for published date.";
            }
        } else { // Default to now if status is published and no date/time provided
            $published_at = date('Y-m-d H:i:s');
        }
    }

    if (empty($image_url)) $image_url = null;

    if (empty($errors)) {
        $sql = "INSERT INTO posts (title, slug, content, author_id, image_url, status, published_at) VALUES (?, ?, ?, ?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "sssisss", $title, $slug, $content, $author_id, $image_url, $status, $published_at);
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'Blog post added successfully!'];
                header("Location: manage_posts.php");
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
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?php echo htmlspecialchars($page_title); ?></h1>
    <a href="manage_posts.php" class="btn btn-sm btn-outline-secondary">Back to Posts</a>
</div>

<?php if (!empty($errors['db'])): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($errors['db']); ?></div>
<?php endif; ?>

<form id="addPostForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" novalidate>
    <div class="row">
        <div class="col-md-8">
            <div class="mb-3">
                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control <?php echo isset($errors['title']) ? 'is-invalid' : ''; ?>" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
                <?php if (isset($errors['title'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['title']); ?></div><?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="slug" class="form-label">Slug (URL-friendly-name)</label>
                <input type="text" class="form-control <?php echo isset($errors['slug']) ? 'is-invalid' : ''; ?>" id="slug" name="slug" value="<?php echo htmlspecialchars($slug); ?>" placeholder="Auto-generated if left empty">
                <small class="form-text text-muted">If left empty, a slug will be generated from the title. Use lowercase letters, numbers, and hyphens.</small>
                <?php if (isset($errors['slug'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['slug']); ?></div><?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                <textarea class="form-control <?php echo isset($errors['content']) ? 'is-invalid' : ''; ?>" id="content" name="content" rows="15" required><?php echo htmlspecialchars($content); ?></textarea>
                <small class="form-text text-muted">You can use basic HTML for formatting. A rich text editor (WYSIWYG) can be added later.</small>
                <?php if (isset($errors['content'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['content']); ?></div><?php endif; ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Publish Settings</h5>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select <?php echo isset($errors['status']) ? 'is-invalid' : ''; ?>" id="status" name="status">
                            <option value="draft" <?php echo ($status == 'draft') ? 'selected' : ''; ?>>Draft</option>
                            <option value="published" <?php echo ($status == 'published') ? 'selected' : ''; ?>>Published</option>
                        </select>
                        <?php if (isset($errors['status'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['status']); ?></div><?php endif; ?>
                    </div>
                     <div id="publish_date_time_fields" class="<?php echo $status == 'published' ? '' : 'd-none'; ?>">
                        <div class="mb-3">
                            <label for="published_at_date" class="form-label">Publish Date</label>
                            <input type="date" class="form-control <?php echo isset($errors['published_at']) ? 'is-invalid' : ''; ?>" id="published_at_date" name="published_at_date" value="<?php echo htmlspecialchars($published_at_date); ?>">
                        </div>
                        <div class="mb-3">
                             <label for="published_at_time" class="form-label">Publish Time</label>
                            <input type="time" class="form-control <?php echo isset($errors['published_at']) ? 'is-invalid' : ''; ?>" id="published_at_time" name="published_at_time" value="<?php echo htmlspecialchars($published_at_time); ?>">
                        </div>
                        <?php if (isset($errors['published_at'])): ?><div class="invalid-feedback d-block"><?php echo htmlspecialchars($errors['published_at']); ?></div><?php endif; ?>
                        <small class="form-text text-muted">If status is 'Published' and no date/time is set, it will default to now.</small>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="card-title">Featured Image</h5>
                    <div class="mb-3">
                        <label for="image_url" class="form-label">Image URL</label>
                        <input type="url" class="form-control <?php echo isset($errors['image_url']) ? 'is-invalid' : ''; ?>" id="image_url" name="image_url" value="<?php echo htmlspecialchars($image_url); ?>" placeholder="https://example.com/image.jpg">
                         <small class="form-text text-muted">Enter a full URL for the post's main image. File uploads can be a future enhancement.</small>
                        <?php if (isset($errors['image_url'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['image_url']); ?></div><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <button type="submit" class="btn btn-primary">Save Post</button>
    <a href="manage_posts.php" class="btn btn-secondary">Cancel</a>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const titleInput = document.getElementById('title');
    const slugInput = document.getElementById('slug');
    const statusSelect = document.getElementById('status');
    const publishDateTimeFields = document.getElementById('publish_date_time_fields');

    // Auto-generate slug from title if slug field is empty (basic version)
    if (titleInput && slugInput) {
        titleInput.addEventListener('blur', function() {
            if (slugInput.value === '') {
                let generatedSlug = titleInput.value.toLowerCase()
                    .trim()
                    .replace(/\s+/g, '-') // Replace spaces with -
                    .replace(/[^\w-]+/g, '') // Remove all non-word chars
                    .replace(/--+/g, '-'); // Replace multiple - with single -
                // slugInput.value = generatedSlug; // User might want to type their own, so maybe don't auto-fill aggressively
            }
        });
    }

    // Show/hide publish date/time fields based on status
    if(statusSelect && publishDateTimeFields){
        function togglePublishDateTimeFields() {
            if (statusSelect.value === 'published') {
                publishDateTimeFields.classList.remove('d-none');
            } else {
                publishDateTimeFields.classList.add('d-none');
            }
        }
        statusSelect.addEventListener('change', togglePublishDateTimeFields);
        // togglePublishDateTimeFields(); // Initial check on page load already handled by PHP class
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
