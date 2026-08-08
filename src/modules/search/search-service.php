<?php
// /src/modules/search/SearchService.php
require_once __DIR__ . '/search-repository.php';

class SearchService {
    private SearchRepository $repository;

    private array $allowedTables = [
        'users' => [
            'select' => "user_id AS id, first_name || ' ' || last_name AS title, email AS subtitle",
            'where'  => "first_name ILIKE :q OR last_name ILIKE :q OR email ILIKE :q"
        ],
        'projects' => [
            'select' => "id, name AS title, project_location AS subtitle",
            'where'  => "name ILIKE :q OR project_location ILIKE :q"
        ],
        
        // --- NEW: The Combined Virtual Table ---
        'entities' => [
            // We use 'table_query' to pass our UNION instead of a standard table name
            'table_query' => "(
                SELECT 'user_' || user_id::text AS id, first_name || ' ' || last_name AS title, email AS subtitle 
                FROM users 
                UNION ALL 
                SELECT 'dept_' || department_id::text AS id, department_name AS title, 'Department' AS subtitle 
                FROM departments
            ) AS combined_view",
            'select' => "id, title, subtitle",
            'where'  => "title ILIKE :q OR subtitle ILIKE :q"
        ]
    ];

    public function __construct(SearchRepository $repository) {
        $this->repository = $repository;
    }

    public function globalSearch(string $table, string $searchQuery): array {
        if (!array_key_exists($table, $this->allowedTables)) {
            throw new Exception("Invalid search table.");
        }

        $config = $this->allowedTables[$table];
        
        // Use the 'table_query' if it exists, otherwise fall back to the standard $table string
        $actualTable = $config['table_query'] ?? $table; 

        return $this->repository->searchTable(
            $actualTable,
            $config['select'],
            $config['where'],
            $searchQuery
        );
    }
}