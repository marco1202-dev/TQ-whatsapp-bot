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
    
    try {
        // Get flow definition
        $stmt = $pdo->prepare("SELECT flow_json FROM bot_flows WHERE bot_id = ? AND is_default = 1");
        $stmt->execute([$botId]);
        $flow = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$flow) {
            error_log("No default flow found for bot_id: " . $botId);
            return null;
        }
        
        $flowData = json_decode($flow['flow_json'], true);
        if (!$flowData) {
            error_log("Invalid flow JSON for bot_id: " . $botId);
            return null;
        }
        
        // Get current flow step
        $currentStep = $flowData[$currentFlow] ?? null;
        if (!$currentStep) {
            error_log("Flow step not found: " . $currentFlow);
            return 'start'; // Default to start if step not found
        }
        
        $messageContent = strtolower(trim($messageContent));
        error_log("Processing message: '" . $messageContent . "' in flow: " . $currentFlow);
        
        // For interactive messages, the messageContent is the button ID (match value)
        if (isset($currentStep['choices'])) {
            foreach ($currentStep['choices'] as $choice) {
                $choiceMatch = strtolower(trim($choice['match']));
                error_log("Checking choice match: '" . $choiceMatch . "' against message: '" . $messageContent . "'");
                
                if ($choiceMatch === $messageContent) {
                    error_log("Found matching choice, going to: " . $choice['goto']);
                    return $choice['goto'];
                }
            }
        }
        
        // If no match found, return default_goto or current flow
        $nextFlow = $currentStep['default_goto'] ?? $currentFlow;
        error_log("No match found, using default_goto: " . $nextFlow);
        return $nextFlow;
        
    } catch (Exception $e) {
        error_log("Error in determineNextFlowStep: " . $e->getMessage());
        return null;
    }
}

function sendFlowMessage($botId, $to, $flowName) {
    global $pdo;
    
    try {
        // Get bot credentials
        $stmt = $pdo->prepare("SELECT fb_access_token, fb_phone_number_id FROM bots WHERE id = ?");
        $stmt->execute([$botId]);
        $bot = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$bot) {
            error_log("Bot not found: " . $botId);
            return false;
        }
        
        // Get flow content
        $stmt = $pdo->prepare("SELECT flow_json FROM bot_flows WHERE bot_id = ? AND is_default = 1");
        $stmt->execute([$botId]);
        $flow = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$flow) {
            error_log("No default flow found for bot_id: " . $botId);
            return false;
        }
        
        $flowData = json_decode($flow['flow_json'], true);
        if (!$flowData) {
            error_log("Invalid flow JSON for bot_id: " . $botId);
            return false;
        }
        
        $currentStep = $flowData[$flowName] ?? null;
        if (!$currentStep) {
            error_log("Flow step not found: " . $flowName);
            return false;
        }
        
        $messageContent = $currentStep['message'] ?? 'Please select an option';
        error_log("Sending message for flow: " . $flowName . " with content: " . $messageContent);
        
        // Prepare API request
        $url = "https://graph.facebook.com/v22.0/{$bot['fb_phone_number_id']}/messages";
        $headers = [
            "Authorization: Bearer {$bot['fb_access_token']}",
            "Content-Type: application/json"
        ];
        
        // Simple text message if no buttons
        if (empty($currentStep['choices'])) {
            $data = [
                "messaging_product" => "whatsapp",
                "to" => $to,
                "type" => "text",
                "text" => ["body" => $messageContent]
            ];
        } else {
            // Interactive message with buttons
            $buttons = [];
            foreach ($currentStep['choices'] as $choice) {
                $buttons[] = [
                    'type' => 'reply',
                    'reply' => [
                        'id' => $choice['match'],
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
        
        if ($httpCode !== 200) {
            error_log("Failed to send message. HTTP Code: " . $httpCode . ", Response: " . $response);
            curl_close($ch);
            return false;
        }
        
        curl_close($ch);
        
        // Update user session with the CURRENT flow state
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
        
    } catch (Exception $e) {
        error_log("Error in sendFlowMessage: " . $e->getMessage());
        return false;
    }
}
?>