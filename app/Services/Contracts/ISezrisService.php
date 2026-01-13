<?php
// File: app/Contracts/ISezrisService.php

namespace App\Services\Contracts;

interface ISezrisService
{
    public function fetchData(array $filters): array;
}
