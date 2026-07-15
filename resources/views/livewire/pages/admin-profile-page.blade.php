<div class="space-y-5">

    {{-- ── Header ── --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-indigo-400">CMS Profil</p>
            <h2 class="mt-0.5 text-lg font-extrabold text-zinc-800">Profil Program Studi</h2>
            <p class="mt-0.5 text-xs text-zinc-500">Kelola sejarah, visi misi, struktur organisasi, SDM, dan capaian strategis.</p>
        </div>
        <span class="rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
            {{ count($sections) }} bagian profil{{ $selectedProdi ? ' · '.$selectedProdi->name : '' }}
        </span>
    </div>

    @if (auth()->user()?->isAdmin())
        <div class="rounded-2xl border border-zinc-200 bg-white p-2 shadow-sm">
            <div class="flex gap-2 overflow-x-auto" role="tablist" aria-label="Pilih program studi">
                @foreach ($prodis as $prodi)
                    <button type="button" role="tab"
                        wire:click="pilihProdi({{ $prodi->id }})"
                        wire:loading.attr="disabled"
                        aria-selected="{{ $selectedProdiId === $prodi->id ? 'true' : 'false' }}"
                        class="shrink-0 rounded-xl px-4 py-2.5 text-sm font-semibold transition {{ $selectedProdiId === $prodi->id ? 'bg-indigo-600 text-white shadow-sm' : 'text-zinc-600 hover:bg-indigo-50 hover:text-indigo-700' }}">
                        <span class="mr-1.5 inline-block h-2 w-2 rounded-full {{ $selectedProdiId === $prodi->id ? 'bg-white' : 'bg-indigo-300' }}"></span>
                        {{ $prodi->code }} — {{ $prodi->name }}
                    </button>
                @endforeach
            </div>
        </div>
    @elseif ($selectedProdi)
        <div class="inline-flex items-center gap-2 rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-2 text-sm font-semibold text-indigo-700">
            <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
            {{ $selectedProdi->code }} — {{ $selectedProdi->name }}
        </div>
    @endif

    {{-- ── Sections ── --}}
    @foreach ($sections as $index => $section)
        <div class="section-box rounded-2xl p-5" wire:key="section-{{ $selectedProdiId }}-{{ $section['slug'] }}">
            @if ($editingSlug === $section['slug'])
                {{-- Edit mode ── --}}
                <form wire:submit="simpanSection" class="space-y-4">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-indigo-400">Mode Edit</p>
                            <h3 class="text-sm font-bold text-zinc-800">{{ $section['title'] }}</h3>
                        </div>
                        <button type="button" wire:click="cancelEdit"
                            class="inline-flex items-center gap-1.5 rounded-xl border border-zinc-300 bg-white px-3 py-1.5 text-xs font-semibold text-zinc-600 hover:bg-zinc-50">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            Batal
                        </button>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-zinc-600">Judul</label>
                            <input wire:model.defer="editTitle" type="text"
                                class="w-full rounded-xl border border-zinc-300 bg-white px-3.5 py-2.5 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"/>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-zinc-600">Warna Aksen</label>
                            <select wire:model.defer="editColorClass"
                                class="w-full rounded-xl border border-zinc-300 bg-white px-3.5 py-2.5 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
                                <option value="blue">Biru</option>
                                <option value="violet">Violet</option>
                                <option value="emerald">Hijau</option>
                                <option value="amber">Kuning</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="mb-1.5 block text-xs font-semibold text-zinc-600">Ringkasan <span class="text-zinc-400">(tampil di card preview, maks 255 karakter)</span></label>
                            <textarea wire:model.defer="editSummary" rows="2" maxlength="255"
                                class="w-full resize-none rounded-xl border border-zinc-300 bg-white px-3.5 py-2.5 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="mb-1.5 block text-xs font-semibold text-zinc-600">Konten Lengkap</label>
                            <textarea wire:model.defer="editContent" rows="8"
                                class="w-full resize-none rounded-xl border border-zinc-300 bg-white px-3.5 py-2.5 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end border-t border-zinc-100 pt-4">
                        <button type="submit"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 active:scale-95 disabled:opacity-60">
                            <svg wire:loading.remove class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <svg wire:loading class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            @else
                {{-- View mode ── --}}
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2.5">
                            @php
                                $dotColors = ['blue'=>'bg-blue-500','violet'=>'bg-violet-500','emerald'=>'bg-emerald-500','amber'=>'bg-amber-500'];
                                $dot = $dotColors[$section['color_class'] ?? 'blue'] ?? 'bg-blue-500';
                            @endphp
                            <span class="h-2.5 w-2.5 flex-shrink-0 rounded-full {{ $dot }}"></span>
                            <h3 class="text-sm font-bold text-zinc-800">{{ $section['title'] }}</h3>
                        </div>
                        <p class="mt-2 text-sm leading-relaxed text-zinc-500 line-clamp-2">{{ $section['summary'] }}</p>
                    </div>
                    <button type="button" wire:click="editSection('{{ $section['slug'] }}')"
                        class="inline-flex flex-shrink-0 items-center gap-1.5 rounded-xl border border-zinc-300 bg-white px-3.5 py-2 text-xs font-semibold text-zinc-600 shadow-sm transition hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-700">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                    </button>
                </div>
            @endif
        </div>
    @endforeach

    @if (empty($sections))
        <div class="flex flex-col items-center gap-3 rounded-2xl border-2 border-dashed border-zinc-200 py-12 text-center">
            <svg class="h-8 w-8 text-zinc-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <p class="text-sm text-zinc-400">Belum ada bagian profil.</p>
        </div>
    @endif

</div>
