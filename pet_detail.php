<?php
require 'includes/config.php';
if(session_status()==PHP_SESSION_NONE) session_start();

$id = $_GET['id'] ?? 0; 
$pet_result = $conn->query("SELECT * FROM pets WHERE id=".intval($id));
if($pet_result->num_rows === 0) { header("Location: index.php"); exit; }
$pet = $pet_result->fetch_assoc();
$images = $conn->query("SELECT * FROM pet_images WHERE pet_id=".intval($id));

if(isset($_POST['adopt'])){
    if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
    if ($_SESSION['is_admin'] == 1) {
        $message = "<p class='alert alert-danger text-center'><i class='fas fa-lock'></i> Admins cannot submit adoption requests.</p>";
    } else {
        $user_id = $_SESSION['user_id'];
        $check = $conn->query("SELECT * FROM adoption_requests WHERE user_id=$user_id AND pet_id=".intval($id));
        if($check->num_rows==0){
            $conn->query("INSERT INTO adoption_requests(user_id, pet_id) VALUES($user_id, ".intval($id).")");
            $message = "<p class='alert alert-success text-center'><i class='fas fa-check-circle'></i> Adoption request submitted!</p>";
        } else {
            $message = "<p class='alert alert-warning text-center'><i class='fas fa-info-circle'></i> You already have a request for this pet.</p>";
        }
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
    padding:20px;
}
.card {
    border-radius:15px;
    box-shadow:0 6px 15px rgba(0,0,0,0.1);
    border:none;
}
.btn-primary, .btn-success, .btn-dark {
    border-radius:10px;
    font-weight:500;
    transition:0.3s;
}
.btn-primary { background:#00bfa6; border:none; }
.btn-primary:hover { background:#009e89; }
.btn-success { background:#00bfa6; border:none; }
.btn-success:hover { background:#009e89; }
.btn-dark { background:#333; border:none; }
.btn-dark:hover { background:#555; }
.alert { border-radius:10px; padding:12px 15px; }
h1, h4 { color:#00796b; }
.carousel-inner img { border-radius:15px; }
</style>

<div class="container">
    <div class="row mb-3">
        <div class="col-12">
            <a href="index.php" class="btn btn-secondary btn-sm mb-3"><i class="fas fa-arrow-left"></i> Back to Pet List</a>
        </div>
    </div>

    <?php if (isset($message)) echo $message; ?>

    <div class="row g-4">
      <div class="col-md-6">
        <div id="petCarousel" class="carousel slide border rounded shadow-sm" data-bs-ride="carousel">
          <div class="carousel-inner rounded">
            <?php 
            $first=true;
            if($images->num_rows > 0) {
                $images->data_seek(0); 
                while($img=$images->fetch_assoc()){ 
            ?>
              <div class="carousel-item <?= $first?'active':'' ?>">
                <img src="uploads/<?= htmlspecialchars($img['image']) ?>" class="d-block w-100" alt="Pet Image" style="height:400px; object-fit:cover;">
              </div>
            <?php $first=false; } 
            } else { ?>
                 <div class="carousel-item active">
                    <img src="assets/images/default_pet.jpg" class="d-block w-100" alt="Default Pet Image" style="height:400px; object-fit:cover;">
                 </div>
            <?php } ?>
          </div>
          <?php if($images->num_rows > 1): ?>
          <button class="carousel-control-prev" type="button" data-bs-target="#petCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#petCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
          </button>
          <?php endif; ?>
        </div>
      </div>

      <div class="col-md-6">
        <div class="card p-4 shadow-lg border-0">
            <h1 class="text-primary"><?= htmlspecialchars($pet['name']) ?></h1>
            <hr class="text-secondary">
            <p class="fs-5 mb-1"><i class="fas fa-paw text-success"></i> Species: <?= htmlspecialchars($pet['species']) ?></p>
            <p class="fs-5 mb-4"><i class="fas fa-birthday-cake text-info"></i> Age: <?= htmlspecialchars($pet['age']) ?> years old</p>
            
            <h4 class="mt-3">About <?= htmlspecialchars($pet['name']) ?></h4>
            <p class="text-muted"><?= nl2br(htmlspecialchars($pet['description'])) ?></p>

            <?php if($pet['is_adopted'] == 1): ?>
                <button class="btn btn-dark btn-lg mt-3" disabled><i class="fas fa-check"></i> Already Adopted</button>
            <?php else: ?>
                <hr>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <?php if($_SESSION['is_admin'] == 1): ?>
                        <p class="alert alert-info text-center"><i class="fas fa-users-cog"></i> Admin cannot adopt pets.</p>
                    <?php else: 
                        $user_id = $_SESSION['user_id'];
                        $has_requested = $conn->query("SELECT id, status FROM adoption_requests WHERE user_id=$user_id AND pet_id=".intval($id))->fetch_assoc();
                    ?>
                        <?php if($has_requested): ?>
                            <p class="alert alert-warning text-center">
                                <i class="fas fa-clock"></i> Request already <?= ucfirst($has_requested['status']) ?>.<br>Check your <a href="profile.php">profile</a>.
                            </p>
                        <?php else: ?>
                            <form method="post">
                                <button type="submit" name="adopt" class="btn btn-success btn-lg w-100 mt-3 shadow-sm">
                                    <i class="fas fa-heart"></i> Request Adoption
                                </button>
                            </form>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary btn-lg w-100 mt-3 shadow-sm">
                        <i class="fas fa-sign-in-alt"></i> Login to Adopt
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
      </div>
    </div>
</div>

<?php require 'includes/footer.php'; ?>
