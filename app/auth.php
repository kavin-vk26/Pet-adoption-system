<?php
// app/auth.php
require_once __DIR__ . '/db.php';

function currentUser() {
    if (!empty($_SESSION['user_id'])) {
        $pdo = getPDO();
        $stmt = $pdo->prepare('SELECT id, name, email, is_admin FROM users WHERE id = :id');
        $stmt->execute(['id' => $_SESSION['user_id']]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    return null;
}

function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['flash'] = 'Please login first.';
        header('Location: /?page=login');
        exit;
    }
}
