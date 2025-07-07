<?php
session_start();
// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: dashboard.php");
    exit;
}

require_once '../includes/db.php'; // Path to main site's db connection

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error_message = "Username and password are required.";
    } else {
        // Prepare a select statement
        $sql = "SELECT id, username, password, email FROM users WHERE username = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = $username;

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    mysqli_stmt_bind_result($stmt, $id, $db_username, $hashed_password, $email);
                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct, start a new session
                            session_regenerate_id(); // Regenerate session ID
                            $_SESSION['admin_logged_in'] = true;
                            $_SESSION['admin_id'] = $id;
                            $_SESSION['admin_username'] = $db_username;
                            $_SESSION['admin_email'] = $email;

                            header("location: dashboard.php");
                            exit;
                        } else {
                            // Display an error message if password is not valid
                            $error_message = "The password you entered was not valid.";
                        }
                    }
                } else {
                    // Display an error message if username doesn't exist
                    $error_message = "No account found with that username.";
                }
            } else {
                $error_message = "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        } else {
            $error_message = "Database error. Could not prepare statement.";
        }
    }
    mysqli_close($link);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - BioGas Accra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            background-color: #fff;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <h2 class="text-center mb-4">Admin Panel Login</h2>
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['logged_out'])): ?>
            <div class="alert alert-success">You have been logged out successfully.</div>
        <?php endif; ?>
         <?php if (isset($_GET['auth_required'])): ?>
            <div class="alert alert-warning">Please login to access the admin panel.</div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Login</button>
            </div>
        </form>
        <p class="mt-3 text-center"><a href="../index.php">← Back to Main Site</a></p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
