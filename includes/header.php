<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Set a default user name if the session is active but name is not set (e.g., just logged in)
if (isset($_SESSION['user_id']) && !isset($_SESSION['user_name'])) {
    // Note: You should ideally fetch this from the database in config.php or login.php
    $_SESSION['user_name'] = "User"; 
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pet Adoption System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,.08);
            background-color: #fff; /* Ensure it stands out */
        }
        .admin-navbar {
            background-color: #343a40 !important; /* Dark background for Admin */
        }
        .admin-navbar .nav-link, .admin-navbar .navbar-brand, .admin-navbar .dropdown-toggle {
            color: #ffffff !important;
        }
        .admin-navbar .nav-link:hover {
            color: #17a2b8 !important;
        }
        .container-main {
            padding-top: 20px;
            padding-bottom: 50px;
        }
    </style>
</head>
<body>

<?php
// Determine the base path for links (if in /admin/, links need '../', otherwise './')
$is_admin_area = (basename(dirname($_SERVER['PHP_SELF'])) == 'admin');
$path_prefix = $is_admin_area ? '../' : '';
$admin_class = (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) ? 'admin-navbar navbar-dark' : 'navbar-light bg-light';
?>

<nav class="navbar navbar-expand-lg <?= $admin_class ?> sticky-top">
    <div class="container-fluid container">
        <a class="navbar-brand" href="<?= $path_prefix ?>index.php">
            <i class="fas fa-home"></i> Pet Adoption
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if (isset($_SESSION['user_id'])): ?>
                    
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                        <!-- Admin Navigation Links -->
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $path_prefix ?>admin/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $path_prefix ?>admin/add_pet.php"><i class="fas fa-paw"></i> Manage Pets</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $path_prefix ?>admin/requests_list.php"><i class="fas fa-list-alt"></i> Requests</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownAdmin" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-shield"></i> Admin (<span class="fw-bold"><?= htmlspecialchars($_SESSION['user_name']) ?></span>)
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownAdmin">
                                <li><a class="dropdown-item" href="<?= $path_prefix ?>logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <!-- Regular User Navigation Links -->
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $path_prefix ?>index.php"><i class="fas fa-search"></i> Adopt a Pet</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownUser" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle"></i> Hi, <?= htmlspecialchars($_SESSION['user_name']) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownUser">
                                <li><a class="dropdown-item" href="<?= $path_prefix ?>profile.php"><i class="fas fa-address-card"></i> My Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?= $path_prefix ?>logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>

                <?php else: ?>
                    <!-- Public Navigation Links -->
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $path_prefix ?>index.php">Browse Pets</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-primary me-2" href="<?= $path_prefix ?>login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-primary text-white" href="<?= $path_prefix ?>register.php">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="container container-main">
<!-- The content of the individual page will start here -->