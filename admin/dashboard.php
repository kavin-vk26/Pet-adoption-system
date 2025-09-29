<?php
require '../includes/config.php';
if(session_status()==PHP_SESSION_NONE) session_start();

// Restrict access to admins only
if(!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../index.php");
    exit;
}

$requests_query = "
    SELECT 
        ar.id as request_id, 
        ar.status, 
        ar.requested_at, 
        u.name as user_name, 
        u.email as user_email, 
        p.name as pet_name,
        p.species,
        p.id as pet_id
    FROM adoption_requests ar
    JOIN users u ON ar.user_id = u.id
    JOIN pets p ON ar.pet_id = p.id
    ORDER BY ar.requested_at DESC
";
$requests_result = $conn->query($requests_query);

$pets_query = "SELECT id, name, species, age, is_adopted FROM pets ORDER BY id DESC";
$pets_result = $conn->query($pets_query);

require '../includes/header.php';
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
h1,h3,h4 {
    color:#00796b;
}
.table th, .table td {
    vertical-align: middle;
}
.btn-primary, .btn-success, .btn-warning, .btn-danger, .btn-secondary {
    border-radius:10px;
    font-weight:500;
    transition:0.3s;
}
.btn-primary { background:#00bfa6; border:none; }
.btn-primary:hover { background:#009e89; }
.btn-success { background:#00bfa6; border:none; }
.btn-success:hover { background:#009e89; }
.btn-warning { background:#ff9800; border:none; color:#fff; }
.btn-warning:hover { background:#e68900; }
.btn-danger { background:#f44336; border:none; }
.btn-danger:hover { background:#d32f2f; }
.btn-secondary { background:#607d8b; border:none; color:#fff; }
.btn-secondary:hover { background:#455a64; }
.alert { border-radius:10px; padding:12px 15px; }
.badge { font-weight:500; padding:0.5em 0.7em; border-radius:10px; }
</style>

<div class="container">
    <h1 class="text-center text-danger mb-5"><i class="fas fa-tools"></i> Admin Dashboard</h1>

    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h3 class="text-secondary"><i class="fas fa-paw"></i> Pet Management</h3>
            <a href="add_pet.php" class="btn btn-success btn-lg shadow-sm"><i class="fas fa-plus-circle"></i> Add New Pet</a>
        </div>
    </div>

    <!-- Pet Management Table -->
    <div class="card p-4 shadow-lg border-0 mb-5">
        <h4 class="card-title text-primary mb-4">All Pets in System (<?= $pets_result ? $pets_result->num_rows : 0 ?> total)</h4>
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Species</th>
                        <th>Age</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($pets_result && $pets_result->num_rows > 0): ?>
                        <?php while($pet = $pets_result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $pet['id'] ?></td>
                            <td><a href="../pet_detail.php?id=<?= $pet['id'] ?>" class="text-primary fw-bold"><?= htmlspecialchars($pet['name']) ?></a></td>
                            <td><?= htmlspecialchars($pet['species']) ?></td>
                            <td><?= $pet['age'] ?></td>
                            <td>
                                <?php 
                                $badge_class = $pet['is_adopted'] == 1 ? 'bg-secondary' : 'bg-success';
                                $status_text = $pet['is_adopted'] == 1 ? 'Adopted' : 'Available';
                                ?>
                                <span class="badge <?= $badge_class ?>"><?= $status_text ?></span>
                            </td>
                            <td>
                                <a href="edit_pet.php?id=<?= $pet['id'] ?>" class="btn btn-sm btn-warning me-2"><i class="fas fa-edit"></i> Edit</a>
                                <a href="delete_pet.php?id=<?= $pet['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this pet?');"><i class="fas fa-trash"></i> Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center">No pets found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Adoption Requests Table -->
    <div class="card p-4 shadow-lg border-0">
        <h3 class="card-title text-danger mb-4"><i class="fas fa-heartbeat"></i> Pending Adoption Requests (<?= $requests_result ? $requests_result->num_rows : 0 ?> total)</h3>
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <th>Pet Name</th>
                        <th>Applicant</th>
                        <th>Status</th>
                        <th>Requested On</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($requests_result && $requests_result->num_rows > 0): ?>
                        <?php while($req = $requests_result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $req['request_id'] ?></td>
                            <td><a href="../pet_detail.php?id=<?= $req['pet_id'] ?>" class="text-primary fw-bold"><?= htmlspecialchars($req['pet_name']) ?></a></td>
                            <td><?= htmlspecialchars($req['user_name']) ?> (<?= htmlspecialchars($req['user_email']) ?>)</td>
                            <td>
                                <?php
                                    $badge_class = 'bg-secondary';
                                    if ($req['status'] == 'approved') $badge_class = 'bg-success';
                                    if ($req['status'] == 'rejected') $badge_class = 'bg-danger';
                                    if ($req['status'] == 'pending') $badge_class = 'bg-warning text-dark';
                                ?>
                                <span class="badge <?= $badge_class ?>"><?= ucfirst($req['status']) ?></span>
                            </td>
                            <td><?= date('M d, Y H:i', strtotime($req['requested_at'])) ?></td>
                            <td>
                                <?php if($req['status'] == 'pending'): ?>
                                    <a href="approve_request.php?id=<?= $req['request_id'] ?>" class="btn btn-sm btn-success me-2"><i class="fas fa-check"></i> Approve</a>
                                    <a href="reject_request.php?id=<?= $req['request_id'] ?>" class="btn btn-sm btn-danger"><i class="fas fa-times"></i> Reject</a>
                                <?php else: ?>
                                    <span class="text-muted">Reviewed</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center">No requests found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>
