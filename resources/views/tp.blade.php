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
        <editor title="{{ $title }}" author="{{ $author }}" publish_date="{{ $updated_at }}" content="{{ $content }}"
                show_edit_button="{{ $show_edit_button }}" is_read_only="{{ $is_read_only }}"
                read_min="{{ $read_time }}"></editor>
    </div>
</body>

{{--<script src="js/vue-min.js"></script>--}}
{{--<script src="js/main-min.js"></script>--}}
<script src="{{ elixir('js/app.js') }}"></script>
{{--<script src="{{ elixir('js/jquery.min.js') }}"></script>--}}
{{--<script src="{{ elixir('js/jquery.selection.min.js') }}"></script>--}}
{{--<script src="{{ elixir('js/autosize.min.js') }}"></script>--}}
{{--<script src="{{ elixir('js/load-image.all.min.js') }}"></script>--}}
{{--<script src="{{ elixir('js/quill.min.js') }}"></script>--}}
{{--<script src="{{ elixir('js/core.min.js') }}"></script>--}}


</html>