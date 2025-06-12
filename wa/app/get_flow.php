<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
redirectIfNotLoggedIn();

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $flowId = $data['id'];
    $userId = $_SESSION['user_id'];

    $stmt = $pdo->prepare("SELECT f.* FROM bot_flows f
                          JOIN bots b ON f.bot_id = b.id
                          WHERE f.id = ? AND b.user_id = ?");
    $stmt->execute([$flowId, $userId]);

    if ($flow = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $flow['flow_json'] = json_decode($flow['flow_json']);
        echo json_encode(['success' => true, 'flow' => $flow]);
    } else {
        throw new Exception('Flow not found');
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}