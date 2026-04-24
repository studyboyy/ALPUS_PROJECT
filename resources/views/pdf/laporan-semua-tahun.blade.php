<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Laporan Tahunan Semua Tahun</title>
    <style>
        @page {
            margin: 24px 28px 52px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #111827;
            font-size: 11px;
            line-height: 1.45;
        }

        h1 {
            margin: 0;
            font-size: 22px;
        }

        h2 {
            margin: 18px 0 8px;
            font-size: 16px;
            border-bottom: 1px solid #dbe5f2;
            padding-bottom: 4px;
        }

        .muted {
            color: #4b5563;
        }

        .cover {
            border: 1px solid #dbe5f2;
            border-radius: 12px;
            padding: 24px;
            background: #f8fbff;
            margin-bottom: 20px;
            page-break-after: always;
        }

        .year-block {
            page-break-after: always;
        }

        .year-block:last-child {
            page-break-after: auto;
        }

        .box {
            border: 1px solid #dbe5f2;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 8px;
            background: #fff;
        }

        .grid {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        .grid th,
        .grid td {
            border: 1px solid #dbe5f2;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }

        .grid th {
            background: #f8fafc;
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
        Laporan Tahunan Semua Tahun | Halaman <span class="page-number"></span>
    </div>

    <section class="cover">
        <h1>Laporan Tahunan Semua Tahun</h1>
        <p class="muted">Dokumen gabungan seluruh tahun laporan untuk kebutuhan pengujian akhir.</p>
        <p><strong>Total Tahun:</strong> {{ count($reportBundles) }}</p>
    </section>

    @foreach ($reportBundles as $bundle)
        <section class="year-block">
            <h1>Laporan Tahunan {{ $bundle['year'] }}</h1>

            <h2>Indikator KPI</h2>
            @foreach ($bundle['kpi'] as $item)
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
                    @foreach ($bundle['capaian'] as $item)
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
                    @foreach ($bundle['programItems'] as $item)
                        <tr>
                            <td>{{ $item->type }}</td>
                            <td>{{ $item->title }}</td>
                            <td>{{ $item->description }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <h2>Bagian Laporan</h2>
            @foreach ($bundle['sections'] as $section)
                <div class="box">
                    <strong>{{ $section->title }}</strong><br>
                    @if ($section->summary)
                        <span class="muted">{{ $section->summary }}</span><br>
                    @endif
                    <div>{{ $section->content }}</div>
                </div>
            @endforeach
        </section>
    @endforeach
</body>

</html>
