<?php
require '../includes/config.php';
if(session_status()==PHP_SESSION_NONE) session_start();

// Security Check: Only allow logged-in admins
if(!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../login.php");
    exit;
}

$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

$filter_status = $_GET['status'] ?? 'all';

// Fetch Requests for Table
$where_clause = '';
if ($filter_status != 'all') {
    $safe_status = $conn->real_escape_string($filter_status);
    $where_clause = "WHERE ar.status = '$safe_status'";
}

$requests_sql = "
    SELECT 
        ar.id as request_id, ar.status, ar.requested_at,
        u.name as user_name, u.email as user_email, u.phone, u.address,
        p.name as pet_name, p.species, p.id as pet_id, p.is_adopted
    FROM adoption_requests ar
    JOIN users u ON ar.user_id = u.id
    JOIN pets p ON ar.pet_id = p.id
    $where_clause
    ORDER BY ar.requested_at DESC
";
$requests_result = $conn->query($requests_sql);

// Fetch counts for tabs
$counts = [
    'all' => $conn->query("SELECT COUNT(*) FROM adoption_requests")->fetch_row()[0],
    'pending' => $conn->query("SELECT COUNT(*) FROM adoption_requests WHERE status = 'pending'")->fetch_row()[0],
    'approved' => $conn->query("SELECT COUNT(*) FROM adoption_requests WHERE status = 'approved'")->fetch_row()[0],
    'rejected' => $conn->query("SELECT COUNT(*) FROM adoption_requests WHERE status = 'rejected'")->fetch_row()[0],
];

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
h1,h2,h4 {
    color:#00796b;
}
.nav-tabs .nav-link {
    border-radius:10px;
    margin-right:5px;
    transition:0.3s;
}
.nav-tabs .nav-link:hover {
    opacity:0.8;
}
.table {
    border-radius:10px;
    overflow:hidden;
    box-shadow:0 4px 10px rgba(0,0,0,0.1);
}
.table th, .table td {
    vertical-align: middle;
}
.btn {
    border-radius:10px;
    font-weight:500;
    transition:0.3s;
}
.btn-primary { background:#00bfa6; border:none; color:#fff; }
.btn-primary:hover { background:#009e89; }
.btn-success { background:#00bfa6; border:none; color:#fff; }
.btn-success:hover { background:#009e89; }
.btn-danger { background:#f44336; border:none; color:#fff; }
.btn-danger:hover { background:#d32f2f; }
.badge { font-weight:500; padding:0.5em 0.7em; border-radius:10px; }
.badge.bg-warning { color:#333; }
</style>

<div class="container">
    <h1 class="text-center text-primary mb-4"><i class="fas fa-list-alt"></i> Adoption Requests</h1>
    <?= $message ?>

    <!-- Filter Tabs -->
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link <?= $filter_status == 'all' ? 'active bg-primary text-white' : '' ?>" href="requests_list.php?status=all">All (<?= $counts['all'] ?>)</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $filter_status == 'pending' ? 'active bg-warning text-dark' : '' ?>" href="requests_list.php?status=pending">Pending (<?= $counts['pending'] ?>)</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $filter_status == 'approved' ? 'active bg-success text-white' : '' ?>" href="requests_list.php?status=approved">Approved (<?= $counts['approved'] ?>)</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $filter_status == 'rejected' ? 'active bg-danger text-white' : '' ?>" href="requests_list.php?status=rejected">Rejected (<?= $counts['rejected'] ?>)</a>
        </li>
    </ul>

    <!-- Requests Table -->
    <div class="table-responsive">
        <table class="table table-striped table-hover shadow-sm">
            <thead class="bg-dark text-white">
                <tr>
                    <th>Req ID</th>
                    <th>Pet</th>
                    <th>Adopter</th>
                    <th>Status</th>
                    <th>Requested At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($requests_result && $requests_result->num_rows > 0): ?>
                    <?php while($req = $requests_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $req['request_id'] ?></td>
                        <td>
                            <a href="../pet_detail.php?id=<?= $req['pet_id'] ?>" target="_blank" class="fw-bold text-primary">
                                <?= htmlspecialchars($req['pet_name']) ?> (<?= htmlspecialchars($req['species']) ?>)
                            </a>
                        </td>
                        <td>
                            <?= htmlspecialchars($req['user_name']) ?><br>
                            <small class="text-muted"><i class="fas fa-envelope"></i> <?= htmlspecialchars($req['user_email']) ?></small>
                        </td>
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
                            <?php if ($req['status'] == 'pending'): ?>
                                <a href="approve_request.php?id=<?= $req['request_id'] ?>&status=<?= $filter_status ?>" class="btn btn-sm btn-success me-2" onclick="return confirm('Approve request for <?= htmlspecialchars($req['pet_name']) ?>? This marks the pet as adopted.');" title="Approve">
                                    <i class="fas fa-check"></i> Approve
                                </a>
                                <a href="reject_request.php?id=<?= $req['request_id'] ?>&status=<?= $filter_status ?>" class="btn btn-sm btn-danger" onclick="return confirm('Reject request for <?= htmlspecialchars($req['pet_name']) ?>?');" title="Reject">
                                    <i class="fas fa-times"></i> Reject
                                </a>
                            <?php else: ?>
                                <span class="text-muted"><i class="fas fa-info-circle"></i> Action Taken</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No <?= $filter_status ?> requests found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require '../includes/footer.php'; ?>
