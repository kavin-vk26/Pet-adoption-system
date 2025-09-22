<?php
// app/config.php
// Edit DB credentials here
define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
define('DB_NAME', 'pet_adoption');
define('DB_USER', 'pet_user');
define('DB_PASS', 'pet_pass');

define('BASE_URL', '/'); // if hosted in subfolder, update
define('UPLOAD_DIR', __DIR__ . '/../public/uploads/');
define('UPLOAD_URL', BASE_URL . 'uploads/');
session_start();
