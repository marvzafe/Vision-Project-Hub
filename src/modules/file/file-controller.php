<?php
// /src/modules/files/file-controller.php
require_once __DIR__ . '/file-repository.php';
require_once __DIR__ . '/file-service.php';

// Instantiate our architecture layers
$repository = new FileRepository();
$fileService = new FileService($repository);

$projectId = $_GET['project_id'] ?? null;
$taskId    = $_GET['task_id'] ?? null;

if (!$projectId && !$taskId) {
    // ROUTE 1: ROOT DIRECTORY (View all Projects as Folders)
    $folders = $fileService->getProjectFolders();
    $viewMode = 'projects';

} elseif ($projectId && !$taskId) {
    // ROUTE 2: INSIDE A PROJECT (View Task Folders + Project Root Files)
    $project = $fileService->getProjectDetails($projectId);
    if (!$project) die("Project not found.");
    
    $taskFolders = $fileService->getTaskFolders($projectId);
    $files = $fileService->getProjectRootFiles($projectId); 
    
    $viewMode = 'tasks';

} elseif ($projectId && $taskId) {
    // ROUTE 3: INSIDE A TASK (View actual files)
    $project = $fileService->getProjectDetails($projectId);
    $task = $fileService->getTaskDetails($taskId);
    if (!$project || !$task) die("Folder not found.");
    
    $files = $fileService->getTaskFiles($taskId);
    $viewMode = 'task_files';
}

require_once __DIR__ . '/views/file-manager.php';