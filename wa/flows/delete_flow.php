<?php
require_once '../app/includes/config.php';
require_once '../app/includes/functions.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Get JSON data from request
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Flow ID is required']);
    exit;
}

try {
    // Start transaction
    $pdo->beginTransaction();

    // First verify that the flow belongs to one of the user's bots
    $stmt = $pdo->prepare("
        SELECT f.id 
        FROM bot_flows f
        INNER JOIN bots b ON f.bot_id = b.id
        WHERE f.id = :flow_id AND b.user_id = :user_id
    ");
    
    $stmt->execute([
        'flow_id' => $data['id'],
        'user_id' => $_SESSION['user_id']
    ]);

    if (!$stmt->fetch()) {
        throw new Exception('Flow not found or you do not have permission to delete it');
    }

    // Delete the flow
    $stmt = $pdo->prepare("DELETE FROM bot_flows WHERE id = :id");
    $stmt->execute(['id' => $data['id']]);

    // Commit transaction
    $pdo->commit();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    // Rollback transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} 