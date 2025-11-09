<?php
// Video Feed Configuration

// Video settings
define('VIDEO_FEED_SETTINGS', [
    'videos_per_page' => 5,
    'max_videos_per_request' => 20,
    'max_comment_length' => 500,
    'supported_video_formats' => ['mp4', 'webm', 'ogg'],
    'default_avatar' => '/assets/images/default-avatar.png',
    'default_thumbnail' => '/assets/images/video-placeholder.jpg'
]);

// Performance settings
define('PERFORMANCE_SETTINGS', [
    'enable_caching' => true,
    'cache_duration' => 300, // 5 minutes
    'lazy_load_threshold' => 1000, // pixels
    'video_preload' => 'metadata'
]);

// Security settings
define('SECURITY_SETTINGS', [
    'rate_limit_likes' => 10, // per minute
    'rate_limit_comments' => 5, // per minute
    'max_video_file_size' => 100 * 1024 * 1024, // 100MB
    'allowed_video_domains' => ['localhost', 'your-domain.com']
]);

// UI/UX settings
define('UI_SETTINGS', [
    'auto_play_muted' => true,
    'scroll_snap_enabled' => true,
    'show_video_progress' => false,
    'comments_modal_height' => '60vh',
    'action_button_size' => 'large'
]);

// Database table names (in case of custom prefixes)
define('DB_TABLES', [
    'videos' => 'videos',
    'users' => 'users',
    'video_likes' => 'video_likes',
    'video_comments' => 'video_comments',
    'video_shares' => 'video_shares'
]);

// Error messages
define('ERROR_MESSAGES', [
    'video_not_found' => 'Video not found or has been removed',
    'user_not_authenticated' => 'Please log in to perform this action',
    'comment_too_long' => 'Comment is too long (max 500 characters)',
    'rate_limit_exceeded' => 'You are performing actions too quickly. Please slow down.',
    'upload_failed' => 'Failed to upload video. Please try again.',
    'invalid_format' => 'Unsupported video format',
    'file_too_large' => 'Video file is too large'
]);

// Success messages
define('SUCCESS_MESSAGES', [
    'video_uploaded' => 'Video uploaded successfully!',
    'comment_added' => 'Comment added successfully',
    'video_liked' => 'Video liked!',
    'video_unliked' => 'Video unliked',
    'video_shared' => 'Video shared successfully'
]);
?>