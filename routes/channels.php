<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('applications', function () {
    return true;
});

Broadcast::channel('notifications',function (){
    return true;
});

