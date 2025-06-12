<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

session_start();
redirectIfNotLoggedIn();

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $userId = $_SESSION['user_id'];

    // Validate input
    $required = ['bot_id', 'flow_name', 'flow_json'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }

    // Validate JSON
    json_decode($data['flow_json']);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON format');
    }

    // Check bot ownership
    $stmt = $pdo->prepare("SELECT id FROM bots WHERE id = ? AND user_id = ?");
    $stmt->execute([$data['bot_id'], $userId]);
    if (!$stmt->fetch()) {
        throw new Exception('Invalid bot selection');
    }

    // Prepare data
    $flowData = [
        'bot_id' => $data['bot_id'],
        'flow_name' => $data['flow_name'],
        'flow_json' => $data['flow_json'],
        'updated_at' => date('Y-m-d H:i:s')
    ];

    if (empty($data['id'])) {
        // Create new
        $stmt = $pdo->prepare("INSERT INTO bot_flows SET " . 
            implode('=?, ', array_keys($flowData)) . "=?");
        $stmt->execute(array_values($flowData));
    } else {
        // Update existing
        $stmt = $pdo->prepare("UPDATE bot_flows SET " . 
            implode('=?, ', array_keys($flowData)) . "=? WHERE id=?");
        $stmt->execute(array_merge(array_values($flowData), [$data['id']]));
    }

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}