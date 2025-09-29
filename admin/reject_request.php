### `php:Reject Request:admin/reject_request.php`
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
    // Update the request status
    $conn->query("UPDATE adoption_requests SET status = 'rejected' WHERE id = $request_id AND status = 'pending'");
    
    if ($conn->affected_rows > 0) {
         $success = true;
         // NOTE: The pet status (is_adopted) remains unchanged, as it is still available.
    } else {
        $error = "Request $request_id was already actioned or not found.";
    }
    
    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    $error = 'Transaction failed: ' . $e->getMessage();
}

if ($success) {
    $_SESSION['message'] = '<div class="alert alert-warning"><i class="fas fa-times-circle"></i> Request rejected.</div>';
} else {
    $_SESSION['message'] = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> ' . $error . '</div>';
}

// Redirect back to the requests list, preserving the filter
header("Location: requests_list.php?status=" . $filter_status);
exit;
?>

