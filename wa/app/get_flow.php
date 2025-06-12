<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
redirectIfNotLoggedIn();

header('Content-Type: application/json');

try {
    // Get flow ID from request
    $flowId = $_GET['id'] ?? null;
    
    if (!$flowId) {
        throw new Exception('Flow ID is required');
    }

    // Get the flow
    $stmt = $pdo->prepare("
        SELECT f.*, b.business_name as bot_name 
        FROM flows f 
        LEFT JOIN bots b ON f.bot_id = b.id 
        WHERE f.id = ? AND f.user_id = ?
    ");
    $stmt->execute([$flowId, $_SESSION['user_id']]);
    $flow = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$flow) {
        throw new Exception('Flow not found');
    }

    // Parse the flow data
    $flowData = json_decode($flow['flow_data'], true);
    if (!$flowData) {
        throw new Exception('Invalid flow data');
    }

    echo json_encode([
        'success' => true,
        'flow' => [
            'id' => $flow['id'],
            'name' => $flow['name'],
            'bot_id' => $flow['bot_id'],
            'bot_name' => $flow['bot_name'],
            'nodes' => $flowData['nodes'] ?? [],
            'connections' => $flowData['connections'] ?? []
        ]
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}