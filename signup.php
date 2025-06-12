<?php
session_start();
$users = json_decode(file_get_contents("data/users.json"), true);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $users[] = ["email" => $email, "password" => $password];
    file_put_contents("data/users.json", json_encode($users, JSON_PRETTY_PRINT));
    $_SESSION['email'] = $email;
    header("Location: dashboard.php");
    exit;
}
?>
<?php include 'includes/header.php'; ?>
<div class="signup-box">
  <div class="text-center mb-4">
    <img src="assets/img/logo.png" width="80" alt="Logo">
    <h3>Create Account</h3>
  </div>
  <form method="post">
    <div class="mb-3"><input type="email" name="email" class="form-control" placeholder="Email" required></div>
    <div class="mb-3"><input type="password" name="password" class="form-control" placeholder="Password" required></div>
    <button type="submit" class="btn btn-success w-100">Signup</button>
    <a href="login.php" class="btn btn-link w-100">Already have an account?</a>
  </form>
</div>
<?php include 'includes/footer.php'; ?>