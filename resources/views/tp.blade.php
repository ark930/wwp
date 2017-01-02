<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
    <title>A-Z.press</title>
    <link rel="stylesheet" href="/css/article.css">

    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body>
    <div id="app">
        <editor title="{{ $title }}" author="{{ $author }}" publish_date="{{ $updated_at }}" content="{{ $content }}" show_edit_button="{{ $show_edit_button }}"></editor>
    </div>
</body>

{{--<script src="js/vue-min.js"></script>--}}
{{--<script src="js/main-min.js"></script>--}}
<script src="{{ elixir('js/app.js') }}"></script>


</html>