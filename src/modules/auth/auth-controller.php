<?php
// /src/modules/auth/auth-controller.php
session_start();

require_once __DIR__ . '/auth-repository.php';
require_once __DIR__ . '/auth-service.php';

// Initialize the architecture
$repository = new AuthRepository();
$authService = new AuthService($repository);

// Check the URL for an action (e.g., ?action=login). If none exists, default to 'login'
$action = $_GET['action'] ?? 'login';

// ==========================================
// ROUTE 1: THE LOGIN PAGE (View)
// ==========================================
if ($action === 'login') {
    
    // If the user is already logged in, redirect them to the dashboard immediately
    if (isset($_SESSION['user_id'])) {
        header("Location: /");
        exit;
    }

    $pageTitle = "Login - Vision CRM";
    require_once __DIR__ . '/views/login.php';
    exit;
}

// ==========================================
// ROUTE 2: SET SESSION (Supabase API Bridge)
// ==========================================
if ($action === 'set_session' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Tell the browser we are explicitly sending JSON back
    header('Content-Type: application/json');

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    // Pass the payload to the Service layer to handle session building
    if ($data && $authService->setupUserSession($data)) {
        echo json_encode(['success' => true]);
        exit;
    }
    
    echo json_encode(['success' => false, 'error' => 'Invalid payload']);
    exit;
}

// ==========================================
// ROUTE 3: LOGOUT
// ==========================================
if ($action === 'logout') {
    
    // Pass the logout logic to the Service layer
    $authService->logoutUser();

    header("Location: /src/modules/auth/auth-controller.php?action=login&status=logged_out");
    exit;
}