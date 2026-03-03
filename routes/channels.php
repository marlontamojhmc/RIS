<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('applications', function () {
    return true;
});