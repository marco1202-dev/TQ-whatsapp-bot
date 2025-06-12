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
        $stmt = $pdo->prepare("DELETE FROM bots WHERE id = ? AND user_id = ?");
        $stmt->execute([$botId, $userId]);
        
        if ($stmt->rowCount() > 0) {
            setFlash('success', 'Bot deleted successfully');
        } else {
            setFlash('error', 'Bot not found or permission denied');
        }
    } catch(PDOException $e) {
        setFlash('error', 'Error deleting bot: ' . $e->getMessage());
    }
    
    header('Location: dashboard.php');
    exit();
}
?>