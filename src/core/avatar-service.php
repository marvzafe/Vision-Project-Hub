<?php
// /src/core/avatar-service.php

class AvatarService {
    
    // Define the default placeholder location as a constant
    public const PLACEHOLDER_URL = '/assets/media/avatar/placeholder.jpg';
    
    public static function renderAvatar(?string $avatarUrl, ?string $firstName, ?string $lastName, string $size = '40px', ?string $userId = null): string {
        
        // Attach the ID for Javascript to read[cite: 2]
        $dataAttr = $userId ? ' data-user-id="' . htmlspecialchars($userId) . '"' : ''; //[cite: 2]

        if (!empty($avatarUrl)) {
            // Note: Make sure to keep the crossorigin="anonymous" from our earlier fix
            return '<img src="' . htmlspecialchars($avatarUrl) . '" alt="Profile" class="avatar-img global-avatar-hover" ' . 
                   'referrerpolicy="no-referrer" crossorigin="anonymous" ' . $dataAttr . 
                   'style="width: ' . $size . '; height: ' . $size . '; border-radius: 50%; object-fit: cover; flex-shrink: 0; border: 1px solid var(--border-color); cursor: pointer;">';
        }

        $first = $firstName ?? ''; //[cite: 2]
        $last  = $lastName ?? ''; //[cite: 2]
        $initials = strtoupper(substr($first, 0, 1) . substr($last, 0, 1)); //[cite: 2]
        
        $hash = md5($first . $last); //[cite: 2]
        $color = '#' . substr($hash, 0, 6); //[cite: 2]

        return '<div class="avatar fallback-avatar global-avatar-hover" ' . $dataAttr . //[cite: 2]
               'style="width: ' . $size . '; height: ' . $size . '; background-color: ' . $color . '; border-radius: 50%; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; flex-shrink: 0; border: 1px solid var(--border-color); cursor: pointer;">' //[cite: 2]
               . htmlspecialchars($initials) . //[cite: 2]
               '</div>'; //[cite: 2]
    }

    /**
     * Downloads the external avatar and saves it locally. Returns the placeholder on failure.
     */
    public static function downloadAndCacheAvatar(string $userId, ?string $remoteUrl): string {
        if (empty($remoteUrl)) {
            return self::PLACEHOLDER_URL;
        }

        // If it's already a local URL, skip downloading
        if (strpos($remoteUrl, '/assets/media') === 0) {
            return $remoteUrl;
        }

        // Define absolute path for saving the file
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/assets/media/avatar';
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = $userId . '.jpg';
        $filePath = $uploadDir . '/' . $fileName;
        $localUrl = '/assets/media/avatar/' . $fileName;

        // Use cURL to fetch the image, which safely handles 429 status codes
        $ch = curl_init($remoteUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5); // 5-second timeout
        
        $imageBytes = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Only save if the request was successful (HTTP 200 OK)
        if ($httpCode === 200 && $imageBytes !== false) {
            file_put_contents($filePath, $imageBytes);
            return $localUrl;
        }

        // Fallback to placeholder if Google blocked the request (429) or it failed
        return self::PLACEHOLDER_URL;
    }

    public static function renderDepartmentIcon(?string $departmentName, string $size = '40px', ?string $departmentId = null): string {
        
        $dataAttr = $departmentId ? ' data-department-id="' . htmlspecialchars($departmentId) . '"' : '';
        $name = $departmentName ?? 'Unknown';

        // Map your specific departments to icons and colors
        $deptConfig = [
            'Procurement'     => ['icon' => '🛒', 'bg' => '#e8f5e9', 'color' => '#2e7d32'], // Green
            'Human Resources' => ['icon' => '👥', 'bg' => '#fce4ec', 'color' => '#c2185b'], // Pink
            'Sales'           => ['icon' => '📈', 'bg' => '#e3f2fd', 'color' => '#1565c0'], // Blue
            'Management'      => ['icon' => '👔', 'bg' => '#f3e5f5', 'color' => '#6a1b9a'], // Purple
            'Accounting'      => ['icon' => '💰', 'bg' => '#fff8e1', 'color' => '#f57f17'], // Yellow/Gold
            'Marketing'       => ['icon' => '📢', 'bg' => '#fff3e0', 'color' => '#e65100'], // Orange
            'I.T.'            => ['icon' => '💻', 'bg' => '#e0f7fa', 'color' => '#006064'], // Cyan
            'Engineering'     => ['icon' => '⚙️', 'bg' => '#eceff1', 'color' => '#37474f'], // Grey
            'Logistics'       => ['icon' => '🚚', 'bg' => '#efebe9', 'color' => '#4e342e'], // Brown
            'Operations'      => ['icon' => '🏭', 'bg' => '#e8eaf6', 'color' => '#283593']  // Indigo
        ];

        // Fallback styling if the department isn't in the list
        $config = $deptConfig[$name] ?? ['icon' => '🏢', 'bg' => '#f5f5f5', 'color' => '#616161'];

        return '<div class="avatar fallback-avatar global-avatar-hover" title="' . htmlspecialchars($name) . '" ' . $dataAttr . 
               'style="width: ' . $size . '; height: ' . $size . '; background-color: ' . $config['bg'] . '; color: ' . $config['color'] . '; border-radius: 20%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; border: 1px solid var(--border-color); cursor: pointer;">' 
               . $config['icon'] . 
               '</div>';
    }
}

