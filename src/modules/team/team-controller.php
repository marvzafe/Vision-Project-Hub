<?php
// /src/modules/team/team-controller.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

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
        // Grab the POST data
        $projectId = $_POST['project_id'] ?? null;
        $leadId    = $_POST['modal_project_lead_id'] ?? null;
        $userIds   = $_POST['modal_team_user_ids'] ?? [];
        $roles     = $_POST['modal_team_roles'] ?? [];

        try {
            // Let the service handle the looping and validation
            $newTeamIds = $teamService->createMembers($projectId, $leadId, $userIds, $roles);
            
            // Return an array of all the newly generated IDs!
            echo json_encode(['success' => true, 'team_ids' => $newTeamIds]);
            
        } catch (Exception $e) {
            // Catches validation errors (like missing project ID) or database errors
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
}