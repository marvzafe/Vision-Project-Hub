<?php
// /src/modules/template/template-service.php
require_once __DIR__ . '/template-repository.php';

class TemplateService {
    private TemplateRepository $repository;

    public function __construct(TemplateRepository $repository) {
        $this->repository = $repository;
    }

    public function getGroupedTemplates() {
        $rawTemplates = $this->repository->getAllTaskTemplates();
        $grouped = [];

        // Nested Grouping: Category -> Material Name
        foreach ($rawTemplates as $row) {
            $matCategory = !empty($row['material_category']) ? $row['material_category'] : 'Uncategorized';
            $matName = !empty($row['material_name']) ? $row['material_name'] : 'Unknown Material';
            
            // 1. Initialize Category if it doesn't exist
            if (!isset($grouped[$matCategory])) {
                $grouped[$matCategory] = [];
            }
            
            // 2. Initialize Material Name inside the Category
            if (!isset($grouped[$matCategory][$matName])) {
                $grouped[$matCategory][$matName] = [];
            }

            // 3. Add the task details (removed material_name from here since it's now the folder title)
            $grouped[$matCategory][$matName][] = [
                'title'       => $row['title'],
                'category'    => $row['category'], 
                'days_offset' => $row['days_offset'],
                'sort_order'  => $row['sort_order'],
                'weight'      => $row['weight']
            ];
        }

        return $grouped;
    }

public function getCategoriesForDropdowns() {
        return [
            'materials' => $this->repository->getDistinctMaterialCategories(),
            'tasks'     => $this->repository->getDistinctTaskCategories()
        ];
    }

    public function createNewTemplate($postData) {
        if (empty($postData['material_category']) || empty($postData['material_name'])) {
            throw new Exception("Material Category and Name are required.");
        }

        if (empty($postData['task_titles'])) {
            throw new Exception("You must add at least one task to the template.");
        }

        // 1. Process Material Category (Add to ENUM if it's new)
        $materialCategory = trim($postData['material_category']);
        if ($materialCategory === 'add_new' && !empty($postData['new_material_category'])) {
            $materialCategory = trim($postData['new_material_category']);
            // Alters the Postgres Type before the transaction begins
            $this->repository->addEnumValue('material_category', $materialCategory);
        }
        $materialName = trim($postData['material_name']);

        // 2. Process Task Categories array (Add to ENUM if new ones were typed)
        $taskCategories = [];
        for ($i = 0; $i < count($postData['task_categories']); $i++) {
            $cat = trim($postData['task_categories'][$i]);
            
            if ($cat === 'add_new' && !empty($postData['new_task_categories'][$i])) {
                $cat = trim($postData['new_task_categories'][$i]);
                // Alters the Postgres Type before the transaction begins
                $this->repository->addEnumValue('task_category', $cat);
            }
            
            $taskCategories[] = $cat;
        }

        $tasks = [
            'titles'      => $postData['task_titles'],
            'categories'  => $taskCategories, 
            'days_offset' => $postData['task_days'],
            'weights'     => $postData['task_weights'],
            'sort_orders' => $postData['task_orders']
        ];

        // 3. Save the actual template records
        return $this->repository->saveTemplateTransaction($materialCategory, $materialName, $tasks);
    }
}