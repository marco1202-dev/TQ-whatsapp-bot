<?php
require_once '../app/includes/config.php';
require_once '../app/includes/functions.php';

// Check if user is logged in

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Get JSON data from request
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON data']);
    exit;
}

if (!isset($data['name']) || !isset($data['bot_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Flow name and bot ID are required']);
    exit;
}

try {
    // Verify that the bot belongs to the user
    $stmt = $pdo->prepare("SELECT id FROM bots WHERE id = ? AND user_id = ?");
    $stmt->execute([$data['bot_id'], $_SESSION['user_id']]);
    $bot = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$bot) {
        http_response_code(403);
        echo json_encode(['error' => 'You do not have permission to access this bot']);
        exit;
    }

    // Start transaction
    $pdo->beginTransaction();

    // Prepare flow JSON data
    $flowJson = [
        'nodes' => $data['nodes'],
        'connections' => $data['connections']
    ];

    if (isset($data['id'])) {
        // Verify that the flow belongs to one of the user's bots
        $stmt = $pdo->prepare("
            SELECT f.id 
            FROM bot_flows f 
            JOIN bots b ON f.bot_id = b.id 
            WHERE f.id = ? AND b.user_id = ?
        ");
        $stmt->execute([$data['id'], $_SESSION['user_id']]);
        $flow = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$flow) {
            http_response_code(403);
            echo json_encode(['error' => 'You do not have permission to edit this flow']);
            exit;
        }

        // Update existing flow
        $stmt = $pdo->prepare("UPDATE bot_flows SET flow_name = ?, flow_json = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND bot_id = ?");
        $stmt->execute([
            $data['name'],
            json_encode($flowJson),
            $data['id'],
            $data['bot_id']
        ]);

        $flowId = $data['id'];
    } else {
        // Create new flow
        $stmt = $pdo->prepare("INSERT INTO bot_flows (bot_id, flow_name, flow_json, created_at) VALUES (?, ?, ?, CURRENT_TIMESTAMP)");
        $stmt->execute([
            $data['bot_id'],
            $data['name'],
            json_encode($flowJson)
        ]);

        $flowId = $pdo->lastInsertId();
    }

    // Commit transaction
    $pdo->commit();

    // Return success response
    echo json_encode([
        'success' => true,
        'flow_id' => $flowId
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    $pdo->rollBack();
    
    http_response_code(500);
    echo json_encode([
        'error' => 'Failed to save flow',
        'message' => $e->getMessage()
    ]);
} 