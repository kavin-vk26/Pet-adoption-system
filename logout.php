<?php
session_start();
session_destroy();
// Fixed path to redirect to the correct root index.php
header("Location: index.php");
exit;
