<?php
session_start(); // Start the session to use session variables (e.g., for success/error messages)
require_once 'includes/db.php'; // Include the database connection file

$errors = [];
$data = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate name
    $name = trim($_POST['name']);
    if (empty($name)) {
        $errors['name'] = 'Name is required.';
    } elseif (!preg_match("/^[a-zA-Z-' ]*$/", $name)) {
        $errors['name'] = 'Only letters and white space allowed in name.';
    }

    // Sanitize and validate email
    $email = trim($_POST['email']);
    if (empty($email)) {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format.';
    }

    // Sanitize and validate subject
    $subject = trim($_POST['subject']);
    if (empty($subject)) {
        $errors['subject'] = 'Subject is required.';
    }  // No complex validation needed for subject, just ensure it's not empty and sanitize.

    // Sanitize and validate message
    $message_text = trim($_POST['message']);
    if (empty($message_text)) {
        $errors['message'] = 'Message is required.';
    }

    if (empty($errors)) {
        // Prepare an insert statement
        $sql = "INSERT INTO messages (name, email, subject, message) VALUES (?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            // ssss indicates four string parameters
            mysqli_stmt_bind_param($stmt, "ssss", $param_name, $param_email, $param_subject, $param_message);

            // Set parameters
            $param_name = $name;
            $param_email = $email;
            $param_subject = $subject;
            $param_message = $message_text;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['success_message'] = "Your message has been sent successfully. We will get back to you shortly.";
                // Redirect to a 'thank you' page or back to the contact form with a success message
                // For now, we'll redirect to index.php#contact and display message there (needs JS on index.php to show it)
                header("location: index.php?submission=success#contact");
                exit();
            } else {
                // echo "Oops! Something went wrong. Please try again later.";
                $_SESSION['error_message'] = "Oops! Something went wrong on our end. Please try again later or contact us directly via phone or email.";
                header("location: index.php?submission=error#contact");
                exit();
            }

            // Close statement
            mysqli_stmt_close($stmt);
        } else {
            // echo "ERROR: Could not prepare query: $sql. " . mysqli_error($link);
            $_SESSION['error_message'] = "ERROR: Could not prepare your message. Please try again.";
            header("location: index.php?submission=dberror#contact");
            exit();
        }
    } else {
        // Store errors in session to display them on the form page
        $_SESSION['form_errors'] = $errors;
        $_SESSION['form_data'] = $_POST; // Optionally send back submitted data to repopulate form
        header("location: index.php?submission=validation_error#contact");
        exit();
    }

    // Close connection
    mysqli_close($link);
} else {
    // Not a POST request, redirect to home or show an error
    header("location: index.php");
    exit();
}
?>
