<?php
require_once 'wa/app/includes/config.php';
require_once 'wa/app/includes/functions.php';
include 'wa/app/flow_processor.php';

$payload = file_get_contents('php://input');
$data = json_decode($payload, true);

// Verify webhook
if (!empty($_GET['hub_verify_token'])) {
    $botId = verifyWebhookToken($_GET['hub_verify_token']);
    if ($botId) {
        echo $_GET['hub_challenge'];
        exit;
    }
    http_response_code(403);
    exit;
}

// Process incoming messages
if ($data['object'] === 'whatsapp_business_account') {
    foreach ($data['entry'] as $entry) {
        $phoneNumberId = $entry['changes'][0]['value']['metadata']['phone_number_id'];
        $bot = getBotByPhoneNumberId($phoneNumberId);
        
        if (!$bot) {
            error_log("Bot not found for phone_number_id: " . $phoneNumberId);
            continue;
        }

        $message = $entry['changes'][0]['value']['messages'][0] ?? null;
        if ($message) {
            // Log incoming message for debugging
            error_log("Incoming message: " . json_encode($message));
            
            // Extract message content based on type
            $messageContent = '';
            $messageType = $message['type'];
            
            switch ($messageType) {
                case 'text':
                    $messageContent = $message['text']['body'];
                    break;
                case 'interactive':
                    $messageContent = $message['interactive']['button_reply']['title'] ?? 
                                    $message['interactive']['list_reply']['title'] ?? 
                                    '[interactive]';
                    break;
                default:
                    $messageContent = '[' . $messageType . ']';
            }
            
            processIncomingMessage($bot['id'], $message, $messageContent);
        }
    }
    http_response_code(200);
    exit;
}

http_response_code(404);

// Helper functions
function verifyWebhookToken($token) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id FROM bots WHERE fb_verify_token = ?");
    $stmt->execute([$token]);
    return $stmt->fetchColumn();
}

function getBotByPhoneNumberId($phoneNumberId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM bots WHERE fb_phone_number_id = ?");
    $stmt->execute([$phoneNumberId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function processIncomingMessage($botId, $message, $messageContent) {
    global $pdo;
    
    try {
        // // Save message to database
        // $stmt = $pdo->prepare("INSERT INTO messages 
        //     (bot_id, message_id, from_number, content, type, timestamp)
        //     VALUES (?, ?, ?, ?, ?, ?)");
        
        // $stmt->execute([
        //     $botId,
        //     $message['id'],
        //     $message['from'],
        //     $messageContent,
        //     $message['type'],
        //     date('Y-m-d H:i:s', $message['timestamp'])
        // ]);
        
        // // Process flow
        // // $currentFlow = getCurrentFlow($botId, $message['from']);
        // // error_log("Current flow for {$message['from']}: " . $currentFlow);
        
        // // $nextStep = determineNextFlowStep($botId, $currentFlow, $messageContent);
        // // error_log("Next step determined: " . ($nextStep ?? 'null'));
        
        // // if ($nextStep) {
        // //     sendFlowMessage($botId, $message['from'], $nextStep);
        // // }

        // $stmt = $pdo->prepare("SELECT fb_access_token, fb_phone_number_id FROM bots WHERE id = ?");
        // $stmt->execute([$botId]);
        // $bot = $stmt->fetch(PDO::FETCH_ASSOC);

        // $url = "https://graph.facebook.com/v22.0/{$bot['fb_phone_number_id']}/messages";
        // $headers = [
        //     "Authorization: Bearer {$bot['fb_access_token']}",
        //     "Content-Type: application/json"
        // ];

        // $data = [
        //     "messaging_product" => "whatsapp",
        //     "to" => $to,
        //     "type" => "text",
        //     "text" => ["body" => "Hi, How can I help you?"]
        // ];

        // $ch = curl_init();
        // curl_setopt_array($ch, [
        //     CURLOPT_URL => $url,
        //     CURLOPT_HTTPHEADER => $headers,
        //     CURLOPT_POST => true,
        //     CURLOPT_POSTFIELDS => json_encode($data),
        //     CURLOPT_RETURNTRANSFER => true,
        // ]);
        
        // $response = curl_exec($ch);
        // $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        // if ($httpCode !== 200) {
        //     error_log("Failed to send message. HTTP Code: " . $httpCode . ", Response: " . $response);
        //     curl_close($ch);
        //     return false;
        // }
        
        // curl_close($ch);

        $url = "https://graph.facebook.com/v22.0/640983692435947/messages";
        $headers = [
            "Authorization: Bearer EAAPREqtct0kBO4oRGjkZBix0BJIEFrBSjr0dEAOjujsybfUIxOo7GgcFiZBZChnOSm1blpFbZAdNIi1Q0RzaGvLpjjSowXrvZA21hgJzQazWrZAt3W6K5ML3iLqnqtAlDZBx6w0s8Br3Qy0DKgnkWO4g7SPWXcI73PWAg350NzXUb5J0OiPNhWaiSVmO5S8jFuHGGxZB36RZANB7PBzRaZBi6ics3YeI1WxEVfNiZA7PkOhz8hYevsZD",
            "Content-Type: application/json"
        ];

        $data = [
            "messaging_product" => "whatsapp",
            "to" => "529991987791",  // fixed here
            "type" => "text",
            "text" => ["body" => "Hi, How can I help you?"]
        ];

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

    } catch (Exception $e) {
        error_log("Error processing message: " . $e->getMessage());
    }
}

?>