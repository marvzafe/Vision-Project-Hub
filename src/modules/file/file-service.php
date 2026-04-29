<?php
// /src/modules/files/FileService.php
require_once __DIR__ . '/file-repository.php';

class FileService {
    private FileRepository $repository;

    public function __construct(FileRepository $repository) {
        $this->repository = $repository;
    }

    public function getProjectFolders() {
        return $this->repository->getProjectFolders();
    }

    public function getProjectDetails($projectId) {
        return $this->repository->getProjectById($projectId);
    }

    public function getTaskFolders($projectId) {
        return $this->repository->getTaskFoldersByProject($projectId);
    }

    public function getProjectRootFiles($projectId) {
        return $this->repository->getProjectRootFiles($projectId);
    }

    public function getTaskDetails($taskId) {
        return $this->repository->getTaskById($taskId);
    }

    public function getTaskFiles($taskId) {
        return $this->repository->getFilesByTask($taskId);
    }

    // Business Logic / Utility helper
    public static function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}