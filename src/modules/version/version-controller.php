<?php
require_once 'version-service.php';

// Ensure no HTML is accidentally printed before the JSON
header('Content-Type: application/json');

$service = new VersionService();
$response = $service->getPatchNotes();

echo json_encode($response);
exit;