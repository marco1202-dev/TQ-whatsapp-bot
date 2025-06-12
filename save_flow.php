<?php
require_once 'wa/app/includes/config.php';
require_once 'wa/app/includes/functions.php';

// Get JSON data from request
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON data']);
    exit;
}

try {
    // Start transaction
    $pdo->beginTransaction();

    // Save flow data
    $stmt = $pdo->prepare("INSERT INTO flows (name, data, created_at) VALUES (?, ?, NOW())");
    $stmt->execute([
        'New Flow ' . date('Y-m-d H:i:s'),
        json_encode($data)
    ]);

    $flowId = $pdo->lastInsertId();

    // Save nodes
    $stmt = $pdo->prepare("INSERT INTO flow_nodes (flow_id, node_id, type, x, y, content) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($data['nodes'] as $node) {
        $stmt->execute([
            $flowId,
            $node['id'],
            $node['type'],
            $node['x'],
            $node['y'],
            $node['content']
        ]);
    }

    // Save connections
    $stmt = $pdo->prepare("INSERT INTO flow_connections (flow_id, from_node, to_node) VALUES (?, ?, ?)");
    foreach ($data['connections'] as $conn) {
        $stmt->execute([
            $flowId,
            $conn['from'],
            $conn['to']
        ]);
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