<?php
session_start();
session_unset();
session_destroy();
header("Location: login.php"); // Redirect back to the login page
exit();
?>