<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

session_start();
redirectIfNotLoggedIn();

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $userId = $_SESSION['user_id'];

    if (empty($data['id'])) {
        throw new Exception('Missing flow ID');
    }

    // Delete flow only if user owns the bot
    $stmt = $pdo->prepare("DELETE f FROM bot_flows f
                          JOIN bots b ON f.bot_id = b.id
                          WHERE f.id = ? AND b.user_id = ?");
    $stmt->execute([$data['id'], $userId]);

    echo json_encode([
        'success' => $stmt->rowCount() > 0
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}