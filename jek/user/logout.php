<?php
session_start();
session_unset();
session_destroy();

// Redirect with a message
header("Location: login.php?message=You have been logged out successfully");
exit;
?>
