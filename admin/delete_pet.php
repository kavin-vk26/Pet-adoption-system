### `php:Delete Pet:admin/delete_pet.php`
```php
<?php
require '../includes/config.php';

// Security Check: Only allow logged-in admins
if(!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$pet_id = (int)$_GET['id'];
$success = false;
$error = '';

// Start transaction
$conn->begin_transaction();
try {
    // 1. Fetch images to delete from filesystem
    $images_to_delete = $conn->query("SELECT image FROM pet_images WHERE pet_id = $pet_id");
    
    // 2. Delete all adoption requests associated with the pet
    $conn->query("DELETE FROM adoption_requests WHERE pet_id = $pet_id");
    
    // 3. Delete pet images records
    $conn->query("DELETE FROM pet_images WHERE pet_id = $pet_id");
    
    // 4. Delete the pet itself
    $conn->query("DELETE FROM pets WHERE id = $pet_id");
    
    $conn->commit();
    $success = true;

    // 5. Delete images from filesystem after successful DB commit
    if ($images_to_delete) {
        while ($img = $images_to_delete->fetch_assoc()) {
            @unlink('../uploads/' . $img['image']);
        }
    }

} catch (Exception $e) {
    $conn->rollback();
    $error = 'Error deleting pet: ' . $e->getMessage();
}

if ($success) {
    $_SESSION['message'] = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Pet and associated data deleted successfully.</div>';
} else {
    $_SESSION['message'] = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> ' . $error . '</div>';
}

header("Location: add_pet.php"); // Redirect back to the pet management view
exit;
?>


### `php:Approve Request:admin/approve_request.php`
```php
<?php
require '../includes/config.php';

// Security Check: Only allow logged-in admins
if(!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$request_id = (int)$_GET['id'];
$filter_status = $_GET['status'] ?? 'pending';
$success = false;
$error = '';

$conn->begin_transaction();
try {
    // 1. Update the request status
    $conn->query("UPDATE adoption_requests SET status = 'approved' WHERE id = $request_id AND status = 'pending'");

    // Check if update was successful (and it was pending)
    if ($conn->affected_rows > 0) {
        // 2. Mark the pet as adopted (Prevent future requests)
        $pet_id_result = $conn->query("SELECT pet_id FROM adoption_requests WHERE id = $request_id")->fetch_assoc();
        if ($pet_id_result) {
            $pet_id = $pet_id_result['pet_id'];
            $conn->query("UPDATE pets SET is_adopted = 1 WHERE id = $pet_id");

            // 3. Reject all other pending requests for the same pet
            $conn->query("UPDATE adoption_requests SET status = 'rejected' WHERE pet_id = $pet_id AND status = 'pending' AND id != $request_id");
        }
        $success = true;
    } else {
        $error = "Request $request_id was already actioned or not found.";
    }
    
    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    $error = 'Transaction failed: ' . $e->getMessage();
}

if ($success) {
    $_SESSION['message'] = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Request approved! Pet marked as adopted and other pending requests rejected.</div>';
} else {
    $_SESSION['message'] = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> ' . $error . '</div>';
}

// Redirect back to the requests list, preserving the filter
header("Location: requests_list.php?status=" . $filter_status);
exit;
?>
