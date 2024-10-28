<?php
session_start();
session_destroy(); // Destroy all sessions
header('Location: index.html'); // Redirect to the login page
exit();
?>
