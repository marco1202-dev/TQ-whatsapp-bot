<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!validateCsrfToken($_POST['csrf_token'])) {
        setFlash('login_error', 'Invalid CSRF token');
        header('Location: index.php');
        exit();
    }

    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']) ? true : false;

    // Validate inputs
    // Change all setFlash('error') to setFlash('login_error')
if (empty($email) || empty($password)) {
    setFlash('login_error', 'Please fill in all fields');
    header('Location: index.php');
    exit();
}

// Similarly update all other error messages in login.php to use 'login_error'

    if (!validateEmail($email)) {
        setFlash('login_error', 'Please enter a valid email address');
        header('Location: index.php');
        exit();
    }

    // Check if user exists
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !verifyPassword($password, $user['password'])) {
            setFlash('login_error', 'Invalid email or password');
            header('Location: index.php');
            exit();
        }

        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];

        // Set remember me cookie if checked
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $expiry = time() + 60 * 60 * 24 * 30; // 30 days
            
            setcookie('remember_token', $token, $expiry, '/');
            
            // Store token in database
            $stmt = $pdo->prepare("UPDATE users SET remember_token = ?, token_expiry = ? WHERE id = ?");
            $stmt->execute([$token, date('Y-m-d H:i:s', $expiry), $user['id']]);
        }

        // Redirect to dashboard
        setFlash('success', 'Login successful!');
        header('Location: dashboard.php');
        exit();

    } catch(PDOException $e) {
        setFlash('login_error', 'Database error: ' . $e->getMessage());
        header('Location: index.php');
        exit();
    }
} else {
    header('Location: index.php');
    exit();
}
?>