<!DOCTYPE html>
<html lang="zh-Hans">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="index, follow, noarchive">
    <title>{{ $title }}</title>
    <meta name="description" content="开箱即写，发布到任何地方">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="/css/article.css">

    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body>
    <div id="app">
        <editor title="{{ $title }}" author="{{ $author }}" content="{{ $content }}"
                mode="{{ $mode }}" read_min="{{ $read_time }}" publish_date="{{ $updated_at }}"></editor>
    </div>
</body>

<script src="{{ elixir('js/app.js') }}"></script>

</html>