<?php
// /src/modules/contact_verification/contact-controller.php
session_start();

require_once __DIR__ . '/contact-repository.php';
require_once __DIR__ . '/contact-service.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /src/modules/auth/auth-controller.php?action=login");
    exit;
}

$userId = $_SESSION['user_id'];
$repository = new ContactVerificationRepository();
$service = new ContactVerificationService($repository);

$action = $_GET['action'] ?? 'check';
$error = null;

// ROUTE: Process Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'submit') {
    $phoneDigits = $_POST['phone_digits'] ?? '';
    
    $result = $service->processAndSavePhoneNumber($userId, $phoneDigits);
    
    if ($result['success']) {
        header("Location: ?action=success");
        exit;
    } else {
        $error = $result['error'];
        $action = 'check'; // Fall back to showing the form with the error
    }
}

// ROUTE: View Forms
if ($action === 'check') {
    if (!$service->needsPhoneNumber($userId)) {
        // Already has phone, redirect to dashboard
        header("Location: /"); 
        exit;
    }
    require_once __DIR__ . '/views/phone-form.php';
    exit;
}

if ($action === 'success') {
    require_once __DIR__ . '/views/phone-success.php';
    exit;
}