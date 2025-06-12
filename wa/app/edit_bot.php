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

    $botId = $_POST['bot_id'];
    $userId = $_SESSION['user_id'];
    
    // Validate bot ownership
    try {
        $stmt = $pdo->prepare("SELECT * FROM bots WHERE id = ? AND user_id = ?");
        $stmt->execute([$botId, $userId]);
        if ($stmt->rowCount() === 0) {
            setFlash('error', 'Bot not found');
            header('Location: dashboard.php');
            exit();
        }
    } catch(PDOException $e) {
        setFlash('error', 'Database error: ' . $e->getMessage());
        header('Location: dashboard.php');
        exit();
    }

    $botName = sanitize($_POST['bot_name']);
    $phoneNumber = sanitize($_POST['phone_number']);
    $accessToken = sanitize($_POST['fb_access_token']);
    $verifyToken = sanitize($_POST['fb_verify_token']);
    $phoneNumberId = sanitize($_POST['fb_phone_number_id']);
    $description = sanitize($_POST['description']);

    try {
        $stmt = $pdo->prepare("UPDATE bots SET
            business_name = ?,
            phone_number = ?,
            fb_access_token = ?,
            fb_verify_token = ?,
            fb_phone_number_id = ?,
            description = ?
            WHERE id = ? AND user_id = ?");
        
        $stmt->execute([
            $botName,
            $phoneNumber,
            $accessToken,
            $verifyToken,
            $phoneNumberId,
            $description,
            $botId,
            $userId
        ]);

        setFlash('success', 'Bot updated successfully!');
    } catch(PDOException $e) {
        setFlash('error', 'Error updating bot: ' . $e->getMessage());
    }
    
    header('Location: dashboard.php');
    exit();
}
?>