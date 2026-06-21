<?php
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If the user is logged in, ping their active status
if (isset($_SESSION['user_id'])) {
    require_once __DIR__ . '/../../src/modules/users/user-service.php';
    $globalUserService = new UserService();
    $globalUserService->pingLastSeen($_SESSION['user_id']);
}
?>