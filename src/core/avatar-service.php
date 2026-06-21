<?php
// /src/core/avatar-service.php

class AvatarService {
    
    // NEW: Added $userId as the 5th parameter
    public static function renderAvatar(?string $avatarUrl, ?string $firstName, ?string $lastName, string $size = '40px', ?string $userId = null): string {
        
        // Attach the ID for Javascript to read
        $dataAttr = $userId ? ' data-user-id="' . htmlspecialchars($userId) . '"' : '';

        if (!empty($avatarUrl)) {
            return '<img src="' . htmlspecialchars($avatarUrl) . '" alt="Profile" class="avatar-img global-avatar-hover" ' . 
                   'referrerpolicy="no-referrer" ' . $dataAttr . 
                   'style="width: ' . $size . '; height: ' . $size . '; border-radius: 50%; object-fit: cover; flex-shrink: 0; border: 1px solid var(--border-color); cursor: pointer;">';
        }

        $first = $firstName ?? '';
        $last  = $lastName ?? '';
        $initials = strtoupper(substr($first, 0, 1) . substr($last, 0, 1));
        
        $hash = md5($first . $last);
        $color = '#' . substr($hash, 0, 6);

        return '<div class="avatar fallback-avatar global-avatar-hover" ' . $dataAttr . 
               'style="width: ' . $size . '; height: ' . $size . '; background-color: ' . $color . '; border-radius: 50%; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; flex-shrink: 0; border: 1px solid var(--border-color); cursor: pointer;">' 
               . htmlspecialchars($initials) . 
               '</div>';
    }
}