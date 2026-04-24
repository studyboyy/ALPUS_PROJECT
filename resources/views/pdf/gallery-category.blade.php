<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Galeri {{ $categoryLabel }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #0f172a;
            font-size: 12px;
            line-height: 1.5;
            margin: 24px;
        }

        h1 {
            margin: 0 0 6px;
            font-size: 24px;
        }

        .muted {
            color: #64748b;
        }

        .grid {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
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
    </style>
</head>

<body>
    <h1>Galeri Kegiatan</h1>
    <p class="muted">Kategori: {{ $categoryLabel }}</p>

    <table class="grid">
        <thead>
            <tr>
                <th>Judul</th>
                <th>Kategori</th>
                <th>Gambar</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($galleryItems as $item)
                <tr>
                    <td>{{ data_get($item, 'title') }}</td>
                    <td>{{ data_get($item, 'category') }}</td>
                    <td>{{ data_get($item, 'image_url') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
