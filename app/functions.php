<?php
// app/functions.php
function flash() {
    if (!empty($_SESSION['flash'])) {
        $msg = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return "<div class='flash'>{$msg}</div>";
    }
    return "";
}

function redirect($url) {
    header('Location: ' . $url);
    exit;
}

function uploadImage($file) {
    if (!isset($file) || $file['error'] != UPLOAD_ERR_OK) return '';
    $allowed = ['image/jpeg','image/png','image/gif'];
    if (!in_array($file['type'], $allowed)) return '';
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $name = uniqid('pet_', true) . '.' . $ext;
    $dest = UPLOAD_DIR . $name;
    if (move_uploaded_file($file['tmp_name'], $dest)) {
        return $name;
    }
    return '';
}
