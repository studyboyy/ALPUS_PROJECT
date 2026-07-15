<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->string('username', 80)->nullable()->unique()->after('name');
            });
        }

        DB::table('users')->orderBy('id')->get()->each(function ($user): void {
            if (! empty($user->username)) {
                return;
            }

            $base = $user->role === 'admin'
                ? (string) env('ADMIN_USERNAME', 'admin')
                : Str::of((string) $user->email)->before('@')->lower()->replaceMatches('/[^a-z0-9._-]+/', '.')->trim('.')->value();
            $base = $base !== '' ? $base : 'user';
            $candidate = $base;
            $suffix = 1;
            while (DB::table('users')->where('username', $candidate)->exists()) {
                $candidate = $base.'.'.$suffix++;
            }

            DB::table('users')->where('id', $user->id)->update(['username' => $candidate]);
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropUnique(['username']);
                $table->dropColumn('username');
            });
        }
    }
};
