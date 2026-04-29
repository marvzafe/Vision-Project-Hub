<?php
// /src/modules/dashboard/dashboard-service.php
require_once __DIR__ . '/dashboard-repository.php';

class DashboardService {
    private DashboardRepository $repository;

    public function __construct(DashboardRepository $repository) {
        $this->repository = $repository;
    }

    public function getProjectStats(): array {
        return $this->repository->getProjectStats();
    }

    public function getMyAssignedProjects(string $userId): array {
        return $this->repository->getMyAssignedProjects($userId);
    }

    // --- NEW METHOD ---
    public function getActiveUsers(): array {
        return $this->repository->getActiveUsers();
    }

    public function getTimeBasedGreeting(): string {
        date_default_timezone_set('Asia/Manila'); 
        $hour = (int) date('H');

        if ($hour < 12) {
            return "Good morning";
        } elseif ($hour < 18) {
            return "Good afternoon";
        } else {
            return "Good evening";
        }
    }

    public function getUserFirstName(array $sessionData): string {
        return $sessionData['first_name'] ?? (explode(' ', $sessionData['full_name'] ?? 'User')[0]);
    }

    // Add this inside the DashboardService class
    public function getUpcomingDeadlines(string $userId): array {
        return $this->repository->getUpcomingDeadlines($userId);
    }
}