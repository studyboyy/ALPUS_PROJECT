<div class="mx-auto max-w-[1440px] space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <div class="mb-2 inline-flex items-center gap-2 rounded-full bg-indigo-50 px-3 py-1 text-[10px] font-extrabold uppercase tracking-[0.16em] text-indigo-600">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2m7-10a4 4 0 100-8 4 4 0 000 8zm11 10v-2a4 4 0 00-3-3.87m-1-11.26a4 4 0 010 7.75"/></svg>
                Akses &amp; Program Studi
            </div>
            <h1 class="text-2xl font-extrabold tracking-tight text-zinc-900">Manajemen User dan Prodi</h1>
            <p class="mt-1 text-sm text-zinc-500">Kelola Program Studi, Kaprodi, Sekprodi, dan akun administrator dari satu tempat.</p>
        </div>
        <div class="hidden items-center gap-2 rounded-xl border border-zinc-200 bg-white px-3 py-2 text-xs font-semibold text-zinc-500 shadow-sm sm:flex">
            <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Sistem aktif
        </div>
    </div>

    @if (session('status'))
        <div class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700" role="status">
            <svg class="mt-0.5 h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            {{ session('status') }}
        </div>
    @endif

    <section class="overflow-hidden rounded-3xl border border-indigo-100 bg-white shadow-sm">
        <div class="flex items-start gap-3 border-b border-indigo-100 bg-gradient-to-r from-indigo-50 via-blue-50/60 to-white px-6 py-5">
            <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-indigo-600 text-white shadow-md shadow-indigo-200">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            </div>
            <div>
                <h2 class="text-base font-extrabold text-zinc-900">Tambah Program Studi + Kaprodi</h2>
                <p class="mt-1 text-xs leading-relaxed text-zinc-500">Data awal statistik, laporan, dokumen, profil, dan beranda akan disiapkan otomatis untuk prodi baru.</p>
            </div>
        </div>
        <form wire:submit="createProdiWithKaprodi" class="grid gap-x-5 gap-y-4 p-6 sm:grid-cols-2 xl:grid-cols-3">
            <label class="adm-field-label">Kode Prodi
                <input wire:model="newProdiCode" class="adm-input uppercase @error('newProdiCode') is-invalid @enderror" placeholder="Contoh: EKONOMI">
                @error('newProdiCode')<span class="mt-1 block text-xs font-medium text-rose-600">{{ $message }}</span>@enderror
            </label>
            <label class="adm-field-label">Nama Program Studi
                <input wire:model="newProdiName" class="adm-input @error('newProdiName') is-invalid @enderror" placeholder="Contoh: Ekonomi">
                @error('newProdiName')<span class="mt-1 block text-xs font-medium text-rose-600">{{ $message }}</span>@enderror
            </label>
            <label class="adm-field-label">Nama Kaprodi
                <input wire:model="newKaprodiName" class="adm-input @error('newKaprodiName') is-invalid @enderror" placeholder="Nama lengkap Kaprodi">
                @error('newKaprodiName')<span class="mt-1 block text-xs font-medium text-rose-600">{{ $message }}</span>@enderror
            </label>
            <label class="adm-field-label">Username Kaprodi
                <input wire:model="newKaprodiUsername" class="adm-input @error('newKaprodiUsername') is-invalid @enderror" placeholder="Contoh: kaprodi.ekonomi">
                @error('newKaprodiUsername')<span class="mt-1 block text-xs font-medium text-rose-600">{{ $message }}</span>@enderror
            </label>
            <label class="adm-field-label">Email Kaprodi
                <input wire:model="newKaprodiEmail" type="email" class="adm-input @error('newKaprodiEmail') is-invalid @enderror" placeholder="kaprodi.ekonomi@unwari.ac.id">
                @error('newKaprodiEmail')<span class="mt-1 block text-xs font-medium text-rose-600">{{ $message }}</span>@enderror
            </label>
            <label class="adm-field-label">Password Awal
                <input wire:model="newKaprodiPassword" type="password" class="adm-input @error('newKaprodiPassword') is-invalid @enderror" placeholder="Minimal 8 karakter">
                @error('newKaprodiPassword')<span class="mt-1 block text-xs font-medium text-rose-600">{{ $message }}</span>@enderror
            </label>
            <div class="flex items-center justify-between gap-4 border-t border-zinc-100 pt-4 sm:col-span-2 xl:col-span-3">
                <p class="hidden text-xs text-zinc-400 sm:block">Pastikan kode prodi dan email belum digunakan.</p>
                <button class="adm-btn-primary w-full sm:w-auto" type="submit" wire:loading.attr="disabled">
                    <svg wire:loading.remove wire:target="createProdiWithKaprodi" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    <svg wire:loading wire:target="createProdiWithKaprodi" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                    <span wire:loading.remove wire:target="createProdiWithKaprodi">Buat Prodi &amp; Kaprodi</span>
                    <span wire:loading wire:target="createProdiWithKaprodi">Menyiapkan data...</span>
                </button>
            </div>
        </form>
    </section>

    <div class="grid gap-6 xl:grid-cols-[minmax(300px,.78fr),minmax(0,1.22fr)]">
        <form wire:submit="save" class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm">
            <div class="mb-5 flex items-start gap-3 border-b border-zinc-100 pb-4">
                <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-zinc-100 text-zinc-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-6a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                </div>
                <div><h2 class="text-base font-extrabold text-zinc-900">{{ $editingId ? 'Edit Akun' : 'Tambah Akun' }}</h2><p class="mt-1 text-xs leading-relaxed text-zinc-500">Buat akun Kaprodi, Sekprodi, atau Admin tambahan.</p></div>
            </div>
            <div class="space-y-4">
                <label class="adm-field-label">Nama Lengkap<input wire:model="name" class="adm-input @error('name') is-invalid @enderror" placeholder="Nama lengkap pengguna">@error('name')<span class="mt-1 block text-xs font-medium text-rose-600">{{ $message }}</span>@enderror</label>
                <label class="adm-field-label">Username<input wire:model="username" class="adm-input @error('username') is-invalid @enderror" placeholder="Username unik">@error('username')<span class="mt-1 block text-xs font-medium text-rose-600">{{ $message }}</span>@enderror</label>
                <label class="adm-field-label">Email<input wire:model="email" type="email" class="adm-input @error('email') is-invalid @enderror" placeholder="nama@unwari.ac.id">@error('email')<span class="mt-1 block text-xs font-medium text-rose-600">{{ $message }}</span>@enderror</label>
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-1">
                    <label class="adm-field-label">Role<select wire:model="role" class="adm-input @error('role') is-invalid @enderror"><option value="kaprodi">Kaprodi</option><option value="sekprodi">Sekprodi</option><option value="admin">Admin</option></select>@error('role')<span class="mt-1 block text-xs font-medium text-rose-600">{{ $message }}</span>@enderror</label>
                    <label class="adm-field-label">Program Studi<select wire:model="prodi_id" class="adm-input @error('prodi_id') is-invalid @enderror">@foreach($prodis as $prodi)<option value="{{ $prodi->id }}">{{ $prodi->code }} — {{ $prodi->name }}</option>@endforeach</select>@error('prodi_id')<span class="mt-1 block text-xs font-medium text-rose-600">{{ $message }}</span>@enderror</label>
                </div>
                <label class="adm-field-label">Password<input wire:model="password" type="password" class="adm-input @error('password') is-invalid @enderror" placeholder="{{ $editingId ? 'Kosongkan jika tidak diganti' : 'Minimal 8 karakter' }}">@error('password')<span class="mt-1 block text-xs font-medium text-rose-600">{{ $message }}</span>@enderror</label>
                <div class="flex flex-wrap gap-2 border-t border-zinc-100 pt-4">
                    <button class="adm-btn-primary" type="submit" wire:loading.attr="disabled"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Simpan Akun</button>
                    <button type="button" wire:click="resetForm" class="adm-btn-secondary">Batal</button>
                </div>
            </div>
        </form>

        <section class="overflow-hidden rounded-3xl border border-zinc-200 bg-white shadow-sm">
            <div class="flex items-center justify-between gap-3 border-b border-zinc-200 px-5 py-4">
                <div><h2 class="text-base font-extrabold text-zinc-900">Daftar Pengguna</h2><p class="mt-1 text-xs text-zinc-500">{{ $users->count() }} akun terdaftar</p></div>
                <div class="grid h-9 w-9 place-items-center rounded-xl bg-indigo-50 text-indigo-600"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4-4a4 4 0 100-8 4 4 0 000 8zm6 0a3 3 0 100-6 3 3 0 000 6z"/></svg></div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[620px] text-left text-sm">
                    <thead><tr class="border-b border-zinc-200 bg-zinc-50/80 text-[10px] font-extrabold uppercase tracking-wider text-zinc-500"><th class="px-5 py-3">Pengguna</th><th class="px-4 py-3">Role</th><th class="px-4 py-3">Program Studi</th><th class="px-5 py-3 text-right">Aksi</th></tr></thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr class="border-b border-zinc-100 last:border-0 hover:bg-zinc-50/70">
                                <td class="px-5 py-4"><div class="flex items-center gap-3"><div class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-indigo-100 text-xs font-extrabold text-indigo-700">{{ $user->initials() }}</div><div class="min-w-0"><div class="truncate font-bold text-zinc-900">{{ $user->name }}</div><div class="mt-0.5 truncate text-xs text-zinc-500">&#64;{{ $user->username }} <span class="text-zinc-300">·</span> {{ $user->email }}</div></div></div></td>
                                <td class="px-4 py-4"><span class="inline-flex rounded-full bg-indigo-50 px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-wide text-indigo-700">{{ $user->role }}</span></td>
                                <td class="px-4 py-4 text-xs font-medium text-zinc-600">{{ $user->prodi?->name ?? 'Administrator' }}</td>
                                <td class="px-5 py-4 text-right"><div class="inline-flex items-center gap-1"><button wire:click="edit({{ $user->id }})" class="rounded-lg p-2 text-zinc-500 transition hover:bg-indigo-50 hover:text-indigo-700" title="Edit akun"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>@if($user->email !== env('ADMIN_EMAIL', 'admin@prodi.local'))<button wire:click="delete({{ $user->id }})" wire:confirm="Hapus akun ini?" class="rounded-lg p-2 text-zinc-500 transition hover:bg-rose-50 hover:text-rose-600" title="Hapus akun"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 7h12m-9 0v10m6-10v10M9 7V4h6v3m-9 0h12l-1 13H7L6 7z"/></svg></button>@endif</div></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-5 py-12 text-center text-sm text-zinc-400">Belum ada pengguna.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
