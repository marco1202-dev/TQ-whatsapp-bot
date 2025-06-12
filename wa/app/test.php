<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
header('Content-Type: application/json');

try {
    // Test with hardcoded bot ID that you know has messages
    $testBotId = 1; // CHANGE THIS TO A KNOWN BOT ID
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM messages WHERE bot_id = ?");
    $stmt->execute([$testBotId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'message_count' => $result['count'],
        'query' => "SELECT COUNT(*) as count FROM messages WHERE bot_id = $testBotId"
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}