<?php
// /src/modules/team/team-controller.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

require_once __DIR__ . '/team-repository.php';
require_once __DIR__ . '/team-service.php';

// Tell the browser we are sending JSON back
header('Content-Type: application/json');

// Initialize the architecture
$repository = new TeamRepository();
$teamService = new TeamService($repository);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $action = $_POST['action'] ?? 'create';

    // --- DELETE LOGIC ---
    if ($action === 'delete') {
        $teamId = $_POST['team_id'] ?? null;
        
        try {
            $teamService->deleteMember($teamId);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    // --- CREATE LOGIC ---
    if ($action === 'create') {
        $projectId = $_POST['project_id'] ?? null;
        $leadId    = $_POST['modal_project_lead_id'] ?? null;
        $userIds   = $_POST['team_user_ids'] ?? [];
        $userRoles = $_POST['team_roles'] ?? [];
        $deptIds   = $_POST['team_department_ids'] ?? [];
        $deptRoles = $_POST['team_department_roles'] ?? [];
        $actorId   = $_SESSION['user_id'] ?? null;

        try {
            $newTeamIds = $teamService->createMembers($projectId, $leadId, $userIds, $userRoles, $deptIds, $deptRoles, $actorId);
            echo json_encode(['success' => true, 'team_ids' => $newTeamIds]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
}