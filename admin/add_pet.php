<?php
require '../includes/config.php';
if(session_status()==PHP_SESSION_NONE) session_start();

// Security Check: Only allow logged-in admins
if(!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../login.php");
    exit;
}

$message = '';

// Handle ADD form submission
if (isset($_POST['submit_pet'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $species = $conn->real_escape_string($_POST['species']);
    $age = (int)$_POST['age'];
    $description = $conn->real_escape_string($_POST['description']);
    $is_adopted = 0;

    $sql = "INSERT INTO pets (name, species, age, description, is_adopted) VALUES ('$name', '$species', $age, '$description', $is_adopted)";
    if ($conn->query($sql)) {
        $pet_id = $conn->insert_id;
        $message = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> New pet added successfully.</div>';

        if ($pet_id && !empty($_FILES['images']['name'][0])) {
            $upload_dir = '../uploads/';
            foreach ($_FILES['images']['name'] as $key => $image_name) {
                $tmp_name = $_FILES['images']['tmp_name'][$key];
                $ext = pathinfo($image_name, PATHINFO_EXTENSION);
                $new_file_name = uniqid('pet_') . '.' . $ext;
                $destination = $upload_dir . $new_file_name;
                if (move_uploaded_file($tmp_name, $destination)) {
                    $conn->query("INSERT INTO pet_images (pet_id, image) VALUES ($pet_id, '$new_file_name')");
                }
            }
        }
        header("Location: edit_pet.php?id=$pet_id&success=1");
        exit;
    } else {
        $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Error adding pet: ' . $conn->error . '</div>';
    }
}

$pets_result = $conn->query("SELECT * FROM pets ORDER BY id DESC");

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
h1,h2,h4 {
    color:#00796b;
}
.table th, .table td {
    vertical-align: middle;
}
.btn-primary, .btn-success, .btn-warning, .btn-danger, .btn-info {
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
.btn-info { background:#2196f3; border:none; color:#fff; }
.btn-info:hover { background:#1976d2; }
.alert { border-radius:10px; padding:12px 15px; }
.badge { font-weight:500; padding:0.5em 0.7em; border-radius:10px; }
.form-control {
    border-radius:10px;
    border:2px solid #ddd;
    transition:all 0.3s ease;
}
.form-control:focus {
    border-color:#00bfa6;
    box-shadow:0 0 8px rgba(0,191,166,0.4);
}
</style>

<div class="container">
    <h1 class="text-center text-primary mb-4"><i class="fas fa-plus-circle"></i> Add New Pet</h1>
    <?= $message ?>

    <!-- Add Pet Form Section -->
    <div class="card shadow mb-5 p-4">
        <h4 class="text-secondary mb-3"><i class="fas fa-paw"></i> Pet Details</h4>
        <form method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="species" class="form-label">Species (e.g., Dog, Cat)</label>
                    <input type="text" class="form-control" id="species" name="species" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="age" class="form-label">Age (Years)</label>
                    <input type="number" class="form-control" id="age" name="age" required min="0">
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            </div>
            
            <div class="mb-4">
                <label for="images" class="form-label">Pet Images</label>
                <input type="file" class="form-control" id="images" name="images[]" multiple accept="image/*" required>
                <small class="form-text text-muted">Select one or more images for the pet.</small>
            </div>

            <button type="submit" name="submit_pet" class="btn btn-primary w-100 py-2">
                <i class="fas fa-save"></i> Add Pet
            </button>
        </form>
    </div>

    <!-- Pet List Table Section -->
    <h2 class="mt-5 mb-3"><i class="fas fa-table"></i> Pet Management Overview</h2>
    <div class="table-responsive">
        <table class="table table-striped table-hover shadow-sm">
            <thead class="bg-primary text-white">
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
                <?php while($pet = $pets_result->fetch_assoc()): ?>
                <tr>
                    <td><?= $pet['id'] ?></td>
                    <td><a href="../pet_detail.php?id=<?= $pet['id'] ?>" target="_blank" class="fw-bold text-primary"><?= htmlspecialchars($pet['name']) ?></a></td>
                    <td><?= htmlspecialchars($pet['species']) ?></td>
                    <td><?= $pet['age'] ?></td>
                    <td>
                        <?php if($pet['is_adopted'] == 1): ?>
                            <span class="badge bg-dark"><i class="fas fa-house-user"></i> Adopted</span>
                        <?php else: ?>
                            <span class="badge bg-success"><i class="fas fa-check"></i> Available</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="edit_pet.php?id=<?= $pet['id'] ?>" class="btn btn-sm btn-info text-white me-2" title="Edit"><i class="fas fa-edit"></i></a>
                        <a href="delete_pet.php?id=<?= $pet['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this pet?');" title="Delete"><i class="fas fa-trash-alt"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require '../includes/footer.php'; ?>
