<?php
require_once 'config.php';

// Redirect if already logged in
function redirectIfLoggedIn() {
    if (isset($_SESSION['user_id'])) {
        header('Location: dashboard.php');
        exit();
    }
}

// Redirect if not logged in
function redirectIfNotLoggedIn() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php');
        exit();
    }
}

// Sanitize input data
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Validate email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Validate password strength
function validatePassword($password) {
    return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password);
}

// Hash password
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

// Verify password
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Set flash message
function setFlash($name, $message) {
    $_SESSION[$name] = $message;
}

// Show flash message
function showFlash($name) {
    if (isset($_SESSION[$name])) {
        $message = $_SESSION[$name];
        unset($_SESSION[$name]);
        
        $alertClass = 'alert-info';
        if ($name === 'error' || $name === 'signup_error') $alertClass = 'alert-danger';
        if ($name === 'success') $alertClass = 'alert-success';
        
        echo '<div class="alert '.$alertClass.' alert-dismissible fade show" role="alert">';
        echo $message;
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
    }
}

// Generate CSRF token
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Validate CSRF token
function validateCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}



function getCurrentSubscription($userId) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT 
                sp.name,
                sp.price,
                us.start_date,
                us.end_date,
                us.payment_status,
                DATEDIFF(us.end_date, CURDATE()) as days_remaining,
                CASE 
                    WHEN us.end_date > NOW() THEN 'active'
                    ELSE 'expired'
                END as status
            FROM user_subscriptions us
            JOIN subscription_plans sp ON us.plan_id = sp.id
            WHERE us.user_id = ?
            ORDER BY us.end_date DESC
            LIMIT 1
        ");
        $stmt->execute([$userId]);
        
        $subscription = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$subscription) {
            initializeUserSubscription($userId);
            return getCurrentSubscription($userId);
        }
        
        // Convert dates to DateTime objects
        $subscription['start_date'] = new DateTime($subscription['start_date']);
        $subscription['end_date'] = new DateTime($subscription['end_date']);
        
        return $subscription;
        
    } catch (PDOException $e) {
        error_log("Subscription Error: " . $e->getMessage());
        return null;
    }
}



function initializeUserSubscription($userId) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // Get default plan
        $stmt = $pdo->prepare("SELECT id FROM subscription_plans WHERE is_default = 1");
        $stmt->execute();
        $defaultPlan = $stmt->fetch();
        
        // Calculate dates
        $startDate = new DateTime();
        $endDate = (new DateTime())->modify('+15 days');
        
        // Insert subscription
        $stmt = $pdo->prepare("INSERT INTO user_subscriptions 
            (user_id, plan_id, start_date, end_date, payment_status)
            VALUES (?, ?, ?, ?, 'active')");
        $stmt->execute([
            $userId,
            $defaultPlan['id'],
            $startDate->format('Y-m-d H:i:s'),
            $endDate->format('Y-m-d H:i:s')
        ]);
        
        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Subscription init error: " . $e->getMessage());
    }
}

function getSubscriptionPlans($userId) {
    global $pdo;
    
    try {
        // Get current plan ID
        $currentPlanStmt = $pdo->prepare("
            SELECT plan_id 
            FROM user_subscriptions 
            WHERE user_id = ?
            ORDER BY end_date DESC 
            LIMIT 1
        ");
        $currentPlanStmt->execute([$userId]);
        $currentPlan = $currentPlanStmt->fetch();
        $currentPlanId = $currentPlan ? $currentPlan['plan_id'] : null;

        // Get all plans
        $stmt = $pdo->query("SELECT * FROM subscription_plans");
        $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Add current plan flag and format features
        return array_map(function($plan) use ($currentPlanId) {
            $plan['is_current'] = ($plan['id'] == $currentPlanId);
            $plan['features_html'] = generateFeaturesList($plan['features']);
            return $plan;
        }, $plans);

    } catch (PDOException $e) {
        error_log("Plan Error: " . $e->getMessage());
        return [];
    }
}

function generateFeaturesList($features) {
    $featureMap = [
        'Team Member' => 'fas fa-user',
        'Subscribers' => 'fas fa-users',
        'AI Tokens' => 'fas fa-brain',
        'Broadcast' => 'fas fa-bullhorn',
        'Live Chat' => 'fas fa-comments',
        'Markup Fees' => 'fas fa-percentage',
        'Message Credits' => 'fas fa-envelope',
        'Campaigns' => 'fas fa-chart-line',
        'Webhook' => 'fas fa-plug',
        'WhatsApp' => 'fab fa-whatsapp',
        'Dedicated Manager' => 'fas fa-headset',
        'Custom Integration' => 'fas fa-puzzle-piece',
        'Priority Support' => 'fas fa-life-ring'
    ];
    
    $items = explode(',', $features);
    return implode('', array_map(function($item) use ($featureMap) {
        $item = trim($item);
        $icon = 'fas fa-check-circle'; // default icon
        foreach ($featureMap as $key => $value) {
            if (stripos($item, $key) !== false) { // Fixed syntax error here
                $icon = $value;
                break;
            }
        }
        return '
        <div class="d-flex align-items-center mb-2">
            <i class="'.$icon.' me-2 text-primary"></i>
            <span>'.$item.'</span>
        </div>';
    }, $items));
}
?>