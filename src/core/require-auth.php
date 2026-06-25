<?php
// /src/core/require-auth.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If the session user_id isn't set, they aren't logged in!
if (!isset($_SESSION['user_id'])) {
    // Kick them back to the login route
    header("Location: /src/modules/auth/auth-controller.php?action=login");
    exit;
}