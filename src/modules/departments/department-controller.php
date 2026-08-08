<?php
// /src/modules/departments/department-controller.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

require_once __DIR__ . '/department-repository.php';
require_once __DIR__ . '/department-service.php';

// Always return JSON for this endpoint
header('Content-Type: application/json');

// Instantiate the architecture
$repository = new DepartmentRepository();
$service = new DepartmentService($repository);

$action = $_GET['action'] ?? 'list';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    
    // Action: Fetch all departments
    if ($action === 'list') {
        try {
            $departments = $service->getDepartmentsList();
            echo json_encode(['success' => true, 'data' => $departments]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
    
    // Action: Fetch a single department
    if ($action === 'view') {
        $deptId = $_GET['id'] ?? null;
        try {
            $department = $service->getDepartmentDetails($deptId);
            echo json_encode(['success' => true, 'data' => $department]);
        } catch (Exception $e) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
}

// Fallback for unsupported methods
http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
exit;