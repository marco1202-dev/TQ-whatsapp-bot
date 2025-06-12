<?php
require_once 'wa/app/includes/config.php';
require_once 'wa/app/includes/functions.php';

header('Content-Type: application/json');

// Check if user is logged in

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT id, business_name as name, phone_number 
        FROM bots 
        WHERE status = 'active' 
        AND user_id = ?
        ORDER BY business_name
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $bots = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($bots);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Failed to fetch bots',
        'message' => $e->getMessage()
    ]);
} 