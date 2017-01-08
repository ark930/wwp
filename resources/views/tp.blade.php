<!DOCTYPE html>
<html lang="zh-Hans">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="index, follow, noarchive">
    <title>{{ urldecode($title) }}</title>
    <meta name="description" content="{{ urldecode($description) }}">
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
                mode="{{ $mode }}" read_min="{{ $read_time }}" publish_date="{{ $created_at }}"></editor>
    </div>
</body>

<script src="{{ elixir('js/app.js') }}"></script>

</html>