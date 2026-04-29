<?php
// /src/modules/users/user-service.php
require_once __DIR__ . '/user-repository.php';

class UserService {
    private UserRepository $repository;

    public function __construct(UserRepository $repository) {
        $this->repository = $repository;
    }

    public function getDepartments() {
        return $this->repository->getDepartments();
    }

    public function createUser($first, $middle, $last, $email, $phone, $deptId) {
        return $this->repository->createUser($first, $middle, $last, $email, $phone, $deptId);
    }

    public function getAllUsers() {
        return $this->repository->getAllUsers();
    }

    public function getAllUserDetails() {
        return $this->repository->getAllUserDetails();
    }

    public function updateUserAssignment($userId, $deptId, $role) {
        return $this->repository->updateUserAssignment($userId, $deptId, $role);
    }

    public function getUserById($userId) {
        if (!$userId) return null;
        return $this->repository->getUserById($userId);
    }
}