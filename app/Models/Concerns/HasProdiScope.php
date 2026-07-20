<?php

namespace App\Models\Concerns;

use App\Models\Prodi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait HasProdiScope
{
    public static function bootHasProdiScope(): void
    {
        static::addGlobalScope('prodi', function (Builder $builder): void {
            $user = Auth::user();
            $prodiId = null;
            $isAdminPanel = static::isManagementRequest();

            if ($isAdminPanel) {
                $prodiId = $user?->role === 'admin'
                    ? session('admin_prodi_id')
                    : $user?->prodi_id;
                $prodiId = $prodiId ?: Prodi::query()->where('code', '!=', 'ADMIN')->where('is_active', true)->orderBy('name')->value('id');
            } elseif (! app()->runningInConsole()) {
                $prodiId = session('public_prodi_id')
                    ?: ($user && $user->role !== 'admin' ? $user->prodi_id : null)
                    ?: Prodi::query()->where('code', '!=', 'ADMIN')->where('is_active', true)->orderBy('name')->value('id');
            }

            if ($prodiId) {
                $builder->where($builder->getModel()->getTable().'.prodi_id', $prodiId);
            }
        });

        static::creating(function (Model $model): void {
            $user = Auth::user();
            if (! $model->getAttribute('prodi_id')) {
                $isAdminPanel = static::isManagementRequest();
                $prodiId = $isAdminPanel
                    ? ($user?->role === 'admin' ? session('admin_prodi_id') : $user?->prodi_id)
                    : ((! app()->runningInConsole() && ! $isAdminPanel) ? session('public_prodi_id') : null);
                $prodiId = $prodiId ?: Prodi::query()->where('code', '!=', 'ADMIN')->where('is_active', true)->orderBy('name')->value('id');
                if ($prodiId) {
                    $model->setAttribute('prodi_id', $prodiId);
                }
            }
        });

        // Kaprodi hanya boleh menambah record baru. Update dan hapus data lama
        // harus dilakukan oleh Admin, termasuk jika request dipalsukan dari UI.
        static::updating(function (): bool {
            return static::ensureCanModifyExistingData();
        });

        static::deleting(function (): bool {
            return static::ensureCanModifyExistingData();
        });
    }

    public function prodi()
    {
        return $this->belongsTo(Prodi::class);
    }

    public function scopeForProdi(Builder $query, ?int $prodiId): Builder
    {
        return $prodiId ? $query->where($this->getTable().'.prodi_id', $prodiId) : $query;
    }

    private static function isManagementRequest(): bool
    {
        if (app()->runningInConsole()) {
            return false;
        }

        if (request()->routeIs('admin.*')) {
            return true;
        }

        $refererPath = (string) parse_url((string) request()->headers->get('referer', ''), PHP_URL_PATH);

        return $refererPath === '/admin' || str_starts_with($refererPath, '/admin/');
    }

    private static function ensureCanModifyExistingData(): bool
    {
        if (static::isManagementRequest() && Auth::check() && ! Auth::user()?->isAdmin()) {
            return false;
        }

        return true;
    }
}
