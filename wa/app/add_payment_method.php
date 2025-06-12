<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
redirectIfNotLoggedIn();

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $userId = $_SESSION['user_id'];

    $fields = [
        'user_id' => $userId,
        'method_type' => $data['methodType']
    ];

    if ($data['methodType'] === 'paypal') {
        if (!filter_var($data['paypalEmail'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid PayPal email address');
        }
        $fields['paypal_email'] = $data['paypalEmail'];
    } else {
        // Encrypt sensitive data
        $encrypt = function($value) use ($userId) {
            return openssl_encrypt($value, 'AES-256-CBC', getEncryptionKey($userId), 0, substr(md5($userId), 0, 16));
        };

        $fields['card_number'] = $encrypt(str_replace(' ', '', $data['cardNumber']));
        $fields['expiry_date'] = $encrypt($data['expiryDate']);
        $fields['cvv'] = $encrypt($data['cvv']);
    }

    $columns = implode(', ', array_keys($fields));
    $values = implode(', ', array_fill(0, count($fields), '?'));
    
    $stmt = $pdo->prepare("INSERT INTO payment_methods ($columns) VALUES ($values)");
    $stmt->execute(array_values($fields));

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

function getEncryptionKey($userId) {
    // Implement secure key management (e.g., use environment variables)
    return hash('sha256', 'your-secret-key' . $userId);
}