<?php
require_once 'includes/config.php';

function getCurrentFlow($botId, $phoneNumber) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT current_flow FROM user_sessions 
                          WHERE bot_id = ? AND phone_number = ?");
    $stmt->execute([$botId, $phoneNumber]);
    return $stmt->fetchColumn() ?: 'start';
}

function determineNextFlowStep($botId, $currentFlow, $messageContent) {
    global $pdo;
    
    // Get flow definition
    $stmt = $pdo->prepare("SELECT flow_json FROM bot_flows 
                          WHERE bot_id = ? AND flow_name = ?");
    $stmt->execute([$botId, $currentFlow]);
    $flow = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$flow) return null;
    
    $flowData = json_decode($flow['flow_json'], true);
    $messageContent = strtolower(trim($messageContent));
    
    // Check for exact matches first
    if (isset($flowData['choices'])) {
        foreach ($flowData['choices'] as $choice) {
            if (strtolower(trim($choice['match'])) === $messageContent) {
                return $choice['goto'];
            }
        }
    }
    
    // Check for partial matches
    if (isset($flowData['choices'])) {
        foreach ($flowData['choices'] as $choice) {
            if (strpos($messageContent, strtolower(trim($choice['match']))) !== false) {
                return $choice['goto'];
            }
        }
    }
    
    return $flowData['default_goto'] ?? $currentFlow;
}

function sendFlowMessage($botId, $to, $flowName) {
    global $pdo;
    
    // Get bot credentials
    $stmt = $pdo->prepare("SELECT fb_access_token, fb_phone_number_id FROM bots WHERE id = ?");
    $stmt->execute([$botId]);
    $bot = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$bot) return false;
    
    // Get flow content
    $stmt = $pdo->prepare("SELECT flow_json FROM bot_flows 
                          WHERE bot_id = ? AND flow_name = ?");
    $stmt->execute([$botId, $flowName]);
    $flow = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$flow) return false;
    
    $flowData = json_decode($flow['flow_json'], true);
    $messageContent = $flowData['message'] ?? 'Please select an option';
    
    // Prepare API request
    $url = "https://graph.facebook.com/v19.0/{$bot['fb_phone_number_id']}/messages";
    $headers = [
        "Authorization: Bearer {$bot['fb_access_token']}",
        "Content-Type: application/json"
    ];
    
    // Simple text message if no buttons
    if (empty($flowData['choices'])) {
        $data = [
            "messaging_product" => "whatsapp",
            "to" => $to,
            "type" => "text",
            "text" => ["body" => $messageContent]
        ];
    } else {
        // Interactive message with buttons
        $buttons = [];
        foreach ($flowData['choices'] as $choice) {
            $buttons[] = [
                'type' => 'reply',
                'reply' => [
                    'id' => $choice['goto'],
                    'title' => $choice['display']
                ]
            ];
        }
        
        $data = [
            "messaging_product" => "whatsapp",
            "to" => $to,
            "type" => "interactive",
            "interactive" => [
                "type" => "button",
                "body" => ["text" => $messageContent],
                "action" => ["buttons" => $buttons]
            ]
        ];
    }
    
    // Send message
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_RETURNTRANSFER => true,
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        error_log("Failed to send message: " . $response);
        return false;
    }
    
    // Update user session
    $stmt = $pdo->prepare("INSERT INTO user_sessions 
        (bot_id, phone_number, current_flow, last_interaction)
        VALUES (?, ?, ?, NOW())
        ON DUPLICATE KEY UPDATE current_flow = ?, last_interaction = NOW()");
    $stmt->execute([$botId, $to, $flowName, $flowName]);
    
    // Save outgoing message
    $responseData = json_decode($response, true);
    $stmt = $pdo->prepare("INSERT INTO messages 
        (bot_id, message_id, from_number, content, type, direction, timestamp)
        VALUES (?, ?, ?, ?, ?, 'outgoing', NOW())");
    $stmt->execute([
        $botId,
        $responseData['messages'][0]['id'] ?? 'unknown',
        $to,
        $messageContent,
        isset($buttons) ? 'interactive' : 'text',
    ]);
    
    return true;
}
?>