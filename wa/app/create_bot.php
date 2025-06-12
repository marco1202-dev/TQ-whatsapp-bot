<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
redirectIfNotLoggedIn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrfToken($_POST['csrf_token'])) {
        setFlash('error', 'Invalid CSRF token');
        header('Location: dashboard.php');
        exit();
    }

    $userId = $_SESSION['user_id'];
    $botName = sanitize($_POST['business_name']);
    $phoneNumber = sanitize($_POST['phone_number']);
    $accessToken = sanitize($_POST['fb_access_token']);
    $verifyToken = sanitize($_POST['fb_verify_token']);
    $phoneNumberId = sanitize($_POST['fb_phone_number_id']);
    $description = sanitize($_POST['description']);

    try {
        $stmt = $pdo->prepare("INSERT INTO bots 
            (user_id, business_name, phone_number, fb_access_token, fb_verify_token, fb_phone_number_id, description)
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $userId,
            $botName,
            $phoneNumber,
            $accessToken,
            $verifyToken,
            $phoneNumberId,
            $description
        ]);

        setFlash('success', 'Bot created successfully!');
    } catch(PDOException $e) {
        setFlash('error', 'Error creating bot: ' . $e->getMessage());
    }
    
    header('Location: dashboard.php');
    exit();
}
?>