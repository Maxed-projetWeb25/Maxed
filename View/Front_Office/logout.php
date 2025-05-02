<?php
// Start the session
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Redirect to the login page
header("Refresh: 2; URL=../Front_Office/auth-boxed-login.php");

exit();
?>