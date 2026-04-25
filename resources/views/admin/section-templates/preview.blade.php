<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Önizleme — {{ $sectionTemplate->name }}</title>

    @if($theme && ! empty($theme->assets_json['css']))
        @foreach($theme->assets_json['css'] as $cssUrl)
            <link rel="stylesheet" href="{{ $cssUrl }}">
        @endforeach
    @else
        {{-- Minimal fallback: Bootstrap 5 --}}
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    @endif

    <style>
        body { margin: 0; }
        .cms-preview-bar {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 9999;
            background: #1e1b4b;
            color: #c7d2fe;
            font-family: ui-monospace, monospace;
            font-size: 11px;
            padding: 4px 12px;
            display: flex;
            gap: 12px;
            align-items: center;
        }
        .cms-preview-bar strong { color: #a5b4fc; }
        body { padding-top: 28px; }
    </style>
</head>
<body>

<div class="cms-preview-bar">
    <strong>CMS Önizleme</strong>
    <span>{{ $sectionTemplate->name }}</span>
    @if($theme)
        <span>· {{ $theme->name }}</span>
    @endif
    <span>· default_content_json ile render</span>
</div>

{!! $rendered !!}

@if($theme && ! empty($theme->assets_json['js']))
    @foreach($theme->assets_json['js'] as $jsUrl)
        <script src="{{ $jsUrl }}"></script>
    @endforeach
@else
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endif

</body>
</html>
