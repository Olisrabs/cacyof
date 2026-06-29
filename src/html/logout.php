<?php
session_start();

// Destroy the session cleanly
session_unset();
session_destroy();

// Start fresh session just to pass the success message
session_start();
$_SESSION['auth_error_type']    = 'success';
$_SESSION['auth_error_title']   = 'Logged Out!';
$_SESSION['auth_error_message'] = 'You have successfully logged out of CACYOF BOUESTI Admin Portal.';

header("Location: index.php");
exit();
?>