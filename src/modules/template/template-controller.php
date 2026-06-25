<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

// /src/modules/template/template-controller.php
require_once __DIR__ . '/template-repository.php';
require_once __DIR__ . '/template-service.php';

$repo = new TemplateRepository();
$service = new TemplateService($repo);

$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'create':
        $error = null;
        
        // Fetch categories for the dropdowns
        $categories = $service->getCategoriesForDropdowns();
        $materialCategories = $categories['materials'];
        $taskCategories = $categories['tasks'];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $service->createNewTemplate($_POST);
                header("Location: /src/modules/template/template-controller.php?action=list");
                exit;
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        
        $pageTitle = "Create Task Template";
        require_once __DIR__ . '/views/create.php';
        break;
        
        $pageTitle = "Create Task Template";
        require_once __DIR__ . '/views/create.php';
        break;

    case 'list':
    default:
        $groupedTemplates = $service->getGroupedTemplates();
        $pageTitle = "CRM - Task Templates";
        require_once __DIR__ . '/views/list.php';
        break;
}