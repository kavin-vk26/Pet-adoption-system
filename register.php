<?php
require 'includes/config.php';
if(session_status()==PHP_SESSION_NONE) session_start();
if(isset($_SESSION['user_id'])) header("Location: profile.php");

$error = '';
if(isset($_POST['register'])){
    $name=$conn->real_escape_string($_POST['name']);
    $email=$conn->real_escape_string($_POST['email']);
    $password=password_hash($_POST['password'],PASSWORD_BCRYPT);
    $phone=$conn->real_escape_string($_POST['phone']);
    $address=$conn->real_escape_string($_POST['address']);

    $check_email=$conn->query("SELECT id FROM users WHERE email='$email'");
    if($check_email->num_rows>0) $error="This email is already registered.";
    else{
        $conn->query("INSERT INTO users(name,email,password,phone,address) VALUES('$name','$email','$password','$phone','$address')");
        $_SESSION['user_id']=$conn->insert_id;
        $_SESSION['is_admin']=0;
        header("Location: profile.php");
        exit;
    }
}
require 'includes/header.php';
?>

<style>
body {
    background: linear-gradient(135deg,#a8edea 0%,#fed6e3 100%);
    font-family:"Poppins",sans-serif;
    color:#333;
    min-height:100vh;
    padding:0;
    margin:0;
}
.auth-container {
    min-height: calc(100vh);
    display:flex;
    justify-content:center;
    align-items:center;
    padding:20px;
}
.auth-card {
    background:rgba(255,255,255,0.95);
    padding:30px 25px;
    border-radius:15px;
    box-shadow:0 6px 15px rgba(0,0,0,0.1);
    width:100%;
    max-width:450px;
}
.auth-card h2 {
    color:#00796b;
    text-align:center;
    margin-bottom:25px;
    font-weight:700;
}
.form-control {
    border-radius:10px;
    border:2px solid #ddd;
    transition:all 0.3s ease;
}
.form-control:focus {
    border-color:#00bfa6;
    box-shadow:0 0 8px rgba(0,191,166,0.4);
}
.btn-success {
    background:#00bfa6;
    border:none;
    border-radius:10px;
    font-weight:500;
    transition:0.3s;
}
.btn-success:hover {
    background:#009e89;
}
.auth-card a {
    color:#00bfa6;
    font-weight:500;
}
.auth-card a:hover {
    color:#009e89;
    text-decoration:none;
}
.alert {
    border-radius:10px;
    padding:12px 15px;
}
</style>

<div class="auth-container">
    <div class="auth-card">
        <h2>Create Account</h2>
        <?php if(!empty($error)) echo "<p class='alert alert-danger text-center'>$error</p>"; ?>
        <form method="post">
            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" required minlength="6">
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" name="phone" id="phone" class="form-control">
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea name="address" id="address" class="form-control"></textarea>
            </div>
            <button name="register" class="btn btn-success w-100 py-2">Register</button>
        </form>
        <p class="text-center mt-3">
            Already have an account? <a href="login.php">Login here</a>
        </p>
    </div>
</div>

<?php require 'includes/footer.php'; ?>
