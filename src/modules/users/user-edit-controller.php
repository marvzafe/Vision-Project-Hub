<?php
// /src/modules/users/user-edit-controller.php
require_once __DIR__ . '/user-repository.php';
require_once __DIR__ . '/user-service.php';

// TODO: Add an auth check here to ensure $_SESSION['user_role'] === 'admin'

$repository = new UserRepository();
$userService = new UserService($repository);

$departments = $userService->getDepartments();
// Check if the ID exists and is a valid number; if so, cast it to an integer. Otherwise, set to null.
$userId = (isset($_GET['id']) && is_numeric($_GET['id'])) ? (int) $_GET['id'] : null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $deptId = $_POST['department_id'] ?? null;
    $role   = $_POST['role'] ?? 'employee'; // Default role
    
    if ($userId && $deptId) {
        try {
            $userService->updateUserAssignment($userId, $deptId, $role);
            echo "<script>alert('User assigned successfully!'); window.location.href='/src/modules/users/user-list-controller.php';</script>";
            exit;
        } catch (PDOException $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    } else {
        $error = "Please select a department.";
    }
}

// Fetch the specific user's current data to pre-fill the form
$currentUser = $userService->getUserById($userId);

require_once __DIR__ . '/views/edit.php';