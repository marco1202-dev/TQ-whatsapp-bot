<?php
require_once '../app/includes/config.php';
require_once '../app/includes/functions.php';

header('Content-Type: application/json');

// Check if user is logged in

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Flow ID is required']);
    exit;
}

try {
    // Get flow data with user check
    $stmt = $pdo->prepare("
        SELECT f.*, b.business_name as bot_name 
        FROM bot_flows f 
        LEFT JOIN bots b ON f.bot_id = b.id 
        WHERE f.id = ? AND b.user_id = ?
    ");
    $stmt->execute([$_GET['id'], $_SESSION['user_id']]);
    $flow = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$flow) {
        http_response_code(404);
        echo json_encode(['error' => 'Flow not found']);
        exit;
    }

    // Parse the flow JSON data
    $flowData = json_decode($flow['flow_json'], true);

    echo json_encode([
        'id' => $flow['id'],
        'name' => $flow['flow_name'],
        'bot_id' => $flow['bot_id'],
        'bot_name' => $flow['bot_name'],
        'nodes' => $flowData['nodes'] ?? [],
        'connections' => $flowData['connections'] ?? [],
        'is_default' => (bool)$flow['is_default']
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Failed to fetch flow',
        'message' => $e->getMessage()
    ]);
} 