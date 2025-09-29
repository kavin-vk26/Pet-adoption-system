<?php
require 'includes/config.php';
if(session_status()==PHP_SESSION_NONE) session_start();

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Block Admin from Profile Page
if($_SESSION['is_admin'] == 1) {
    header("Location: admin/dashboard.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_data = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();
$success_message = '';

if(isset($_POST['update_profile'])){
    $name = $conn->real_escape_string($_POST['name']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $address = $conn->real_escape_string($_POST['address']);

    $conn->query("UPDATE users SET name='$name', phone='$phone', address='$address' WHERE id=$user_id");
    $_SESSION['user_name'] = $name;
    $user_data = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();
    $success_message = "<p class='alert alert-success text-center'><i class='fas fa-check-circle'></i> Profile updated successfully!</p>";
}

// Fetch user's adoption requests
$requests_sql = "SELECT r.id as request_id, r.status, r.requested_at, p.name as pet_name, p.species, p.id as pet_id 
                 FROM adoption_requests r
                 JOIN pets p ON r.pet_id = p.id
                 WHERE r.user_id = $user_id
                 ORDER BY r.requested_at DESC";
$requests = $conn->query($requests_sql);

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
h1, h3 {
    color:#00796b;
}
.card {
    border-radius:15px;
    box-shadow:0 6px 15px rgba(0,0,0,0.1);
    border:none;
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
.btn-primary, .btn-success, .btn-info {
    border-radius:10px;
    font-weight:500;
    transition:0.3s;
}
.btn-primary { background:#00bfa6; border:none; }
.btn-primary:hover { background:#009e89; }
.btn-success { background:#00bfa6; border:none; }
.btn-success:hover { background:#009e89; }
.btn-info { background:#17a2b8; border:none; color:#fff; }
.btn-info:hover { background:#138496; color:#fff; }
.alert { border-radius:10px; padding:12px 15px; }
.badge { border-radius:10px; }
</style>

<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="text-center mb-4">Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h1>
            <?= $success_message ?>
        </div>
    </div>

    <div class="row">
        <!-- Profile Update Card -->
        <div class="col-md-5 mb-4">
            <div class="card p-4 h-100">
                <h3 class="mb-4"><i class="fas fa-user-edit"></i> Update Details</h3>
                <form method="post">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($user_data['name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email (Read Only)</label>
                        <input type="email" class="form-control bg-light" id="email" value="<?= htmlspecialchars($user_data['email']) ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($user_data['phone'] ?? '') ?>">
                    </div>
                    <div class="mb-4">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address"><?= htmlspecialchars($user_data['address'] ?? '') ?></textarea>
                    </div>
                    <button type="submit" name="update_profile" class="btn btn-primary w-100 py-2">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </form>
            </div>
        </div>

        <!-- Adoption Requests Card -->
        <div class="col-md-7 mb-4">
            <div class="card p-4 h-100">
                <h3 class="mb-4"><i class="fas fa-list-alt"></i> My Adoption Requests</h3>
                <?php if($requests->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Pet</th>
                                <th>Species</th>
                                <th>Status</th>
                                <th>Requested At</th>
                                <th>Certificate</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($req = $requests->fetch_assoc()): ?>
                            <tr>
                                <td><a href="pet_detail.php?id=<?= $req['pet_id'] ?>" class="text-primary fw-bold"><?= htmlspecialchars($req['pet_name']) ?></a></td>
                                <td><?= htmlspecialchars($req['species']) ?></td>
                                <td>
                                    <?php
                                        $badge_class = 'bg-secondary';
                                        if ($req['status'] == 'approved') $badge_class = 'bg-success';
                                        if ($req['status'] == 'rejected') $badge_class = 'bg-danger';
                                        if ($req['status'] == 'pending') $badge_class = 'bg-warning text-dark';
                                    ?>
                                    <span class="badge <?= $badge_class ?>"><?= ucfirst($req['status']) ?></span>
                                </td>
                                <td><?= date('M d, Y', strtotime($req['requested_at'])) ?></td>
                                <td>
                                    <?php if($req['status'] == 'approved'): ?>
                                    <a href="generate_certificate.php?request_id=<?= $req['request_id'] ?>" target="_blank" class="btn btn-sm btn-info">
                                        <i class="fas fa-download"></i> PDF
                                    </a>
                                    <?php else: ?>
                                    <span class="text-muted"><i class="fas fa-times-circle"></i> N/A</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <p class="alert alert-info text-center"><i class="fas fa-info-circle"></i> You have no active adoption requests.</p>
                    <p class="text-center"><a href="index.php" class="btn btn-success"><i class="fas fa-plus"></i> Find a Pet</a></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require 'includes/footer.php'; ?>
