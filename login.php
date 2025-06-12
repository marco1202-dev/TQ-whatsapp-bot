<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = $_POST['email'];
  $password = $_POST['password'];

  // Dummy check (replace with real logic)
  if ($email === 'admin@example.com' && $password === 'admin123') {
    $_SESSION['user'] = ['email' => $email];
    header("Location: dashboard.php");
    exit;
  } else {
    $error = "Invalid credentials!";
  }
}
include 'includes/header.php';
?>


<!-- Your styled login form -->
<form method="POST" class="p-4 border rounded shadow-sm bg-white" style="max-width: 400px; margin: 5rem auto;">
  <div class="text-center mb-4">
    <img src="botl.png" width="60" />
    <h4>Login</h4>
  </div>
  <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
  <div class="mb-3">
    <input type="email" name="email" class="form-control" placeholder="Email" required>
  </div>
  <div class="mb-3">
    <input type="password" name="password" class="form-control" placeholder="Password" required>
  </div>
  <button class="btn btn-primary w-100">Login</button>
</form>
<?php include 'includes/footer.php'; ?>
