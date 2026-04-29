<?php
// /src/core/avatar-service.php

class AvatarService {
    
    public static function renderAvatar(?string $avatarUrl, ?string $firstName, ?string $lastName, string $size = '40px'): string {
        
        // 1. Check if URL exists AND is properly formatted
        if (!empty($avatarUrl) && filter_var($avatarUrl, FILTER_VALIDATE_URL)) {
            
            // ADDED: referrerpolicy="no-referrer"
            return '<img src="' . htmlspecialchars($avatarUrl) . '" alt="Profile" class="avatar-img" ' . 
                   'referrerpolicy="no-referrer" ' . 
                   'style="width: ' . $size . '; height: ' . $size . '; border-radius: 50%; object-fit: cover; flex-shrink: 0; border: 1px solid var(--border-color);">';
        }

        // 2. Fallback: Generate Initials
        $first = $firstName ?? '';
        $last  = $lastName ?? '';
        $initials = strtoupper(substr($first, 0, 1) . substr($last, 0, 1));
        
        $hash = md5($first . $last);
        $color = '#' . substr($hash, 0, 6);

        return '<div class="avatar fallback-avatar" ' . 
               'style="width: ' . $size . '; height: ' . $size . '; background-color: ' . $color . '; border-radius: 50%; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; flex-shrink: 0; border: 1px solid var(--border-color);">' 
               . htmlspecialchars($initials) . 
               '</div>';
    }
}