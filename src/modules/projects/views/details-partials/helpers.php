<?php
function formatDiscussionText($text) {
    $escaped = htmlspecialchars($text);
    $withMentions = preg_replace('/@([A-Za-z0-9_]+)/', '<a href="#" class="mention-badge">@$1</a>', $escaped);
    return nl2br($withMentions);
}

function getRelativeTime($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;

    if ($diff < 60) return 'Just now';
    
    $minutes = floor($diff / 60);
    if ($minutes < 60) return $minutes . 'm';
    
    $hours = floor($diff / 3600);
    if ($hours < 24) return $hours . 'h';
    
    $days = floor($diff / 86400);
    if ($days < 7) return $days . 'd';
    
    $weeks = floor($diff / 604800);
    if ($weeks < 52) return $weeks . 'w';
    
    $years = floor($diff / 31536000);
    return $years . 'y';
}
?>