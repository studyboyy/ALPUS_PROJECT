<?php

namespace App\Livewire\Concerns;

use App\Models\Prodi;

trait UsesActiveProdi
{
    protected function activeProdiId(): ?int
    {
        $user = auth()->user();

        if ($user?->isAdmin()) {
            return (int) (session('admin_prodi_id')
                ?: Prodi::query()->where('code', '!=', 'ADMIN')->where('is_active', true)->orderBy('name')->value('id'));
        }

        return $user?->prodi_id ? (int) $user->prodi_id : null;
    }

    protected function prodiQuery(string $modelClass)
    {
        $query = $modelClass::query();
        $prodiId = $this->activeProdiId();

        if ($prodiId) {
            $table = (new $modelClass)->getTable();
            $query->withoutGlobalScope('prodi')->where($table.'.prodi_id', $prodiId);
        }

        return $query;
    }
}
