<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

session_start();
redirectIfNotLoggedIn();

header('Content-Type: application/json');

try {
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        throw new Exception('Invalid data received');
    }

    // Validate required fields
    if (empty($data['name']) || empty($data['bot_id'])) {
        throw new Exception('Name and bot are required');
    }

    // Check if this is a new flow or an update
    if (!empty($data['id'])) {
        // Update existing flow
        $stmt = $pdo->prepare("
            UPDATE flows 
            SET name = ?, bot_id = ?, flow_data = ?, updated_at = NOW()
            WHERE id = ? AND user_id = ?
        ");
        $stmt->execute([
            $data['name'],
            $data['bot_id'],
            json_encode($data),
            $data['id'],
            $_SESSION['user_id']
        ]);
        
        $flowId = $data['id'];
    } else {
        // Create new flow
        $stmt = $pdo->prepare("
            INSERT INTO flows (user_id, bot_id, name, flow_data, created_at, updated_at)
            VALUES (?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([
            $_SESSION['user_id'],
            $data['bot_id'],
            $data['name'],
            json_encode($data)
        ]);
        
        $flowId = $pdo->lastInsertId();
    }

    echo json_encode([
        'success' => true,
        'message' => 'Flow saved successfully',
        'flow_id' => $flowId
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}