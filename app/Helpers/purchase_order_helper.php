<?php

if (!function_exists('get_status_color')) {
    function get_status_color($status)
    {
        return match ($status) {
            'draft' => 'secondary',
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'management' => 'primary',
            'completed' => 'info',
            default => 'light'
        };
    }
}
