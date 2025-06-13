<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
redirectIfNotLoggedIn();

$csrfToken = generateCsrfToken();

// Get user's bots
try {
    $stmt = $pdo->prepare("SELECT * FROM bots WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $bots = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $bots = [];
    setFlash('error', 'Failed to load BOTs: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp BOT Creator Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            
            <span><img src="../logo22.png" style="width:132px;"></span>
        </div>
        <ul class="sidebar-menu">
            <li>
                <a onclick="showDash();" style="cursor:pointer;" class="active">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
        <a style="cursor:pointer" onclick="doshowbot();">
        <i class="fas fa-robot"></i>
        <span>My BOTs</span>
    </a>
    </li>
            <li>
                <a style="cursor:pointer" onclick="showchatflow();">
                    <i class="fas fa-project-diagram"></i>
                    <span>Chat Flows</span>
                </a>
            </li>
            <li>
                <a style="cursor:pointer" onclick="showchats();">
                    <i class="fas fa-comments"></i>
                    <span>Chats</span>
                </a>
            </li>
            <li>
                <a style="cursor:pointer" onclick="showsubs();">
                    <i class="fas fa-chart-bar"></i>
                    <span>Subscriptions</span>
                </a>
            </li>
            <li>
                <a style="cursor:pointer" onclick="showsettings();">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </li>
            
            <li>
                <a href="logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
	
	
    <div class="main-content">
        <div class="header">
            <button class="sidebar-toggle d-lg-none">
                <i class="fas fa-bars"></i>
            </button>
            <h2>Dashboard Overview</h2>
            <div class="user-menu">
                <img src="assets/images/user-avatar.jpg" alt="User">
                <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            </div>
        </div>

        <?php showFlash('error'); ?>
        <?php showFlash('success'); ?>
		
		
		<div id="mytabs">

        <!-- Stats Row -->
        <div class="row">
           <div class="col-md-3">
    <div class="card stat-card">
        <i class="fas fa-robot"></i>
        <?php
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM bots WHERE user_id = ? AND status = 'active'");
            $stmt->execute([$_SESSION['user_id']]);
            $activeBots = $stmt->fetchColumn();
        } catch(PDOException $e) {
            $activeBots = 0;
        }
        ?>
        <h3><?php echo $activeBots; ?></h3>
        <p>Active BOTs</p>
    </div>
</div>
            <div class="col-md-3">
                <div class="card stat-card">
                    <i class="fas fa-comment-dots"></i>
					
					    <?php
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*)FROM messages m INNER JOIN bots b ON m.bot_id = b.id   WHERE b.user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $totchats = $stmt->fetchColumn();
        } catch(PDOException $e) {
            $totchats = 0;
        }
        ?>
                    <h3><?php echo $totchats; ?></h3>
                    <p>Total Chats</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card">
                    <i class="fas fa-users"></i>
                    <h3>*Free (15 Days)</h3>
                    <p>Active Plan</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card">
                    <i class="fas fa-check-circle"></i>
                    <h3>84%</h3>
                    <p>Success Rate</p>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        Message Analytics (Last 7 Days)
                    </div>
                    <div class="card-body">
                        <canvas id="messageChart" height="250"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        BOT Performance
                    </div>
                    <div class="card-body">
                        <canvas id="performanceChart" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Chats and BOT Management -->
       

        <!-- Chat Flow Builder -->
        <!-- Add this after the BOT management section -->




<style>
.chat-container {
    max-height: 600px;
    overflow-y: auto;
}
.chat-item {
    transition: all 0.3s ease;
}
.chat-item:hover {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}
.message-content {
    white-space: pre-wrap;
    word-break: break-word;
}
</style>
		</div>
		
		<div class="col-md-6"  id="my-bots-section">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>My BOTs</span>
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createBotModal">
                <i class="fas fa-plus"></i> Add BOT
            </button>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Bot Name</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bots as $bot): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($bot['business_name']); ?></td>
                        <td>
                            <form action="toggle_bot_status.php" method="POST" class="d-inline">
                                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                                <input type="hidden" name="bot_id" value="<?php echo $bot['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-<?php echo $bot['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                    <?php echo ucfirst($bot['status']); ?>
                                </button>
                            </form>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary edit-bot" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editBotModal"
                                data-bot-id="<?php echo $bot['id']; ?>"
                                data-bot-name="<?php echo htmlspecialchars($bot['business_name']); ?>"
                                data-phone-number="<?php echo htmlspecialchars($bot['phone_number']); ?>"
                                data-access-token="<?php echo htmlspecialchars($bot['fb_access_token']); ?>"
                                data-verify-token="<?php echo htmlspecialchars($bot['fb_verify_token']); ?>"
                                data-phone-number-id="<?php echo htmlspecialchars($bot['fb_phone_number_id']); ?>"
                                data-description="<?php echo htmlspecialchars($bot['description']); ?>">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="delete_bot.php" method="POST" class="d-inline">
                                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                                <input type="hidden" name="bot_id" value="<?php echo $bot['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>




<div class="row mt-4" id="chats">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4><i class="fas fa-comments me-2"></i>Chat Management</h4>
                <div class="d-flex gap-2">
                    <select class="form-select" id="selectBot">
                        <option value="">All Messages (Recent First)</option>
                        <?php foreach ($bots as $bot): ?>
                        <option value="<?= $bot['id'] ?>">
                            <?= htmlspecialchars($bot['business_name']) ?> (<?= $bot['status'] ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-primary" id="refreshChats">
                        <i class="fas fa-sync"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="chatContainer" class="chat-container">
                    <div class="text-center py-5" id="loading">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div id="chatList"></div>
                </div>
            </div>
        </div>
    </div>
</div>





<!-- Add this to your dashboard -->
<div class="row mt-4" id="chatflow"  style="d isplay:flex;">
    <!-- <div class="col-md-4"> -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-project-diagram me-2"></i>Chat Flows</h5>
                <!-- <button class="btn btn-sm btn-primary" id="refreshFlows">
                    <i class="fas fa-sync"></i>
                </button> -->
            </div>
           
        </div>
    <!-- </div> -->

    <!-- <div class="col-md-8"> -->
        <div class="card">
            <div class="flow-builder-container" style="height: 600px; position: relative;">
                <!-- <div class="flow-sidebar" style="position: absolute; left: 0; top: 0; width: 250px; height: 100%; background: #128C7E; color: white; padding: 1rem; overflow-y: auto;">
                    <h3 class="text-white mb-4">Nodes</h3>
                    <div class="node-item" draggable="true" data-type="text">Simple Text</div>
                    <div class="node-item" draggable="true" data-type="media">Media Files</div>
                    <div class="node-item" draggable="true" data-type="buttons">Interactive Buttons</div>
                    <div class="node-item" draggable="true" data-type="delay">Time Delay</div>
                    <div class="node-item" draggable="true" data-type="http">HTTP Request</div>
                </div>
                <div id="flowCanvas" class="flow-canvas" style="position: absolute; left: 250px; top: 0; right: 0; height: 100%; background: #ECE5DD; overflow: auto;"></div> -->
                <iframe  src="https://waassist.io/wa/flows/flows.php"  style="width: 100%; height: 100%; border: none;" allowfullscreen> </iframe>
            </div>
        </div>
    <!-- </div> -->
</div>







<?php
// At the top of your dashboard file
$currentSubscription = getCurrentSubscription($_SESSION['user_id']);
$plans = getSubscriptionPlans($_SESSION['user_id']);


?>

<div class="row g-4" id="subscriptions" style="display:none;">
    <?php if (!empty($plans)): ?>
        <?php foreach ($plans as $plan): ?>
        <div class="col-lg-3">
            <div class="card h-100 shadow-sm <?= $plan['is_current'] ? 'border-success' : '' ?>">
                <div class="card-header <?= $plan['is_current'] ? 'bg-success text-white' : 'bg-light' ?>">
                    <h4 class="my-0"><?= htmlspecialchars($plan['name']) ?></h4>
                </div>
                <div class="card-body">
                    <h2 class="card-title">
                        <?php if($plan['price'] > 0): ?>
                            $<?= number_format($plan['price'], 2) ?><small class="text-muted">/month</small>
                        <?php else: ?>
                            <span class="text-success">Free</span>
                        <?php endif; ?>
                    </h2>
                    <div class="plan-features mt-3 mb-4">
                        <?= $plan['features_html'] ?>
                    </div>
                    
                    <?php if($plan['is_current']): ?>
                        <div class="d-grid">
                            <button class="btn btn-success" disabled>
                                <i class="fas fa-check-circle me-2"></i>Active
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="d-grid">
                            <button class="btn btn-primary choose-plan" 
                                    data-plan-id="<?= $plan['id'] ?>"
                                    data-plan-price="<?= $plan['price'] ?>">
                                <?= $plan['price'] > 0 ? 'Upgrade Now' : 'Switch to Free' ?>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-danger">No subscription plans found!</div>
        </div>
    <?php endif; ?>
</div>








<!-- settings.php -->
<div class="container mt-5" id="settings" style="display:none;">
    <h3><i class="fas fa-cog me-2"></i>Account Settings</h3>
    
    <!-- Password Change Form -->
    <div class="card mt-4">
        <div class="card-header">
            <h5>Change Password</h5>
        </div>
        <div class="card-body">
            <form id="passwordForm">
                <div class="mb-3">
                    <label class="form-label">Current Password</label>
                    <input type="password" class="form-control" id="currentPassword" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <input type="password" class="form-control" id="newPassword" required minlength="8">
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" class="form-control" id="confirmPassword" required>
                </div>
                <button type="submit" class="btn btn-primary">Update Password</button>
            </form>
        </div>
    </div>

    <!-- Payment Methods Section -->
    <div class="card mt-4">
        <div class="card-header">
            <h5>Payment Methods</h5>
        </div>
        <div class="card-body">
            <!-- Existing Payment Methods -->
            <div id="paymentMethodsList" class="mb-4">
                <?php foreach ($paymentMethods as $method): ?>
                <div class="card mb-2">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <?php if($method['method_type'] === 'paypal'): ?>
                                <i class="fab fa-paypal me-2"></i>
                                <?= htmlspecialchars($method['paypal_email']) ?>
                            <?php else: ?>
                                <i class="fas fa-credit-card me-2"></i>
                                **** **** **** <?= substr($method['card_number'], -4) ?>
                                (Exp: <?= $method['expiry_date'] ?>)
                            <?php endif; ?>
                        </div>
                        <button class="btn btn-danger btn-sm delete-method" 
                                data-method-id="<?= $method['id'] ?>">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Add New Payment Method -->
            <div class="accordion">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#addPaymentMethod">
                            Add New Payment Method
                        </button>
                    </h2>
                    <div id="addPaymentMethod" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <form id="paymentMethodForm">
                                <div class="mb-3">
                                    <select class="form-select" id="methodType" required>
                                        <option value="">Select Payment Method</option>
                                        <option value="paypal">PayPal</option>
                                        <option value="credit_card">Credit Card</option>
                                    </select>
                                </div>
                                
                                <!-- PayPal Fields -->
                                <div class="paypal-fields" style="display: none;">
                                    <div class="mb-3">
                                        <label class="form-label">PayPal Email</label>
                                        <input type="email" class="form-control" id="paypalEmail">
                                    </div>
                                </div>

                                <!-- Credit Card Fields -->
                                <div class="credit-card-fields" style="display: none;">
                                    <div class="mb-3">
                                        <label class="form-label">Card Number</label>
                                        <input type="text" class="form-control" id="cardNumber" 
                                               pattern="\d{16}" maxlength="16">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Expiry Date (MM/YY)</label>
                                            <input type="text" class="form-control" id="expiryDate" 
                                                   pattern="\d{2}/\d{2}" placeholder="MM/YY">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">CVV</label>
                                            <input type="text" class="form-control" id="cvv" 
                                                   pattern="\d{3}" maxlength="3">
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">Save Payment Method</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




		
		
		
		
    </div>
	
	
	
	
	
	

    <!-- Create BOT Modal -->
    <div class="modal fade" id="createBotModal" tabindex="-1" aria-labelledby="createBotModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createBotModalLabel">Create New WhatsApp BOT</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="create_bot.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="businessName" class="form-label">Bot Name</label>
                            <input type="text" class="form-control" id="businessName" name="business_name" placeholder="Enter your business name" required>
                        </div>
                        <div class="mb-3">
                            <label for="phoneNumber" class="form-label">WhatsApp Business Number</label>
                            <input type="text" class="form-control" id="phoneNumber" name="phone_number" placeholder="+1 234 567 8901" required>
                        </div>
                        <div class="mb-3">
                            <label for="apiKey" class="form-label">Meta Access Token</label>
                            <input type="text" class="form-control" id="apiKey" name="fb_access_token" placeholder="Enter your Meta API key" required>
                        </div>
						
						 <div class="mb-3">
                            <label for="apiKey" class="form-label">Meta Verify Token</label>
                            <input type="text" class="form-control" id="apiKey" name="fb_verify_token" placeholder="Enter your Meta verify Token" required>
                        </div>
						
						
						 <div class="mb-3">
                            <label for="apiKey" class="form-label">Meta Phone Number Id </label>
                            <input type="text" class="form-control" id="apiKey" name="fb_phone_number_id" placeholder="Meta Phone Number Id " required>
                        </div>
						
                        <div class="mb-3">
                            <label for="businessDesc" class="form-label"> Description(optional)</label>
                            <textarea class="form-control" id="businessDesc" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create BOT</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
	
	
	
	
	<!-- Edit Bot Modal -->
<div class="modal fade" id="editBotModal" tabindex="-1" aria-labelledby="editBotModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editBotModalLabel">Edit WhatsApp BOT</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="edit_bot.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                <input type="hidden" name="bot_id" id="editBotId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editBotName" class="form-label">Bot Name</label>
                        <input type="text" class="form-control" id="editBotName" name="bot_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="editPhoneNumber" class="form-label">WhatsApp Business Number</label>
                        <input type="text" class="form-control" id="editPhoneNumber" name="phone_number" required>
                    </div>
                    <div class="mb-3">
                        <label for="editFbAccessToken" class="form-label">Facebook Access Token</label>
                        <input type="text" class="form-control" id="editFbAccessToken" name="fb_access_token" required>
                    </div>
                    <div class="mb-3">
                        <label for="editFbVerifyToken" class="form-label">Facebook Verify Token</label>
                        <input type="text" class="form-control" id="editFbVerifyToken" name="fb_verify_token" required>
                    </div>
                    <div class="mb-3">
                        <label for="editFbPhoneNumberId" class="form-label">Facebook Phone Number ID</label>
                        <input type="text" class="form-control" id="editFbPhoneNumberId" name="fb_phone_number_id" required>
                    </div>
                    <div class="mb-3">
                        <label for="editDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="editDescription" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update BOT</button>
                </div>
            </form>
        </div>
    </div>
</div>




<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Complete Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="paypal-button-container"></div>
            </div>
        </div>
    </div>
</div>



<!-- Add this new section below the existing dashboard content -->


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/dashboard.js"></script>
	
	<script>
	
	function doshowbot() {
  var mytabs = document.getElementById("mytabs");
  var myBotsSection = document.getElementById("my-bots-section");
    var chatflow = document.getElementById("chatflow");
	var chats = document.getElementById("chats");

  if (mytabs) mytabs.style.display = "none";
  
  if (myBotsSection) myBotsSection.style.display = "block";
  
    chatflow.style.display ="none";
	chats.style.display = "none";

  
  
}


function showDash(){
	
	var mytabs = document.getElementById("mytabs");
  var myBotsSection = document.getElementById("my-bots-section");
  var chatflow = document.getElementById("chatflow");
  	var chats = document.getElementById("chats");
	var subscription  = document.getElementById("subscriptions");
  var settings =   document.getElementById("settings");


   mytabs.style.display = "block";
  myBotsSection.style.display = "block";
  //chatflow.style.display ="block";
  	chats.style.display = "block";

  
	
}


function showchatflow(){
	
	var mytabs = document.getElementById("mytabs");
  var myBotsSection = document.getElementById("my-bots-section");
  var chatflow = document.getElementById("chatflow");
  var chats = document.getElementById("chats");
  	var subscription  = document.getElementById("subscriptions");
  var settings =   document.getElementById("settings");


   mytabs.style.display = "none";
  myBotsSection.style.display = "none";
  chatflow.style.display ="block";
  chats.style.display = "none";
  subscription.style.display ="none";
  settings.style.display = "none";

	
}

function showchats(){
		var mytabs = document.getElementById("mytabs");
  var myBotsSection = document.getElementById("my-bots-section");
  var chatflow = document.getElementById("chatflow");
    	var chats = document.getElementById("chats");
			var subscription  = document.getElementById("subscriptions");
  var settings =   document.getElementById("settings");


   mytabs.style.display = "none";
  myBotsSection.style.display = "none";
  chatflow.style.display ="none";
    	chats.style.display = "block";
		
		subscription.style.display ="none";
  settings.style.display = "none";
	
}


function showsubs(){
	
	
		var mytabs = document.getElementById("mytabs");
  var myBotsSection = document.getElementById("my-bots-section");
  var chatflow = document.getElementById("chatflow");
    	var chats = document.getElementById("chats");
			var subscription  = document.getElementById("subscriptions");
  var settings =   document.getElementById("settings");


   mytabs.style.display = "none";
  myBotsSection.style.display = "none";
  chatflow.style.display ="none";
    	chats.style.display = "none";
		
		subscription.style.display ="flex";
  settings.style.display = "none";
	
	
}


function showsettings(){
	
		var mytabs = document.getElementById("mytabs");
  var myBotsSection = document.getElementById("my-bots-section");
  var chatflow = document.getElementById("chatflow");
    	var chats = document.getElementById("chats");
			var subscription  = document.getElementById("subscriptions");
  var settings =   document.getElementById("settings");


   mytabs.style.display = "none";
  myBotsSection.style.display = "none";
  chatflow.style.display ="none";
    	chats.style.display = "none";
		
		subscription.style.display ="none";
  settings.style.display = "block";
}

	</script>
</body>
</html>