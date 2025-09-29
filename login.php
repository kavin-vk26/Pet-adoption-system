<?php
require 'includes/config.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if(isset($_SESSION['user_id'])) {
    if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) {
        header("Location: admin/dashboard.php");
        exit();
    } else {
        header("Location: profile.php");
        exit();
    }
}

$error = '';
$email = '';
$password = '';

// --- Hardcoded admin credentials ---
$admin_email = 'admin@example.com';
$admin_password = 'admin123'; // Change as needed

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } 
    elseif ($email === $admin_email && $password === $admin_password) {
        $_SESSION['user_id'] = 0;
        $_SESSION['user_name'] = 'Admin';
        $_SESSION['is_admin'] = 1;
        header("Location: admin/dashboard.php");
        exit();
    }
    elseif (!isset($conn) || !$conn || (isset($conn->connect_error) && $conn->connect_error)) {
        $error = "System error: Database connection is unavailable. Please check XAMPP services.";
    } else {
        $sql = "SELECT id AS user_id, name AS full_name, email, password AS password_hash, is_admin FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $error = "Database query preparation failed: " . $conn->error;
        } else {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['is_admin'] = $user['is_admin'];
                if ($_SESSION['is_admin'] == 1) {
                    header("Location: admin/dashboard.php"); 
                    exit();
                } else {
                    header("Location: profile.php");
                    exit();
                }
            } else {
                $error = "Invalid email or password";
            }
        }
    }
}

// include header/navbar
require 'includes/header.php';
?>

<!--
  Styles below ensure the page layout is a column (header - main - footer),
  and the main area (.page-wrap) will center the login card.
-->
<style>
  /* page layout: header (from includes/header.php), main centers, footer below */
  html,body {
    height: 100%;
    margin: 0;
  }

  body {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
    font-family: "Poppins", sans-serif;
  }

  /* Main area between header and footer */
  .page-wrap {
    flex: 1; /* grow to fill space between header & footer */
    display: flex;
    align-items: center;      /* vertical centering */
    justify-content: center;  /* horizontal centering */
    padding: 30px 15px;
  }

  /* small safety if your header is fixed and overlaps content:
     adjust --header-offset to the header height (in px) if you have a fixed header */
  :root { --header-offset: 0px; }
  .page-wrap.fixed-header-offset {
    padding-top: calc(30px + var(--header-offset));
    box-sizing: border-box;
  }

  /* Card visual styles (matching the theme you used) */
  .login-card {
    max-width: 420px;
    width: 100%;
  }
  .login-card .card {
    background: rgba(255,255,255,0.95);
    border-radius: 15px;
    border: none;
    padding: 24px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.12);
    transition: transform 0.18s ease, box-shadow 0.18s ease;
  }
  .login-card .card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.14);
  }

  .form-control {
    border-radius: 10px;
    border: 2px solid #ddd;
    transition: all .25s ease;
  }
  .form-control:focus {
    border-color: #00bfa6;
    box-shadow: 0 0 8px rgba(0,191,166,0.28);
  }

  .btn-primary {
    background: #00bfa6;
    border: none;
    border-radius: 10px;
    padding: 10px 14px;
    font-weight: 600;
  }
  .btn-primary:hover { background: #009e89; }

  h2.login-title {
    color: #00796b;
    margin-bottom: 1rem;
    text-align: center;
  }

  /* make sure small screens look good */
  @media (max-width: 576px) {
    .login-card .card { padding: 18px; }
    body { padding: 10px; }
  }
</style>

<main class="page-wrap">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-sm-10 col-md-8 col-lg-5 login-card">
        <div class="card">
          <h2 class="login-title">User Login</h2>

          <?php if (!empty($error)): ?>
              <div class="alert alert-danger" role="alert">
                  <?= htmlspecialchars($error) ?>
              </div>
          <?php endif; ?>

          <form action="login.php" method="POST" novalidate>
              <div class="mb-3">
                  <label for="email" class="form-label">Email</label>
                  <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
              </div>
              <div class="mb-3">
                  <label for="password" class="form-label">Password</label>
                  <input type="password" class="form-control" id="password" name="password" required>
              </div>
              <button type="submit" class="btn btn-primary w-100">Log In</button>
          </form>

          <div class="text-center mt-3">
              Don't have an account? <a href="register.php" class="text-decoration-none">Register here</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<?php
// include footer
require 'includes/footer.php';
?>
