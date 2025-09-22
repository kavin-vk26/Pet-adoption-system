<?php
// views/layout/header.php
// Note: The $user variable is available here because it's defined in index.php before this file is included.
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Pet Adoption</title>
  <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<header>
  <h1>🐾 Pet Adoption</h1>
  <nav>
    <a href="/">Home</a>
    <?php if ($user): ?>
      <a href="/?page=profile">My Profile</a>
      <?php if ($user['is_admin']): ?>
        <a href="/?page=admin_add">Add Pet</a>
      <?php endif; ?>
      <a href="/?page=logout">Logout</a>
    <?php else: ?>
      <a href="/?page=login">Login</a>
      <a href="/?page=register">Register</a>
    <?php endif; ?>
  </nav>
</header>
<main>
<?= flash() ?>