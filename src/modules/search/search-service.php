<?php
// /src/modules/search/SearchService.php
require_once __DIR__ . '/search-repository.php';

class SearchService {
    private SearchRepository $repository;

    // 1. SECURITY WHITELIST: Define exactly what tables can be searched
    private array $allowedTables = [
        'users' => [
            'select' => "user_id AS id, first_name || ' ' || last_name AS title, email AS subtitle",
            'where'  => "first_name ILIKE :q OR last_name ILIKE :q OR email ILIKE :q"
        ],
        'projects' => [
            'select' => "id, name AS title, project_location AS subtitle",
            'where'  => "name ILIKE :q OR project_location ILIKE :q"
        ]
    ];

    // Use Dependency Injection to pass the repository into the service
    public function __construct(SearchRepository $repository) {
        $this->repository = $repository;
    }

    public function globalSearch(string $table, string $searchQuery): array {
        // 2. Stop immediately if the requested table isn't in our secure whitelist
        if (!array_key_exists($table, $this->allowedTables)) {
            throw new Exception("Invalid search table.");
        }

        $config = $this->allowedTables[$table];

        // 3. Pass the validated, safe configuration down to the Data layer
        return $this->repository->searchTable(
            $table,
            $config['select'],
            $config['where'],
            $searchQuery
        );
    }
}