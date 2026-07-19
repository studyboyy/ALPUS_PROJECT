<?php

namespace App\Services;

use App\Models\Prodi;

class ProdiProvisioner
{
    public function __construct(private readonly ProdiDemoDataSeeder $demoDataSeeder)
    {
    }

    public function cloneStarterData(Prodi $target, ?string $kaprodiName = null): void
    {
        $this->demoDataSeeder->seed($target, $kaprodiName, replace: true);
    }
}
