<?php
    // End a logged in user's session and redirect to the login page
    session_start();
    session_destroy();
    header("Location: login.php");
?>