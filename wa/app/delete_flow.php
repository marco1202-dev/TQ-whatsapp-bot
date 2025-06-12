<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

session_start();
redirectIfNotLoggedIn();

header('Content-Type: application/json');

try {
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data || empty($data['id'])) {
        throw new Exception('Flow ID is required');
    }

    // Delete the flow
    $stmt = $pdo->prepare("DELETE FROM flows WHERE id = ? AND user_id = ?");
    $stmt->execute([$data['id'], $_SESSION['user_id']]);

    if ($stmt->rowCount() === 0) {
        throw new Exception('Flow not found or you do not have permission to delete it');
    }

    echo json_encode([
        'success' => true,
        'message' => 'Flow deleted successfully'
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}