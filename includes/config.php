<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$host = "localhost";
$user = "root";
$pass = "root"; // IMPORTANT: Set your MySQL root password here if you have one
$db = "pet_adoption";

$conn = new mysqli($host,$user,$pass,$db);
if($conn->connect_error){
    // Instead of die, we can redirect or log an error gracefully, but for development, die is often simplest.
    die("Database connection failed: (HY000/".$conn->connect_errno.") ".$conn->connect_error);
}
?>
