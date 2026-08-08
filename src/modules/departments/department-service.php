<?php
// /src/modules/departments/department-service.php
require_once __DIR__ . '/department-repository.php';

class DepartmentService {
    
    private DepartmentRepository $repository;

    public function __construct(DepartmentRepository $repository) {
        $this->repository = $repository;
    }

    public function getDepartmentsList(): array {
        return $this->repository->getAllDepartments();
    }
    
    public function getDepartmentDetails(string $departmentId) {
        if (empty($departmentId)) {
            throw new Exception("Department ID is required.");
        }
        
        $department = $this->repository->getDepartmentById($departmentId);
        
        if (!$department) {
            throw new Exception("Department not found.");
        }
        
        return $department;
    }
}