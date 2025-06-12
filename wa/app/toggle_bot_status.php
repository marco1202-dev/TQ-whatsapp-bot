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

    try {
        // Get current status
        $stmt = $pdo->prepare("SELECT status FROM bots WHERE id = ? AND user_id = ?");
        $stmt->execute([$botId, $userId]);
        $bot = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$bot) {
            setFlash('error', 'Bot not found');
            header('Location: dashboard.php');
            exit();
        }

        $newStatus = $bot['status'] === 'active' ? 'inactive' : 'active';
        
        $updateStmt = $pdo->prepare("UPDATE bots SET status = ? WHERE id = ?");
        $updateStmt->execute([$newStatus, $botId]);
        
        setFlash('success', 'Bot status updated successfully');
    } catch(PDOException $e) {
        setFlash('error', 'Error updating status: ' . $e->getMessage());
    }
    
    header('Location: dashboard.php');
    exit();
}
?>