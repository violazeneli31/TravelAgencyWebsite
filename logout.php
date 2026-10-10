<?php
// logout.php
session_start();

// Unset all session variables
$_SESSION = array();

// Finally, destroy the session
session_destroy();

// Redirect to index page
header("Location: index.php");
exit();
?>