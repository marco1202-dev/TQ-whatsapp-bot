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

try {
    $stmt = $pdo->prepare("
        SELECT f.*, b.business_name as bot_name 
        FROM bot_flows f
        INNER JOIN bots b ON f.bot_id = b.id
        WHERE b.user_id = :user_id
        ORDER BY f.created_at DESC
    ");
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
    $flows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($flows);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Failed to fetch flows',
        'message' => $e->getMessage()
    ]);
} 