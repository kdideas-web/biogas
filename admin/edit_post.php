<?php
$page_title = "Edit Blog Post";
require_once 'includes/header.php';
require_once '../includes/db.php';

$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$title = '';
$slug = '';
$content = '';
$image_url = '';
$status = 'draft';
$published_at_date = '';
$published_at_time = '';
$current_author_id = 0;
$errors = [];

// Function to generate a unique slug (copied from add_post.php, could be refactored into a helper file)
function generateUniqueSlug($link, $title, $original_slug = null, $id_to_exclude = 0) {
    $slug_to_check = $original_slug ? $original_slug : $title;
    // Sanitize slug
    $slug_to_check = strtolower(trim($slug_to_check));
    $slug_to_check = preg_replace('/[^a-z0-9]+/', '-', $slug_to_check);
    $slug_to_check = preg_replace('/-+/', '-', $slug_to_check);
    $slug_to_check = trim($slug_to_check, '-');

    if (empty($slug_to_check)) {
        return 'post-' . ($id_to_exclude ?: time()); // Fallback
    }

    $query = "SELECT id FROM posts WHERE slug = ? AND id != ?";
    $stmt = mysqli_prepare($link, $query);
    $count = 0;
    $baseSlug = $slug_to_check;
    $finalSlug = $baseSlug;

    do {
        mysqli_stmt_bind_param($stmt, "si", $finalSlug, $id_to_exclude);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $is_taken = mysqli_stmt_num_rows($stmt) > 0;
        if ($is_taken) {
            $count++;
            $finalSlug = $baseSlug . '-' . $count;
        }
    } while ($is_taken);

    mysqli_stmt_close($stmt);
    return $finalSlug;
}

// Fetch existing post data
if ($post_id > 0) {
    $sql_fetch = "SELECT title, slug, content, author_id, image_url, status, published_at FROM posts WHERE id = ?";
    if ($stmt_fetch = mysqli_prepare($link, $sql_fetch)) {
        mysqli_stmt_bind_param($stmt_fetch, "i", $post_id);
        if (mysqli_stmt_execute($stmt_fetch)) {
            mysqli_stmt_bind_result($stmt_fetch, $db_title, $db_slug, $db_content, $db_author_id, $db_image_url, $db_status, $db_published_at);
            if (mysqli_stmt_fetch($stmt_fetch)) {
                $title = $db_title;
                $slug = $db_slug;
                $content = $db_content;
                $current_author_id = $db_author_id; // Store current author
                $image_url = $db_image_url;
                $status = $db_status;
                if ($db_published_at) {
                    $published_dt_obj = new DateTime($db_published_at);
                    $published_at_date = $published_dt_obj->format('Y-m-d');
                    $published_at_time = $published_dt_obj->format('H:i');
                }
            } else {
                $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Blog post not found.'];
                header("Location: manage_posts.php");
                exit;
            }
        } else {
            $errors['db_fetch'] = "Error fetching post data: " . mysqli_error($link);
        }
        mysqli_stmt_close($stmt_fetch);
    } else {
        $errors['db_fetch'] = "Database error: Could not prepare fetch statement.";
    }
} else {
    $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Invalid post ID.'];
    header("Location: manage_posts.php");
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $slug_provided = trim($_POST['slug']);
    $content = trim($_POST['content']);
    $image_url = trim($_POST['image_url']);
    $status = in_array($_POST['status'], ['draft', 'published']) ? $_POST['status'] : 'draft';
    $published_at_date = trim($_POST['published_at_date']);
    $published_at_time = trim($_POST['published_at_time']);
    // Author ID generally shouldn't change on edit by another admin, unless explicitly designed for it.
    // For now, keep original author or update to current admin if that's desired.
    // $author_id = $_SESSION['admin_id']; // Or keep $current_author_id

    if (empty($title)) $errors['title'] = "Title is required.";
    if (empty($content)) $errors['content'] = "Content is required.";

    // Slug generation/validation, excluding current post ID from unique check
    $slug = generateUniqueSlug($link, $title, $slug_provided, $post_id);
     if (empty($slug)) $errors['slug'] = "Slug could not be generated. Provide a valid title or slug.";

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
        } elseif (!empty($published_at_date) && empty($published_at_time)) {
             $published_at_datetime_str = $published_at_date . ' 00:00';
             $d = DateTime::createFromFormat('Y-m-d H:i', $published_at_datetime_str);
             if ($d && $d->format('Y-m-d H:i') === $published_at_datetime_str) {
                $published_at = $d->format('Y-m-d H:i:s');
            } else {
                 $errors['published_at'] = "Invalid date format for published date.";
            }
        } elseif (empty($db_published_at)) { // If it was never published before and now set to published without date
             $published_at = date('Y-m-d H:i:s');
        } elseif (!empty($db_published_at)) { // If it was published, keep original publish date unless new one specified
            $published_at = (new DateTime($db_published_at))->format('Y-m-d H:i:s');
        }
    }
     if (empty($image_url)) $image_url = null;

    if (empty($errors)) {
        // Note: author_id is not updated here, keeps original author.
        // If you want to update author to current editor: $author_id_to_set = $_SESSION['admin_id'];
        $sql_update = "UPDATE posts SET title = ?, slug = ?, content = ?, image_url = ?, status = ?, published_at = ?, updated_at = NOW() WHERE id = ?";
        if ($stmt_update = mysqli_prepare($link, $sql_update)) {
            mysqli_stmt_bind_param($stmt_update, "ssssssi", $title, $slug, $content, $image_url, $status, $published_at, $post_id);
            if (mysqli_stmt_execute($stmt_update)) {
                $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'Blog post updated successfully!'];
                header("Location: manage_posts.php");
                exit;
            } else {
                $errors['db'] = "Database error: Could not execute update. " . mysqli_error($link);
            }
            mysqli_stmt_close($stmt_update);
        } else {
            $errors['db'] = "Database error: Could not prepare update. " . mysqli_error($link);
        }
    }
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?php echo htmlspecialchars($page_title); ?></h1>
    <a href="manage_posts.php" class="btn btn-sm btn-outline-secondary">Back to Posts</a>
</div>

<?php if (!empty($errors['db_fetch'])): ?> <div class="alert alert-danger"><?php echo htmlspecialchars($errors['db_fetch']); ?></div> <?php endif; ?>
<?php if (!empty($errors['db'])): ?> <div class="alert alert-danger"><?php echo htmlspecialchars($errors['db']); ?></div> <?php endif; ?>

<form id="editPostForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?id=<?php echo $post_id; ?>" method="post" novalidate>
    <div class="row">
        <div class="col-md-8">
            <div class="mb-3">
                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control <?php echo isset($errors['title']) ? 'is-invalid' : ''; ?>" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
                <?php if (isset($errors['title'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['title']); ?></div><?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="slug" class="form-label">Slug</label>
                <input type="text" class="form-control <?php echo isset($errors['slug']) ? 'is-invalid' : ''; ?>" id="slug" name="slug" value="<?php echo htmlspecialchars($slug); ?>" placeholder="Auto-updated if left empty based on title">
                <small class="form-text text-muted">If left empty, a slug will be generated from the title. Use lowercase letters, numbers, and hyphens.</small>
                <?php if (isset($errors['slug'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['slug']); ?></div><?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                <textarea class="form-control <?php echo isset($errors['content']) ? 'is-invalid' : ''; ?>" id="content" name="content" rows="15" required><?php echo htmlspecialchars($content); ?></textarea>
                 <small class="form-text text-muted">You can use basic HTML for formatting.</small>
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
                     <div id="publish_date_time_fields_edit" class="<?php echo $status == 'published' ? '' : 'd-none'; ?>">
                        <div class="mb-3">
                            <label for="published_at_date" class="form-label">Publish Date</label>
                            <input type="date" class="form-control <?php echo isset($errors['published_at']) ? 'is-invalid' : ''; ?>" id="published_at_date" name="published_at_date" value="<?php echo htmlspecialchars($published_at_date); ?>">
                        </div>
                        <div class="mb-3">
                             <label for="published_at_time" class="form-label">Publish Time</label>
                            <input type="time" class="form-control <?php echo isset($errors['published_at']) ? 'is-invalid' : ''; ?>" id="published_at_time" name="published_at_time" value="<?php echo htmlspecialchars($published_at_time); ?>">
                        </div>
                        <?php if (isset($errors['published_at'])): ?><div class="invalid-feedback d-block"><?php echo htmlspecialchars($errors['published_at']); ?></div><?php endif; ?>
                         <small class="form-text text-muted">If status is 'Published' and no date/time is set, it will use original publish date or default to now if never published.</small>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="card-title">Featured Image</h5>
                    <div class="mb-3">
                        <label for="image_url" class="form-label">Image URL</label>
                        <input type="url" class="form-control <?php echo isset($errors['image_url']) ? 'is-invalid' : ''; ?>" id="image_url" name="image_url" value="<?php echo htmlspecialchars($image_url ?? ''); ?>" placeholder="https://example.com/image.jpg">
                        <small class="form-text text-muted">Enter a full URL for the post's main image.</small>
                        <?php if (isset($errors['image_url'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['image_url']); ?></div><?php endif; ?>
                         <?php if (!empty($image_url)): ?>
                            <img src="<?php echo htmlspecialchars($image_url); ?>" alt="Current image preview" class="img-thumbnail mt-2" style="max-height: 150px;">
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="card-title">Author</h5>
                    <p>
                        <?php
                        // Fetch author username - this is a bit inefficient here, better to join in initial query if displaying.
                        // For now, just show ID or a placeholder.
                        echo "Author ID: " . htmlspecialchars($current_author_id);
                        // In a real app, you might have a dropdown to change author if permissions allow.
                        ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <button type="submit" class="btn btn-primary">Update Post</button>
    <a href="manage_posts.php" class="btn btn-secondary">Cancel</a>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusSelectEdit = document.getElementById('status'); // Ensure unique ID if add_post is on same page, but it's not.
    const publishDateTimeFieldsEdit = document.getElementById('publish_date_time_fields_edit');

    if(statusSelectEdit && publishDateTimeFieldsEdit){
        function togglePublishDateTimeFieldsEdit() {
            if (statusSelectEdit.value === 'published') {
                publishDateTimeFieldsEdit.classList.remove('d-none');
            } else {
                publishDateTimeFieldsEdit.classList.add('d-none');
            }
        }
        statusSelectEdit.addEventListener('change', togglePublishDateTimeFieldsEdit);
        // togglePublishDateTimeFieldsEdit(); // Initial state handled by PHP class
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
