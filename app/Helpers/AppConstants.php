<?php

namespace App\Helpers;

class AppConstants
{
    // Status constants
    const STATUS_PENDING   = 'Pending';
    const STATUS_APPROVED  = 'Approved';
    const STATUS_REJECTED  = 'Rejected';
    const STATUS_RETURNED  = 'Returned';

    // Roles
    const ROLE_ADMIN       = 'Admin';
    const ROLE_USER        = 'User';
    const ROLE_MANAGER     = 'Manager';

    // Misc
    const DATE_FORMAT      = 'Y-m-d';
}
