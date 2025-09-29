<?php
require '../includes/config.php';

// Security Check: Only allow logged-in admins
if(!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: add_pet.php");
    exit;
}

$pet_id = (int)$_GET['id'];
$message = '';

// Handle EDIT form submission
if (isset($_POST['submit_pet'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $species = $conn->real_escape_string($_POST['species']);
    $age = (int)$_POST['age'];
    $description = $conn->real_escape_string($_POST['description']);
    $is_adopted = isset($_POST['is_adopted']) ? 1 : 0;

    // UPDATE PET (breed removed)
    $sql = "UPDATE pets SET name='$name', species='$species', age=$age, description='$description', is_adopted=$is_adopted WHERE id=$pet_id";
    if ($conn->query($sql)) {
        $message = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Pet updated successfully.</div>';
    } else {
         $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Error updating pet: ' . $conn->error . '</div>';
    }

    // Handle Image Uploads (Adding new images)
    if (!empty($_FILES['images']['name'][0])) {
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
        $message .= '<div class="alert alert-info mt-2"><i class="fas fa-images"></i> New images uploaded.</div>';
    }
}

// Handle Image Deletion
if (isset($_GET['delete_image_id'])) {
    $image_id = (int)$_GET['delete_image_id'];
    $file_to_delete = $conn->query("SELECT image FROM pet_images WHERE id=$image_id AND pet_id=$pet_id")->fetch_assoc();
    
    if ($file_to_delete) {
        $conn->query("DELETE FROM pet_images WHERE id = $image_id");
        @unlink('../uploads/' . $file_to_delete['image']);
        $message = '<div class="alert alert-warning"><i class="fas fa-trash-alt"></i> Image deleted successfully.</div>';
        header("Location: edit_pet.php?id=$pet_id");
        exit;
    }
}

// Fetch current pet data and images for display
$current_pet = $conn->query("SELECT * FROM pets WHERE id=$pet_id")->fetch_assoc();
if (!$current_pet) {
    header("Location: add_pet.php");
    exit;
}

$image_query = $conn->query("SELECT id, image FROM pet_images WHERE pet_id=$pet_id");
$current_images = [];
while($row = $image_query->fetch_assoc()) {
    $current_images[] = $row;
}

require '../includes/header.php';
?>

<h1 class="text-center text-primary mb-4"><i class="fas fa-edit"></i> Edit Pet: <?= htmlspecialchars($current_pet['name']) ?></h1>
<?php 
if (isset($_GET['success']) && $_GET['success'] == 1) {
    echo '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Pet added successfully! Now you can edit details and manage images.</div>';
}
echo $message; 
?>

<!-- Edit Pet Form Section -->
<div class="card shadow mb-5">
    <div class="card-header bg-secondary text-white">
        <h4 class="mb-0">Pet Details</h4>
    </div>
    <div class="card-body">
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="pet_id" value="<?= $current_pet['id'] ?>">
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($current_pet['name']) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="species" class="form-label">Species (e.g., Dog, Cat)</label>
                    <input type="text" class="form-control" id="species" name="species" value="<?= htmlspecialchars($current_pet['species']) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="age" class="form-label">Age (Years)</label>
                    <input type="number" class="form-control" id="age" name="age" value="<?= htmlspecialchars($current_pet['age']) ?>" required min="0">
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required><?= htmlspecialchars($current_pet['description']) ?></textarea>
            </div>
            
            <div class="mb-4 form-check">
                <input type="checkbox" class="form-check-input" id="is_adopted" name="is_adopted" value="1" <?= ($current_pet['is_adopted'] == 1) ? 'checked' : '' ?>>
                <label class="form-check-label" for="is_adopted">Mark as Already Adopted (Prevents new requests)</label>
            </div>

            <button type="submit" name="submit_pet" class="btn btn-success w-100 py-2">
                <i class="fas fa-save"></i> Update Pet Details
            </button>
            <a href="add_pet.php" class="btn btn-outline-secondary w-100 mt-2">Go back to Pet List/Add</a>
        </form>

        <?php if (!empty($current_images)): ?>
            <hr>
            <h5><i class="fas fa-images"></i> Current Images</h5>
            <div class="row">
                <?php foreach($current_images as $img): ?>
                <div class="col-md-3 mb-2 position-relative">
                    <img src="../uploads/<?= htmlspecialchars($img['image']) ?>" class="img-fluid rounded" alt="Pet Image" style="height: 150px; width: 100%; object-fit: cover;">
                    <a href="edit_pet.php?id=<?= $pet_id ?>&delete_image_id=<?= $img['id'] ?>" 
                       class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" 
                       onclick="return confirm('Remove this image?');"
                       title="Remove Image">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <hr>
        <div class="mb-4">
            <h5 class="mb-3"><i class="fas fa-upload"></i> Upload New Images</h5>
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="pet_id" value="<?= $current_pet['id'] ?>">
                <div class="input-group mb-3">
                    <input type="file" class="form-control" id="images" name="images[]" multiple accept="image/*" required>
                    <button class="btn btn-info text-white" type="submit" name="submit_pet_images"><i class="fas fa-upload"></i> Upload</button>
                </div>
                <small class="form-text text-muted">Select new images to add to this pet's gallery.</small>
            </form>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>