<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Laporan Tahunan {{ $tahun }}</title>
    <style>
        @page {
            margin: 24px 28px 52px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #111827;
            font-size: 12px;
            line-height: 1.5;
        }

        h1 {
            margin: 0;
            font-size: 24px;
        }

        h2 {
            margin: 24px 0 10px;
            font-size: 17px;
            color: #0f172a;
            border-bottom: 1px solid #dbe5f2;
            padding-bottom: 6px;
        }

        .muted {
            color: #4b5563;
        }

        .cover {
            border: 1px solid #dbe5f2;
            border-radius: 14px;
            padding: 28px;
            background: #f8fbff;
            margin-bottom: 24px;
            page-break-after: always;
        }

        .subtitle {
            margin-top: 8px;
            font-size: 13px;
            color: #475569;
        }

        .cover-kicker {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 999px;
            background: #dbeafe;
            color: #1d4ed8;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .stats {
            width: 100%;
            margin-top: 16px;
        }

        .stats td {
            width: 50%;
            vertical-align: top;
            padding: 6px;
        }

        .box {
            border: 1px solid #dbe5f2;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 10px;
            background: #ffffff;
        }

        .grid {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        .grid th,
        .grid td {
            border: 1px solid #dbe5f2;
            padding: 8px;
            text-align: left;
        }

        .grid th {
            background: #f8fafc;
        }

        .section-copy {
            margin-top: 8px;
            white-space: pre-line;
        }

        .tag {
            display: inline-block;
            margin-top: 8px;
            padding: 4px 8px;
            border-radius: 999px;
            font-size: 10px;
            background: #e0f2fe;
            color: #075985;
        }

        .toc {
            margin-bottom: 24px;
            page-break-after: always;
        }

        .toc-item {
            border-bottom: 1px dashed #cbd5e1;
            padding: 8px 0;
        }

        .toc-number {
            display: inline-block;
            min-width: 22px;
            font-weight: bold;
            color: #2563eb;
        }

        .footer {
            position: fixed;
            bottom: -28px;
            left: 0;
            right: 0;
            font-size: 10px;
            color: #64748b;
            text-align: center;
        }

        .page-number:after {
            content: counter(page);
        }
    </style>
</head>

<body>
    <div class="footer">
        Laporan Tahunan Program Studi {{ $tahun }} | Halaman <span class="page-number"></span>
    </div>

    <section class="cover">
        <div class="cover-kicker">Laporan Tahunan Resmi</div>
        <div style="height: 28px;"></div>
        <h1>Laporan Tahunan Program Studi</h1>
        <div class="subtitle">Tahun Akademik {{ $tahun }}</div>
        <p class="muted">Dokumen ini memuat ringkasan capaian kinerja, indikator utama, program strategis, serta narasi
            tahunan Program Studi secara terstruktur.</p>

        <table class="stats">
            <tr>
                <td>
                    <div class="box">
                        <strong>Total KPI</strong><br>
                        <span class="muted">{{ count($kpi) }} indikator utama tercatat pada laporan ini.</span>
                    </div>
                </td>
                <td>
                    <div class="box">
                        <strong>Total Bagian Laporan</strong><br>
                        <span class="muted">{{ count($sections) }} bagian naratif tersusun untuk tahun
                            {{ $tahun }}.</span>
                    </div>
                </td>
            </tr>
        </table>
    </section>

    <section class="toc">
        <h2>Daftar Isi</h2>
        <div class="toc-item"><span class="toc-number">1.</span> Indikator KPI</div>
        <div class="toc-item"><span class="toc-number">2.</span> Capaian Persentase</div>
        <div class="toc-item"><span class="toc-number">3.</span> Program dan Agenda</div>
        @foreach ($sections as $index => $section)
            <div class="toc-item"><span class="toc-number">{{ $index + 4 }}.</span> {{ $section->title }}</div>
        @endforeach
    </section>

    <h2>Indikator KPI</h2>
    @foreach ($kpi as $item)
        <div class="box">
            <strong>{{ $item['label'] ?? '-' }}</strong><br>
            <span class="muted">Nilai:
                {{ number_format((float) ($item['value'] ?? 0), (int) ($item['decimals'] ?? 0), ',', '.') }}</span>
        </div>
    @endforeach

    <h2>Capaian Persentase</h2>
    <table class="grid">
        <thead>
            <tr>
                <th>Indikator</th>
                <th>Persentase</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($capaian as $item)
                <tr>
                    <td>{{ $item['label'] ?? '-' }}</td>
                    <td>{{ $item['percent'] ?? 0 }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Program dan Agenda</h2>
    <table class="grid">
        <thead>
            <tr>
                <th>Tipe</th>
                <th>Judul</th>
                <th>Deskripsi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($programItems as $item)
                <tr>
                    <td>{{ $item->type }}</td>
                    <td>{{ $item->title }}</td>
                    <td>{{ $item->description }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Bagian Laporan Tahunan</h2>
    @foreach ($sections as $section)
        <div class="box">
            <strong>{{ $section->title }}</strong><br>
            @if ($section->summary)
                <span class="muted">{{ $section->summary }}</span><br>
            @endif
            <span class="tag">Bagian Tahunan</span>
            <div class="section-copy">{{ $section->content }}</div>
        </div>
    @endforeach
</body>

</html>
