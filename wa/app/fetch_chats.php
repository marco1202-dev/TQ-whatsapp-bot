<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
redirectIfNotLoggedIn();

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $data = json_decode(file_get_contents('php://input'), true);
    $botId = $data['bot_id'] ?? null;
    $userId = $_SESSION['user_id'];

    // Get messages from database
    if ($botId) {
        // Validate bot ownership
        $stmt = $pdo->prepare("SELECT id, business_name FROM bots WHERE id = ? AND user_id = ?");
        $stmt->execute([$botId, $userId]);
        $bot = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$bot) {
            throw new Exception('Invalid bot selection');
        }

        // Get messages for specific bot
        $query = "SELECT m.id, m.message_id, m.from_number, m.content, m.type, m.direction, m.timestamp, 
                  b.business_name as bot_name
                  FROM messages m
                  JOIN bots b ON m.bot_id = b.id
                  WHERE m.bot_id = ?
                  ORDER BY m.timestamp DESC
                  LIMIT 100";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$botId]);
    } else {
        // Get all recent messages for user
        $query = "SELECT m.id, m.message_id, m.from_number, m.content, m.type, m.direction, m.timestamp, 
                 b.business_name as bot_name
                 FROM messages m
                 JOIN bots b ON m.bot_id = b.id
                 WHERE b.user_id = ?
                 ORDER BY m.timestamp DESC
                 LIMIT 100";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$userId]);
    }

    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format messages for consistent response
    $formattedMessages = [];
    foreach ($messages as $msg) {
        $formattedMessages[] = [
            'id' => $msg['message_id'],
            'from' => $msg['from_number'],
            'text' => $msg['content'],
            'type' => $msg['type'],
            'direction' => $msg['direction'],
            'timestamp' => $msg['timestamp'],
            'bot_name' => $msg['bot_name']
        ];
    }

    echo json_encode([
        'success' => true,
        'messages' => $formattedMessages
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'debug' => [
            'bot_id' => $botId ?? null,
            'user_id' => $userId ?? null
        ]
    ]);
}