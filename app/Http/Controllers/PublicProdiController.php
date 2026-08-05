<?php

namespace App\Http\Controllers;

use App\Models\Prodi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PublicProdiController extends Controller
{
    private const REMEMBERED_PRODI_COOKIE = 'alpus_public_prodi_id';

    public function select(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'prodi_id' => ['required', 'integer', 'exists:prodis,id'],
        ]);

        $prodi = Prodi::query()
            ->whereKey((int) $validated['prodi_id'])
            ->where('code', '!=', 'ADMIN')
            ->where('is_active', true)
            ->firstOrFail();

        $request->session()->put('public_prodi_id', $prodi->id);

        $rememberedProdiCookie = cookie(
            self::REMEMBERED_PRODI_COOKIE,
            (string) $prodi->id,
            60 * 24 * 400,
            '/',
            null,
            $request->isSecure(),
            true,
            false,
            'lax',
        );

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'ok' => true,
                'prodi_id' => $prodi->id,
                'prodi_name' => $prodi->name,
                'prodi_code' => $prodi->code,
            ])->withCookie($rememberedProdiCookie);
        }

        return back()->withCookie($rememberedProdiCookie);
    }
}
