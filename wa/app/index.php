<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
redirectIfLoggedIn();

$csrfToken = generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp BOT Creator - Login/Signup</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <div class="auth-container">
	<div>	<image src="../logo22.png" style="width:132px;">
</div>
	
        <div class="auth-form-container">
            <div class="auth-tabs">
                <button class="auth-tab active" data-tab="login">Login</button>
                <button class="auth-tab" data-tab="signup">Sign Up</button>
            </div>
            
            <!-- Login Form -->
            <div class="auth-form active" id="login-form">
                <h2>Welcome Back</h2>
                <p>Login to manage your WhatsApp BOTs</p>
                
				<!-- In login form -->
<?php showFlash('error'); ?>


                <?php showFlash('success'); ?>
                
                <form action="login.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                    
                    <div class="form-group">
                        <label for="login-email">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" id="login-email" name="email" class="form-control" placeholder="Enter your email" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="login-password">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" id="login-password" name="password" class="form-control" placeholder="Enter your password" required>
                            <button type="button" class="btn btn-outline-secondary toggle-password" data-target="login-password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="form-group form-check">
                        <input type="checkbox" id="remember-me" name="remember" class="form-check-input">
                        <label for="remember-me" class="form-check-label">Remember me</label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">Login</button>
                    
                    <div class="text-center mt-3">
                        <a href="#" class="forgot-password">Forgot password?</a>
                    </div>
                </form>
            </div>
            
            <!-- Signup Form -->
            <div class="auth-form" id="signup-form">
                <h2>Create Account</h2>
                <p>Start building WhatsApp BOTs in minutes</p>'
				<!-- In signup form -->
<?php showFlash('signup_error'); ?>
                
                <form action="signup.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                    
                    <div class="form-group">
                        <label for="signup-name">Full Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" id="signup-name" name="name" class="form-control" placeholder="Enter your full name" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="signup-email">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" id="signup-email" name="email" class="form-control" placeholder="Enter your email" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="signup-password">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" id="signup-password" name="password" class="form-control" placeholder="Create a password" required>
                            <button type="button" class="btn btn-outline-secondary toggle-password" data-target="signup-password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <small class="form-text text-muted">Minimum 8 characters with at least one uppercase, one lowercase, one number and one special character</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="signup-confirm-password">Confirm Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" id="signup-confirm-password" name="confirm_password" class="form-control" placeholder="Confirm your password" required>
                        </div>
                    </div>
                    
                    <div class="form-group form-check">
                        <input type="checkbox" id="terms" name="terms" class="form-check-input" required>
                        <label for="terms" class="form-check-label">I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">Sign Up</button>
                </form>
            </div>
        </div>
        
        <div class="auth-image-container">
            <div class="auth-image-overlay"></div>
            <div class="auth-image-content">
			
                <h2>WAassist.IO</h2>
                <p>Build powerful chatbots for your business without coding</p>
                <div class="features-list">
                    <div class="feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Build Chat Flows </span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Multi-language Support</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Real-time Analytics</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>24/7 Customer Support</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>