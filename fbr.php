<?php
require_once 'wa/app/includes/config.php';
require_once 'wa/app/includes/functions.php';

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
            continue;
        }

        $message = $entry['changes'][0]['value']['messages'][0] ?? null;
        if ($message) {
            processIncomingMessage($bot['id'], $message);
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

function processIncomingMessage($botId, $message) {
    global $pdo;
    
    // Save message to database
    $stmt = $pdo->prepare("INSERT INTO messages 
        (bot_id, message_id, from_number, content, type, timestamp)
        VALUES (?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $botId,
        $message['id'],
        $message['from'],
        $message['text']['body'] ?? '[media]',
        $message['type'],
        date('Y-m-d H:i:s', $message['timestamp'])
    ]);
    
    // Process flow
    $currentFlow = getCurrentFlow($botId, $message['from']);
    $nextStep = determineNextFlowStep($botId, $currentFlow, $message);
    
    if ($nextStep) {
        sendFlowMessage($botId, $message['from'], $nextStep);
    }
}

?>