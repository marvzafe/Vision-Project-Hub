<?php
require_once 'version-repository.php';

class VersionService {
    private $repository;

    public function __construct() {
        $this->repository = new VersionRepository();
    }

    public function getPatchNotes() {
        $commits = $this->repository->fetchLatestCommits(6); // Get last 6 updates

        if (!$commits || isset($commits['message'])) {
            return [
                'success' => false, 
                'message' => 'Unable to fetch patch notes from GitHub.'
            ];
        }

        $formattedNotes = [];
        foreach ($commits as $commit) {
            $formattedNotes[] = [
                'version_hash' => substr($commit['sha'], 0, 7), // Short 7-character hash
                'message' => htmlspecialchars($commit['commit']['message']),
                'author' => htmlspecialchars($commit['commit']['author']['name']),
                'date' => date('F j, Y • g:i A', strtotime($commit['commit']['author']['date']))
            ];
        }

        return [
            'success' => true, 
            'data' => $formattedNotes
        ];
    }
}