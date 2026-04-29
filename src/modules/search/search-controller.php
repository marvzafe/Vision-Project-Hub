<?php
// /src/modules/search/search-controller.php
require_once __DIR__ . '/search-repository.php';
require_once __DIR__ . '/search-service.php';

header('Content-Type: application/json');

$table = $_GET['table'] ?? '';
$query = $_GET['q'] ?? '';

if (!empty($table) && !empty($query)) {
    try {
        // Instantiate the layers (Dependency Injection)
        $repository = new SearchRepository();
        $service = new SearchService($repository);

        // Execute the search via the Service layer
        $results = $service->globalSearch($table, $query);
        
        echo json_encode(['success' => true, 'data' => $results]);
        
    } catch (Exception $e) {
        // Optional: Set a 400 Bad Request status code for errors
        http_response_code(400); 
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    // If they delete the text box, return an empty array to clear the results
    echo json_encode(['success' => true, 'data' => []]);
}
exit;