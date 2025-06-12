<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!validateCsrfToken($_POST['csrf_token'])) {
        setFlash('signup_error', 'Invalid CSRF token');
        header('Location: index.php#signup-form');
        exit();
    }

    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $terms = isset($_POST['terms']) ? true : false;

    // Validate inputs
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        setFlash('signup_error', 'Please fill in all fields');
        header('Location: index.php#signup-form');
        exit();
    }

    if (!validateEmail($email)) {
        setFlash('signup_error', 'Please enter a valid email address');
        header('Location: index.php#signup-form');
        exit();
    }

    if (!validatePassword($password)) {
        setFlash('signup_error', 'Password must be at least 8 characters with uppercase, lowercase, number and special character');
        header('Location: index.php#signup-form');
        exit();
    }

    if ($password !== $confirm_password) {
        setFlash('signup_error', 'Passwords do not match');
        header('Location: index.php#signup-form');
        exit();
    }

    if (!$terms) {
        setFlash('signup_error', 'You must agree to the terms and conditions');
        header('Location: index.php#signup-form');
        exit();
    }

    // Check if email already exists
    try {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            setFlash('signup_error', 'Email already registered');
            header('Location: index.php#signup-form');
            exit();
        }

        // Hash password
        $hashed_password = hashPassword($password);

        // Insert new user
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $hashed_password]);

        // Get new user ID
        $user_id = $pdo->lastInsertId();

        // Set session
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;

        // Redirect to dashboard
        setFlash('success', 'Registration successful! Welcome to WhatsApp BOT Creator');
        header('Location: dashboard.php');
        exit();

    } catch(PDOException $e) {
        setFlash('signup_error', 'Database error: ' . $e->getMessage());
        header('Location: index.php#signup-form');
        exit();
    }
} else {
    header('Location: index.php');
    exit();
}
?>