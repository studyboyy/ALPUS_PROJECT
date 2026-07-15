<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Dokumen {{ $categoryLabel }}</title>
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

        .box {
            border: 1px solid #dbe5f2;
            border-radius: 10px;
            padding: 12px;
            margin-top: 10px;
        }

        .tag {
            display: inline-block;
            margin-top: 8px;
            padding: 4px 8px;
            border-radius: 999px;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 10px;
        }
    </style>
</head>

<body>
    <h1>Dokumen Pendukung</h1>
    <p class="muted">Kategori: {{ $categoryLabel }}</p>

    @foreach ($documents as $document)
        <div class="box">
            <strong>{{ $document->title }}</strong><br>
            <span class="muted">{{ $document->description }}</span><br>
            <span class="tag">{{ $document->category }}</span>
            <div style="margin-top: 8px;">Dokumen tercatat dalam sistem.</div>
        </div>
    @endforeach
</body>

</html>
