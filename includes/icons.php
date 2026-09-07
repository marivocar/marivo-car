<?php
/**
 * Icônes SVG réutilisables (style trait, professionnel).
 * Usage : <?= icon('car') ?>
 */
function icon($name, $size = 20) {
    $icons = [
        'car' => '<path d="M5 17h14M5 17a2 2 0 1 1-4 0 2 2 0 0 1 4 0Zm14 0a2 2 0 1 0 4 0 2 2 0 0 0-4 0ZM3 17V11l2-5h10l4 5v6M3 11h16"/>',
        'refresh' => '<path d="M21 12a9 9 0 1 1-2.64-6.36M21 3v6h-6"/>',
        'truck' => '<path d="M1 3h13v13H1zM14 8h4l3 3v5h-7V8zM4.5 20a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3ZM17.5 20a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3Z"/>',
        'headset' => '<path d="M3 13a9 9 0 0 1 18 0M3 13v4a2 2 0 0 0 2 2h1v-7H4a1 1 0 0 0-1 1Zm18 0v4a2 2 0 0 1-2 2h-1v-7h2a1 1 0 0 1 1 1Zm-4 6a2 2 0 0 1-2 2h-2"/>',
        'check' => '<path d="M20 6 9 17l-5-5"/>',
        'shield' => '<path d="M12 2 4 5v6c0 5 3.5 8.5 8 11 4.5-2.5 8-6 8-11V5l-8-3Z"/>',
        'card' => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/>',
        'pin' => '<path d="M12 22s7-6.5 7-12a7 7 0 1 0-14 0c0 5.5 7 12 7 12Z"/><circle cx="12" cy="10" r="2.5"/>',
        'phone' => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.3 1.8.6 2.7a2 2 0 0 1-.4 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.5 2.7.6a2 2 0 0 1 1.7 2Z"/>',
        'globe' => '<circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15 15 0 0 1 0 20 15 15 0 0 1 0-20Z"/>',
        'chat' => '<path d="M21 15a4 4 0 0 1-4 4H8l-5 3V6a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v9Z"/>',
        'fuel' => '<path d="M3 22V6a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v16M3 22h10M3 10h10M14 8.5l3 2.5v7a1.5 1.5 0 0 0 3 0v-5.5L17 9"/>',
        'users' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm7 0a4 4 0 0 0 0-8"/>',
        'moon' => '<path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8Z"/>',
        'calendar' => '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>',
        'chevron' => '<path d="M6 9l6 6 6-6"/>',
        'mail' => '<path d="M4 4h16v16H4z"/><path d="m22 6-10 7L2 6"/>',
        'clock' => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
        'star' => '<path d="M12 2 15 9l7 1-5 5 1.5 7L12 18.5 5.5 22 7 15 2 10l7-1 3-7Z"/>',
        'chart' => '<path d="M3 3v18h18"/><path d="M18 17V9M13 17V5M8 17v-4"/>',
        'logout' => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/>',
    ];
    $path = $icons[$name] ?? $icons['check'];
    return '<svg width="'.$size.'" height="'.$size.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">'.$path.'</svg>';
}