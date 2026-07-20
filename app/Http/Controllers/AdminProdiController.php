<?php

namespace App\Http\Controllers;

use App\Models\Prodi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminProdiController extends Controller
{
    public function select(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $validated = $request->validate([
            'prodi_id' => ['required', 'integer'],
        ]);

        $prodi = Prodi::query()
            ->whereKey($validated['prodi_id'])
            ->where('code', '!=', 'ADMIN')
            ->where('is_active', true)
            ->firstOrFail();

        $request->session()->put('admin_prodi_id', $prodi->id);
        // Portal yang dibuka dari panel admin langsung menampilkan prodi yang sama.
        $request->session()->put('public_prodi_id', $prodi->id);

        return back()->with('status', 'Program studi aktif diubah ke '.$prodi->name.'.');
    }
}
