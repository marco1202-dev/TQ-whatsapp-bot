<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
redirectIfNotLoggedIn();

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $userId = $_SESSION['user_id'];

    $stmt = $pdo->prepare("DELETE FROM payment_methods WHERE id = ? AND user_id = ?");
    $stmt->execute([$data['methodId'], $userId]);

    echo json_encode(['success' => $stmt->rowCount() > 0]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}