<!DOCTYPE html>
<html lang="zh-Hans">
<head>
    <title>{{ urldecode($title) }}</title>
    <meta charset="UTF-8">
    <meta name="robots" content="index, follow, noarchive">
    <meta name="description" content="{{ urldecode($description) }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="/css/article.css">
    {{--<link rel="stylesheet" href="/css/mypannel.css">--}}

    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>

    @if(env('APP_ENV') == 'production')
    <script type='text/javascript'>
        var _vds = _vds || [];
        window._vds = _vds;
        (function(){
            _vds.push(['setAccountId', '92645af211d82034']);
            (function() {
                var vds = document.createElement('script');
                vds.type='text/javascript';
                vds.async = true;
                vds.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'dn-growing.qbox.me/vds.js';
                var s = document.getElementsByTagName('script')[0];
                s.parentNode.insertBefore(vds, s);
            })();
        })();
    </script>
    @endif
</head>
<body>
    {{--<div id="icp"><a href="http://www.miitbeian.gov.cn/" target="blank" class="fontLevel-s3">沪ICP备17003085号-1</a></div>--}}

    <div id="app">
        <editor title="{{ $title }}"
                author="{{ $author }}"
                html_content="{{ $html_content }}"
                text_content="{{ $text_content }}"
                mode="{{ $mode }}"
                read_min="{{ $read_time }}"
                publish_date="{{ $created_at }}">
        </editor>
    </div>
</body>

<script src="{{ elixir('js/app.js') }}"></script>

</html>