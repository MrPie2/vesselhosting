<?php

namespace App\Contracts;

use App\Models\HostingAccount;

interface HostingProvisioner
{
    public function create(HostingAccount $account): array;
    public function suspend(HostingAccount $account): void;
    public function unsuspend(HostingAccount $account): void;
    public function terminate(HostingAccount $account): void;
}