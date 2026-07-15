<?php

namespace App\Http\Controllers;

use App\Models\Prodi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PublicProdiController extends Controller
{
    public function select(Request $request): RedirectResponse
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

        return back();
    }
}
