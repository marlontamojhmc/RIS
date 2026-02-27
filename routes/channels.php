<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('applications', function ($user) {
    return true; // Or add logic: return $user->role === 'OSAC';
});