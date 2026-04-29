<?php
// /src/modules/search/SearchRepository.php
require_once __DIR__ . '/../../core/database.php';

class SearchRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Executes the search query against the database.
     * Note: $table, $selectClause, and $whereClause MUST be safely whitelisted
     * by the Service layer before being passed here to prevent SQL injection.
     */
    public function searchTable(string $table, string $selectClause, string $whereClause, string $searchQuery, int $limit = 10): array {
        $sql = "SELECT {$selectClause} FROM {$table} WHERE {$whereClause} LIMIT " . (int)$limit;
        
        $stmt = $this->db->prepare($sql);
        
        // Wrap the search query in % wildcards for partial matching
        $stmt->execute([':q' => "%" . $searchQuery . "%"]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}