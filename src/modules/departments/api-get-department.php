<?php
// /src/modules/departments/api-get-department.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

require_once __DIR__ . '/../../core/database.php';

header('Content-Type: application/json');

$departmentId = $_GET['id'] ?? null;
$projectId = $_GET['project_id'] ?? null;

if (!$departmentId) { 
    echo json_encode(['success' => false, 'message' => 'Department ID is required.']); 
    exit; 
}

try {
    $db = Database::getConnection();
    
    // 1. Fetch Department
    $deptStmt = $db->prepare("SELECT department_id, department_name FROM departments WHERE department_id = :id");
    $deptStmt->execute([':id' => $departmentId]);
    $department = $deptStmt->fetch(PDO::FETCH_ASSOC);

    if (!$department) {
        echo json_encode(['success' => false, 'message' => 'Department not found.']);
        exit;
    }

    // 2. Fetch Members
    $membersSql = "SELECT u.user_id, u.first_name, u.last_name, u.avatar_url, u.email, u.role as company_role";
    $params = [':did' => $departmentId];

    if ($projectId) {
        $membersSql .= ", pt.project_role 
                        FROM users u 
                        LEFT JOIN project_team pt ON u.user_id = pt.user_id AND pt.project_id = :pid 
                        WHERE u.department_id = :did";
        $params[':pid'] = $projectId;
    } else {
        $membersSql .= " FROM users u WHERE u.department_id = :did";
    }

    // Order by role
    $membersSql .= " ORDER BY CASE WHEN u.role::text ILIKE '%manager%' OR u.role::text ILIKE '%head%' THEN 1 ELSE 2 END, u.last_name ASC";
    
    $membersStmt = $db->prepare($membersSql);
    $membersStmt->execute($params);
    $members = $membersStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true, 
        'data' => [
            'department' => $department,
            'members'    => $members
        ]
    ]);
    
} catch (Throwable $e) { // Changed to Throwable to catch fatal PHP errors too
    http_response_code(500);
    // Explicitly return the exact error message to the browser
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage(),
        'line' => $e->getLine()
    ]);
}