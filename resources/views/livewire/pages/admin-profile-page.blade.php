<div class="space-y-6">
    <section class="section-box rounded-3xl p-6 md:p-8">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="display-font text-4xl leading-tight">Profil Program Studi</h2>
                <p class="mt-2 text-sm text-(--muted)">Kelola sejarah, visi misi, struktur organisasi, SDM, dan
                    pencapaian Program Studi.</p>
            </div>
            <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">Profile CMS</span>
        </div>

    </section>

    @foreach ($sections as $index => $section)
        <section class="section-box rounded-3xl p-6 md:p-8">
            @if ($editingSlug === $section['slug'])
                <form wire:submit="simpanSection" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold">Edit: {{ $section['title'] }}</h3>
                        <button type="button" wire:click="cancelEdit"
                            class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <label class="text-sm">Judul
                            <input wire:model.defer="editTitle" type="text"
                                class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100" />
                        </label>
                        <label class="text-sm">Warna
                            <select wire:model.defer="editColorClass"
                                class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
                                <option value="blue">Biru</option>
                                <option value="violet">Violet</option>
                                <option value="emerald">Hijau</option>
                                <option value="amber">Kuning</option>
                            </select>
                        </label>
                        <label class="text-sm md:col-span-2">Ringkasan (untuk preview card)
                            <textarea wire:model.defer="editSummary" rows="2"
                                class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"
                                maxlength="255"></textarea>
                        </label>
                        <label class="text-sm md:col-span-2">Konten Lengkap
                            <textarea wire:model.defer="editContent" rows="8"
                                class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"></textarea>
                        </label>
                    </div>

                    <button type="submit"
                        class="rounded-2xl bg-linear-to-r from-indigo-600 to-blue-700 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-100">Simpan
                        Perubahan</button>
                </form>
            @else
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-bold">{{ $section['title'] }}</h3>
                        <p class="mt-2 text-sm text-(--muted)">{{ $section['summary'] }}</p>
                        <p class="mt-3 text-xs text-(--muted)">Warna: <span
                                class="font-semibold capitalize">{{ str_replace('-', ' to ', $section['color_class']) }}</span>
                        </p>
                    </div>
                    <button type="button" wire:click="editSection('{{ $section['slug'] }}')"
                        class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">Edit</button>
                </div>
            @endif
        </section>
    @endforeach
</div>
