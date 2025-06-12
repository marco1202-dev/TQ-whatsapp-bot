<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
redirectIfNotLoggedIn();

header('Content-Type: application/json');

try {
    $botId = $_GET['bot_id'] ?? null;
    $userId = $_SESSION['user_id'];
    $limit = $_GET['limit'] ?? 50;

    // Verify bot ownership
    $stmt = $pdo->prepare("SELECT id FROM bots WHERE id = ? AND user_id = ?");
    $stmt->execute([$botId, $userId]);
    if (!$stmt->fetch()) {
        throw new Exception('Invalid bot selection');
    }

    // Get messages
    $stmt = $pdo->prepare("SELECT * FROM messages 
                          WHERE bot_id = ? 
                          ORDER BY timestamp DESC 
                          LIMIT ?");
    $stmt->execute([$botId, $limit]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'messages' => $messages
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>