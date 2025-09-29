<?php
require '../includes/config.php';
if(!isset($_SESSION['user_id']) || $_SESSION['is_admin']!=1) {
    header("Location: ../index.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);
if ($id > 0) {
    $request_result = $conn->query("SELECT * FROM adoption_requests WHERE id=$id");
    if ($request_result->num_rows > 0) {
        $request = $request_result->fetch_assoc();
        
        // 1. Set request status to approved
        $conn->query("UPDATE adoption_requests SET status='approved' WHERE id=$id");
        
        // 2. Mark the pet as adopted
        $conn->query("UPDATE pets SET is_adopted=1 WHERE id=".$request['pet_id']);

        // 3. Reject all other pending requests for the same pet
        $conn->query("UPDATE adoption_requests SET status='rejected' WHERE pet_id=".$request['pet_id']." AND status='pending'");
    }
}

header("Location: dashboard.php");
exit;
