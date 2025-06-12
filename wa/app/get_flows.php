<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

session_start();
redirectIfNotLoggedIn();

header('Content-Type: application/json');

try {
    // Get all flows for the current user
    $stmt = $pdo->prepare("
        SELECT f.*, b.business_name as bot_name 
        FROM flows f 
        LEFT JOIN bots b ON f.bot_id = b.id 
        WHERE f.user_id = ? 
        ORDER BY f.updated_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $flows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format the flows for the frontend
    $formattedFlows = array_map(function($flow) {
        return [
            'id' => $flow['id'],
            'name' => $flow['name'],
            'bot_name' => $flow['bot_name'],
            'created_at' => $flow['created_at'],
            'updated_at' => $flow['updated_at']
        ];
    }, $flows);

    echo json_encode([
        'success' => true,
        'flows' => $formattedFlows
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}