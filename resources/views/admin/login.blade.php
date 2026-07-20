<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Portal Kaprodi Jurusan</title>
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

        .login-field {
            display: block;
            color: #334155;
            font-size: .875rem;
            font-weight: 700;
        }

        .login-control {
            display: block;
            width: 100%;
            min-height: 3rem;
            margin-top: .55rem;
            border: 1px solid #cbd5e1;
            border-radius: .9rem;
            background: #f8fafc;
            padding: .75rem 1rem;
            color: #10213a;
            font-size: .875rem;
            outline: none;
            transition: border-color .15s, background .15s, box-shadow .15s;
        }

        .login-control:hover { border-color: #94a3b8; background: #fff; }
        .login-control:focus {
            border-color: #0ea5e9;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(14,165,233,.14);
        }
    </style>
</head>

<body class="p-4 md:p-8">
    <main class="mx-auto flex min-h-[calc(100vh-2rem)] w-full max-w-6xl flex-col items-stretch justify-center gap-6 lg:flex-row lg:items-center">
        <section class="glass w-full rounded-3xl p-8 text-white lg:flex-1">
            <p
                class="inline-block rounded-full bg-white/20 px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em]">
                Portal Kaprodi Jurusan</p>
            <h1 class="display-font mt-5 text-5xl leading-tight">Kontrol Penuh Data Laporan Tahunan</h1>
            <p class="mt-5 max-w-xl text-sm leading-relaxed text-sky-100">Kelola statistik, grafik, indikator, dan
                agenda
                prodi secara real-time dalam satu panel modern. Semua perubahan langsung tercermin di dashboard publik.
            </p>
            <div class="mt-8 grid gap-3 md:grid-cols-2">
                <div class="rounded-2xl bg-white/10 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-cyan-200">Keamanan</p>
                    <p class="mt-2 text-sm font-semibold">Akses panel hanya untuk pengelola jurusan terotorisasi.</p>
                </div>
                <div class="rounded-2xl bg-white/10 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-amber-200">Efisiensi</p>
                    <p class="mt-2 text-sm font-semibold">Update angka & konten tanpa edit kode manual.</p>
                </div>
            </div>
        </section>

        <section class="login-card w-full rounded-3xl p-6 md:p-8 lg:flex-1">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-sky-700">Portal Access</p>
            <h2 class="display-font mt-3 text-4xl leading-tight text-(--ink)">Masuk ke Panel</h2>
            <p class="mt-2 text-sm text-(--muted)">Pilih Program Studi, lalu masukkan username/email dan password akun Anda.</p>

            @if ($errors->any())
                <div class="mt-5 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.submit') }}" class="mt-7 space-y-4">
                @csrf
                <label class="login-field">Program Studi
                    <select name="prodi_id" required
                        class="login-control"
                        >
                        <option value="">-- Pilih Program Studi --</option>
                        @foreach(\App\Models\Prodi::query()->where('is_active', true)->orderByRaw("code = 'ADMIN' desc")->orderBy('name')->get() as $prodi)
                            <option value="{{ $prodi->id }}" @selected(old('prodi_id') == $prodi->id)>{{ $prodi->code === 'ADMIN' ? 'Administrator' : $prodi->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="login-field">Username atau Email
                    <input name="login" type="text" value="{{ old('login') }}" autocomplete="username"
                        class="login-control"
                        placeholder="Contoh: kaprodi.if atau kaprodi.if@prodi.local" required autofocus>
                </label>
                <label class="login-field">Password
                    <input name="password" type="password" autocomplete="current-password"
                        class="login-control"
                        placeholder="Masukkan password" required>
                </label>
                <button type="submit"
                    class="btn-primary w-full rounded-2xl px-4 py-3.5 text-sm font-bold text-white transition hover:-translate-y-0.5">Masuk Portal</button>
            </form>

            <p class="mt-5 text-center text-xs text-slate-500">Halaman ini bersifat privat. Aktivitas login tercatat
                pada
                sesi browser.</p>
        </section>
    </main>
</body>

</html>
