<?php

namespace App\Http\Controllers;

use App\Models\Prodi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PublicProdiController extends Controller
{
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

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'ok' => true,
                'prodi_id' => $prodi->id,
                'prodi_name' => $prodi->name,
                'prodi_code' => $prodi->code,
            ]);
        }

        return back();
    }
}
