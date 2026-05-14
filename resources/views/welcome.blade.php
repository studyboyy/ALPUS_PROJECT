<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Tahunan Kepala Program Studi</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Fraunces:opsz,wght@9..144,600;9..144,700&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --bg-main: #f4efe5;
            --bg-card: #fffdf9;
            --ink: #1f2430;
            --muted: #4b5563;
            --accent: #d94f30;
            --accent-soft: #ffddd2;
            --line: #e7dbca;
            --olive: #35524a;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Sora', sans-serif;
            background:
                radial-gradient(circle at 14% 10%, #ffe5cc 0%, transparent 28%),
                radial-gradient(circle at 86% 16%, #d5ede4 0%, transparent 22%),
                var(--bg-main);
            color: var(--ink);
        }

        .display-font {
            font-family: 'Fraunces', serif;
        }

        .grain {
            background-image: linear-gradient(120deg, rgba(0, 0, 0, 0.02) 12%, transparent 12%), linear-gradient(300deg, rgba(0, 0, 0, 0.02) 10%, transparent 10%);
            background-size: 22px 22px;
        }

        .section-box {
            background: var(--bg-card);
            border: 1px solid var(--line);
            box-shadow: 0 12px 30px rgba(20, 16, 10, 0.06);
        }

        .reveal {
            animation: riseIn 0.8s ease both;
        }

        .stagger-1 {
            animation-delay: 0.08s;
        }

        .stagger-2 {
            animation-delay: 0.16s;
        }

        .stagger-3 {
            animation-delay: 0.24s;
        }

        .stagger-4 {
            animation-delay: 0.32s;
        }

        @keyframes riseIn {
            from {
                opacity: 0;
                transform: translateY(24px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .mini-bar {
            height: 8px;
            border-radius: 999px;
            background: #eadfce;
            overflow: hidden;
        }

        .mini-bar>span {
            display: block;
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--accent), #ef8d44);
        }
    </style>
</head>

<body>
    <header class="sticky top-0 z-50 border-b border-(--line)/80 bg-(--bg-main)/90 backdrop-blur grain">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 md:px-8">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-(--olive)">Portal Pelaporan</p>
                <h1 class="display-font text-xl font-bold leading-tight md:text-2xl">Laporan Tahunan Kepala Program
                    Studi</h1>
            </div>
            <nav class="hidden gap-5 text-sm font-semibold lg:flex">
                <a href="#beranda" class="hover:text-(--accent)">Beranda</a>
                <a href="#profil" class="hover:text-(--accent)">Profil</a>
                <a href="#laporan" class="hover:text-(--accent)">Laporan</a>
                <a href="#statistik" class="hover:text-(--accent)">Statistik</a>
                <a href="#dokumen" class="hover:text-(--accent)">Dokumen</a>
                <a href="#galeri" class="hover:text-(--accent)">Galeri</a>
                <a href="#kontak" class="hover:text-(--accent)">Kontak</a>
            </nav>
        </div>
    </header>

    <main id="beranda" class="mx-auto flex max-w-7xl flex-col gap-12 px-4 py-10 md:px-8 md:py-14">
        <section class="section-box relative overflow-hidden rounded-3xl p-6 md:p-10">
            <div class="absolute -right-16 -top-16 h-52 w-52 rounded-full bg-(--accent-soft) blur-2xl"></div>
            <div class="absolute -bottom-24 left-1/3 h-56 w-56 rounded-full bg-[#d9ece3] blur-3xl"></div>

            <div class="relative grid gap-8 lg:grid-cols-[1.2fr,0.8fr] lg:items-end">
                <div class="reveal">
                    <p
                        class="mb-2 inline-block rounded-full bg-(--accent-soft) px-3 py-1 text-xs font-semibold uppercase tracking-[0.15em] text-(--accent)">
                        Tahun Akademik 2025</p>
                    <h2 class="display-font text-4xl leading-tight md:text-6xl">Menuju Prodi Unggul, Inovatif, dan
                        Berdaya Saing</h2>
                    <p class="mt-4 max-w-2xl text-sm leading-relaxed text-(--muted) md:text-base">
                        Ringkasan laporan tahunan, capaian akademik, statistik kinerja, dokumen pendukung, dan galeri
                        kegiatan dalam satu portal yang rapi dan mudah ditelusuri.
                    </p>
                    <div class="mt-6 flex flex-wrap gap-3">
                        <a href="#laporan"
                            class="rounded-full bg-(--accent) px-5 py-3 text-sm font-semibold text-white transition hover:-translate-y-0.5">Lihat
                            Laporan Tahunan</a>
                        <a href="#statistik"
                            class="rounded-full border border-(--line) bg-white px-5 py-3 text-sm font-semibold transition hover:border-(--accent)">Lihat
                            Statistik</a>
                    </div>
                </div>

                <div class="section-box reveal stagger-1 rounded-2xl p-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-(--olive)">Sambutan Kepala
                        Prodi</p>
                    <p class="mt-3 text-sm leading-relaxed text-(--muted)">
                        "Laporan tahunan ini adalah komitmen keterbukaan dan evaluasi berkelanjutan untuk memastikan
                        mutu pendidikan, riset, dan layanan kepada mahasiswa semakin meningkat dari tahun ke tahun."
                    </p>
                    <p class="mt-4 text-sm font-semibold">Dr. Nama Kepala Prodi</p>
                </div>
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <article class="section-box reveal stagger-1 rounded-2xl p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-(--olive)">Dosen & Penelitian</p>
                <h3 class="mt-2 text-lg font-bold">Kegiatan dan Luaran</h3>
                <ul class="mt-3 space-y-2 text-sm text-(--muted)">
                    <li><span class="font-semibold">Dokumen Profil DTPS</span> (PDF) - ringkasan kualifikasi dan beban
                        kerja.</li>
                    <li><span class="font-semibold">Laporan Penelitian & PkM 2025</span> (PDF) - daftar judul dan
                        pendanaan.</li>
                    <li><span class="font-semibold">Kumpulan Publikasi Dosen</span> (PDF) - jurnal, prosiding, dan
                        buku.</li>
                </ul>
                <a href="#dokumen" class="mt-4 inline-block text-sm font-semibold text-(--accent)">Lihat Detail
                    -></a>
            </article>
            <article class="section-box reveal stagger-2 rounded-2xl p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-(--olive)">Prestasi Mahasiswa</p>
                <h3 class="mt-2 text-lg font-bold">Sorotan Kegiatan</h3>
                <ul class="mt-3 space-y-2 text-sm text-(--muted)">
                    <li><span class="font-semibold">Juara 1 Lomba Inovasi</span> - dokumentasi poster dan sertifikat
                        kejuaraan tingkat nasional.</li>
                    <li><span class="font-semibold">Program Pengabdian Desa</span> - dokumentasi kegiatan dan dampak
                        sosial berbasis riset.</li>
                    <li><span class="font-semibold">Finalis PKM 2025</span> - dokumentasi presentasi dan ringkasan
                        riset mahasiswa.</li>
                </ul>
                <a href="#galeri" class="mt-4 inline-block text-sm font-semibold text-(--accent)">Lihat Detail
                    -></a>
            </article>
            <article class="section-box reveal stagger-3 rounded-2xl p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-(--olive)">Laporan 2025</p>
                <h3 class="mt-2 text-lg font-bold">Capaian dan Evaluasi</h3>
                <p class="mt-2 text-sm text-(--muted)">Sub-bab akademik, penelitian, prestasi, kegiatan eksternal,
                    dan keuangan.</p>
                <a href="#laporan" class="mt-4 inline-block text-sm font-semibold text-(--accent)">Lihat Detail
                    -></a>
            </article>
            <article class="section-box reveal stagger-4 rounded-2xl p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-(--olive)">Statistik Kinerja</p>
                <h3 class="mt-2 text-lg font-bold">Ringkasan Tahun Berjalan</h3>
                <p class="mt-2 text-sm text-(--muted)">Mahasiswa aktif, dosen, publikasi, dan status akreditasi.
                </p>
                <a href="#statistik" class="mt-4 inline-block text-sm font-semibold text-(--accent)">Lihat Detail
                    -></a>
            </article>
        </section>

        <section id="profil" class="grid gap-4 lg:grid-cols-3">
            <article class="section-box rounded-2xl p-6 lg:col-span-2">
                <h3 class="display-font text-3xl">Profil Program Studi</h3>
                <p class="mt-2 text-sm text-(--muted)">Sejarah, visi misi, struktur organisasi, SDM, dan capaian
                    utama program studi.</p>
                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-xl border border-(--line) p-4">
                        <h4 class="font-semibold">Sejarah dan Visi Misi</h4>
                        <p class="mt-2 text-sm text-(--muted)">Narasi singkat perjalanan prodi dan arah
                            pengembangan jangka panjang.</p>
                    </div>
                    <div class="rounded-xl border border-(--line) p-4">
                        <h4 class="font-semibold">Struktur Organisasi</h4>
                        <p class="mt-2 text-sm text-(--muted)">Kepala prodi, koordinator bidang, hingga unit
                            pendukung akademik.</p>
                    </div>
                    <div class="rounded-xl border border-(--line) p-4">
                        <h4 class="font-semibold">SDM Program Studi</h4>
                        <p class="mt-2 text-sm text-(--muted)">Data DTPS, tenaga kependidikan, dan kualifikasi
                            pendidik.</p>
                    </div>
                    <div class="rounded-xl border border-(--line) p-4">
                        <h4 class="font-semibold">Capaian & Penghargaan</h4>
                        <p class="mt-2 text-sm text-(--muted)">Sorotan pencapaian institusi, dosen, dan mahasiswa.
                        </p>
                    </div>
                </div>
            </article>

            <article class="section-box rounded-2xl p-6">
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-(--olive)">Highlight Cepat</p>
                <ul class="mt-4 space-y-4 text-sm">
                    <li class="rounded-xl border border-(--line) p-3"><strong>Akreditasi:</strong> Unggul</li>
                    <li class="rounded-xl border border-(--line) p-3"><strong>Rasio Dosen:</strong> 1 : 24</li>
                    <li class="rounded-xl border border-(--line) p-3"><strong>Publikasi 2025:</strong> 58 artikel
                    </li>
                    <li class="rounded-xl border border-(--line) p-3"><strong>Kerja Sama Aktif:</strong> 17 mitra
                    </li>
                </ul>
            </article>
        </section>

        <section id="laporan" class="section-box rounded-2xl p-6 md:p-8">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h3 class="display-font text-3xl">Laporan Tahunan</h3>
                <button class="rounded-full border border-(--line) bg-white px-4 py-2 text-sm font-semibold">Unduh
                    Laporan PDF</button>
            </div>
            <div class="mt-6 grid gap-4 md:grid-cols-3">
                <article class="rounded-xl border border-(--line) p-4">
                    <h4 class="text-lg font-bold">2025</h4>
                    <ul class="mt-3 space-y-2 text-sm text-(--muted)">
                        <li>Ringkasan Eksekutif</li>
                        <li>Capaian Kinerja Akademik</li>
                        <li>Penelitian & PkM</li>
                        <li>Prestasi Mahasiswa</li>
                        <li>Kerjasama & Kegiatan Eksternal</li>
                        <li>Keuangan & Anggaran</li>
                    </ul>
                </article>
                <article class="rounded-xl border border-(--line) p-4">
                    <h4 class="text-lg font-bold">2024</h4>
                    <p class="mt-3 text-sm text-(--muted)">Struktur bab serupa 2025 dengan pembanding capaian dan
                        evaluasi tindak lanjut.</p>
                </article>
                <article class="rounded-xl border border-(--line) p-4">
                    <h4 class="text-lg font-bold">2023</h4>
                    <p class="mt-3 text-sm text-(--muted)">Data historis untuk analisis tren performa program
                        studi tiga tahunan.</p>
                </article>
            </div>
        </section>

        <section id="statistik" class="grid gap-4 lg:grid-cols-2">
            <article class="section-box rounded-2xl p-6">
                <h3 class="display-font text-3xl">Data & Statistik</h3>
                <p class="mt-2 text-sm text-(--muted)">Visualisasi ringkas untuk tahun berjalan, dapat dilanjutkan
                    ke Chart.js di tahap backend.</p>
                <div class="mt-5 space-y-4 text-sm">
                    <div>
                        <div class="mb-1 flex justify-between"><span>Mahasiswa Aktif</span><span>92%</span></div>
                        <div class="mini-bar"><span style="width: 92%"></span></div>
                    </div>
                    <div>
                        <div class="mb-1 flex justify-between"><span>Lulusan Tepat Waktu</span><span>81%</span></div>
                        <div class="mini-bar"><span style="width: 81%"></span></div>
                    </div>
                    <div>
                        <div class="mb-1 flex justify-between"><span>Publikasi Ilmiah</span><span>74%</span></div>
                        <div class="mini-bar"><span style="width: 74%"></span></div>
                    </div>
                    <div>
                        <div class="mb-1 flex justify-between"><span>Kegiatan Dosen & Mahasiswa</span><span>88%</span>
                        </div>
                        <div class="mini-bar"><span style="width: 88%"></span></div>
                    </div>
                </div>
            </article>

            <article class="section-box rounded-2xl p-6">
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-(--olive)">Filter Tahunan</p>
                <h4 class="mt-2 text-xl font-bold">Pilih Tahun Data</h4>
                <div class="mt-4 flex flex-wrap gap-2">
                    <button class="rounded-full bg-(--accent) px-4 py-2 text-sm font-semibold text-white">2025</button>
                    <button
                        class="rounded-full border border-(--line) bg-white px-4 py-2 text-sm font-semibold">2024</button>
                    <button
                        class="rounded-full border border-(--line) bg-white px-4 py-2 text-sm font-semibold">2023</button>
                </div>
                <p class="mt-5 text-sm text-(--muted)">Komponen ini siap dijadikan dropdown/filter dinamis saat
                    backend Livewire sudah dibangun.</p>
            </article>
        </section>

        <section class="grid gap-4 lg:grid-cols-2">
            <article id="dokumen" class="section-box rounded-2xl p-6">
                <h3 class="display-font text-3xl">Dokumen Pendukung</h3>
                <ul class="mt-4 space-y-2 text-sm text-(--muted)">
                    <li>- Rencana Strategis (Renstra)</li>
                    <li>- Rencana Operasional (Renop)</li>
                    <li>- Standar Mutu Program Studi</li>
                    <li>- Notulen Rapat Evaluasi</li>
                </ul>
                <button class="mt-5 rounded-full border border-(--line) bg-white px-4 py-2 text-sm font-semibold">Lihat
                    Semua Dokumen</button>
            </article>

            <article id="galeri" class="section-box rounded-2xl p-6">
                <h3 class="display-font text-3xl">Galeri Kegiatan</h3>
                <p class="mt-2 text-sm text-(--muted)">Placeholder grid untuk akademik, kegiatan mahasiswa,
                    pengabdian masyarakat, dan kerja sama.</p>
                <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
                    <div class="h-20 rounded-xl bg-[#f7d7c5]"></div>
                    <div class="h-20 rounded-xl bg-[#dbe9e3]"></div>
                    <div class="h-20 rounded-xl bg-[#f1e2be]"></div>
                    <div class="h-20 rounded-xl bg-[#e8d9d1]"></div>
                </div>
            </article>
        </section>

        <section id="kontak" class="section-box rounded-2xl p-6 md:p-8">
            <div class="grid gap-6 lg:grid-cols-2">
                <div>
                    <h3 class="display-font text-3xl">Kontak & Umpan Balik</h3>
                    <p class="mt-2 text-sm text-(--muted)">Nantinya form ini bisa dihubungkan ke backend Livewire
                        untuk kirim masukan dan tindak lanjut.</p>
                    <ul class="mt-5 space-y-2 text-sm text-(--muted)">
                        <li>Email: sekretariat@prodi.ac.id</li>
                        <li>Alamat: Gedung Prodi, Kampus Utama</li>
                        <li>Media Sosial: Instagram | YouTube | LinkedIn</li>
                    </ul>
                </div>
                <form class="grid gap-3">
                    <input type="text" placeholder="Nama"
                        class="rounded-xl border border-(--line) bg-white px-4 py-3 text-sm outline-none focus:border-(--accent)">
                    <input type="email" placeholder="Email"
                        class="rounded-xl border border-(--line) bg-white px-4 py-3 text-sm outline-none focus:border-(--accent)">
                    <textarea rows="4" placeholder="Pesan / Saran"
                        class="rounded-xl border border-(--line) bg-white px-4 py-3 text-sm outline-none focus:border-(--accent)"></textarea>
                    <button type="button"
                        class="rounded-full bg-(--olive) px-5 py-3 text-sm font-semibold text-white">Kirim Umpan
                        Balik</button>
                </form>
            </div>
        </section>
    </main>

    <footer class="border-t border-(--line) py-6">
        <div
            class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-2 px-4 text-xs text-(--muted) md:flex-row md:px-8">
            <p>© 2026 Program Studi - Portal Laporan Tahunan</p>
            <p>Beranda | Profil | Laporan | Statistik | Dokumen | Galeri | Kontak</p>
        </div>
    </footer>
</body>

</html>
