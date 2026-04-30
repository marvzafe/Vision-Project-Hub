<?php
class VersionRepository {
    private $repoUrl = "https://api.github.com/repos/marvzafe/Vision-Project-Hub/commits";

    public function fetchLatestCommits($limit = 5) {
        $url = $this->repoUrl . "?per_page=" . $limit;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Vision-CRM-App'); 
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        // DELETED curl_close($ch); FROM HERE!

        if ($httpCode === 200 && $response) {
            return json_decode($response, true);
        }
        
        return null;
    }
}