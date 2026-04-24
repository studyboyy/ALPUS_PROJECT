<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;700;800&family=DM+Serif+Display:ital@0;1&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --night: #061525;
            --night-soft: #0a1f36;
            --sun: #f59e0b;
            --cyan: #06b6d4;
            --card: #ffffff;
            --ink: #10213a;
            --muted: #5f7390;
        }

        body {
            font-family: 'Manrope', sans-serif;
            background:
                radial-gradient(900px 500px at 8% 15%, rgba(6, 182, 212, 0.22), transparent 56%),
                radial-gradient(700px 480px at 92% 20%, rgba(245, 158, 11, 0.25), transparent 58%),
                linear-gradient(145deg, var(--night), var(--night-soft));
            min-height: 100vh;
            color: var(--ink);
        }

        .glass {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.11);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .login-card {
            background: var(--card);
            border: 1px solid #d8e4f2;
            box-shadow: 0 28px 70px rgba(4, 16, 34, 0.35);
        }

        .display-font {
            font-family: 'DM Serif Display', serif;
        }

        .btn-primary {
            background: linear-gradient(90deg, #0ea5e9, #2563eb);
            box-shadow: 0 14px 28px rgba(37, 99, 235, 0.34);
        }
    </style>
</head>

<body class="p-4 md:p-8">
    <main class="mx-auto grid min-h-[calc(100vh-2rem)] w-full max-w-6xl items-center gap-6 lg:grid-cols-[1.1fr,0.9fr]">
        <section class="glass hidden rounded-3xl p-8 text-white lg:block">
            <p
                class="inline-block rounded-full bg-white/20 px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em]">
                Dashboard Admin</p>
            <h1 class="display-font mt-5 text-5xl leading-tight">Kontrol Penuh Data Laporan Tahunan</h1>
            <p class="mt-5 max-w-xl text-sm leading-relaxed text-sky-100">Kelola statistik, grafik, indikator, dan
                agenda
                prodi secara real-time dalam satu panel modern. Semua perubahan langsung tercermin di dashboard publik.
            </p>
            <div class="mt-8 grid gap-3 md:grid-cols-2">
                <div class="rounded-2xl bg-white/10 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-cyan-200">Keamanan</p>
                    <p class="mt-2 text-sm font-semibold">Akses panel hanya untuk admin terotorisasi.</p>
                </div>
                <div class="rounded-2xl bg-white/10 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-amber-200">Efisiensi</p>
                    <p class="mt-2 text-sm font-semibold">Update angka & konten tanpa edit kode manual.</p>
                </div>
            </div>
        </section>

        <section class="login-card w-full rounded-3xl p-6 md:p-8">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-sky-700">Admin Access</p>
            <h2 class="display-font mt-3 text-4xl leading-tight text-(--ink)">Masuk ke Panel</h2>
            <p class="mt-2 text-sm text-(--muted)">Gunakan username/email dan password admin untuk melanjutkan.</p>

            @if ($errors->any())
                <div class="mt-5 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.submit') }}" class="mt-6 space-y-4">
                @csrf
                <label class="block text-sm font-semibold text-slate-700">Username atau Email
                    <input name="login" type="text" value="{{ old('login') }}"
                        class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-200"
                        placeholder="admin atau admin@prodi.local" required>
                </label>
                <label class="block text-sm font-semibold text-slate-700">Password
                    <input name="password" type="password"
                        class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-200"
                        placeholder="Masukkan password" required>
                </label>
                <button type="submit"
                    class="btn-primary w-full rounded-2xl px-4 py-3 text-sm font-bold text-white transition hover:-translate-y-0.5">Masuk
                    Admin Dashboard</button>
            </form>

            <p class="mt-5 text-center text-xs text-slate-500">Halaman ini bersifat privat. Aktivitas login tercatat
                pada
                sesi browser.</p>
        </section>
    </main>
</body>

</html>
