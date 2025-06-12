<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
redirectIfNotLoggedIn();

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $userId = $_SESSION['user_id'];
    $planId = $data['plan_id'];
    $paymentDetails = $data['payment_details'];

    // Verify payment with PayPal
    $paymentStatus = verifyPayPalPayment($paymentDetails['id']);

    if (!$paymentStatus->verified) {
        throw new Exception('Payment verification failed');
    }

    // Update subscription
    $pdo->beginTransaction();

    // Record payment
    $stmt = $pdo->prepare("INSERT INTO payments 
        (user_id, plan_id, amount, payment_date, payment_method, transaction_id, status)
        VALUES (?, ?, ?, NOW(), 'paypal', ?, 'completed')");
    $stmt->execute([
        $userId,
        $planId,
        $paymentStatus->amount,
        $paymentDetails['id']
    ]);

    // Update user subscription
    $endDate = date('Y-m-d H:i:s', strtotime('+30 days'));
    
    $stmt = $pdo->prepare("UPDATE user_subscriptions 
        SET plan_id = ?, 
            start_date = NOW(),
            end_date = ?,
            payment_status = 'active'
        WHERE user_id = ?");
    $stmt->execute([$planId, $endDate, $userId]);

    $pdo->commit();

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

function verifyPayPalPayment($paymentId) {
    $client = new \GuzzleHttp\Client();
    $response = $client->request('GET', 
        "https://api.paypal.com/v2/checkout/orders/$paymentId", [
            'headers' => [
                'Authorization' => 'Bearer ' . getPayPalAccessToken(),
                'Content-Type' => 'application/json'
            ]
        ]);

    $data = json_decode($response->getBody());
    
    return (object)[
        'verified' => $data->status === 'COMPLETED',
        'amount' => $data->purchase_units[0]->amount->value
    ];
}