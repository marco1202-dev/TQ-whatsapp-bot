<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

session_start();
redirectIfNotLoggedIn();

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $botId = $data['bot_id'] ?? null;
    $userId = $_SESSION['user_id'];

    $query = "SELECT 
                f.id, f.flow_name, f.flow_json, f.is_default, f.updated_at,
                b.id as bot_id, b.business_name
              FROM bot_flows f
              JOIN bots b ON f.bot_id = b.id
              WHERE b.user_id = :user_id";
    
    $params = [':user_id' => $userId];

    if ($botId) {
        $query .= " AND f.bot_id = :bot_id";
        $params[':bot_id'] = $botId;
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);

    $flows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'flows' => $flows
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}