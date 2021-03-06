<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <title>Telegraph</title>
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="MobileOptimized" content="176">
    <meta name="HandheldFriendly" content="True">
    <meta name="robots" content="index, follow">
    <meta property="og:site_name" content="Telegraph">
    <meta property="og:title" content="Telegraph">
    <meta property="og:description"
          content="Telegra.ph is a minimalist publishing tool that allows you to create richly formatted posts and push them to the Web in just a click. Telegraph posts also get beautiful Instant View pages on Telegram.">
    <meta property="og:image" content="http://telegra.ph/images/logo.png">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel="icon" type="image/png" href="/images/favicon.png" sizes="16x16">
    <link rel="icon" type="image/png" href="/images/favicon_2x.png" sizes="32x32">
    <link href="/css/quill.core.min.css" rel="stylesheet">
    <link href="/css/core.min.css" rel="stylesheet">
</head>
<body>
<div class="tl_page_wrap">
    <div class="tl_page">
        <main class="tl_article tl_article_editable tl_article_edit">
            <header class="tl_article_header">
                <h1></h1>
                <address>
                    <a rel="author"></a>

                    <time datetime=""></time>
                </address>
            </header>
            <article id="_tl_editor" class="tl_article_content ql-container">
                <div class="ql-editor" contenteditable="true"><h1 data-placeholder="Title" data-label="Title"
                                                                  class="empty"><br></h1>
                    <address data-placeholder="Your name" data-label="Author" class="empty"><br></address>
                    <p data-placeholder="Your story..." class="empty"><br></p></div>
                <div class="ql-clipboard" contenteditable="true" tabindex="-1"></div>
                <div id="_tl_link_tooltip" class="tl_link_tooltip"></div>
                <div id="_tl_tooltip" class="tl_tooltip">
                    <div class="buttons">
                        <span class="button_hover"></span>
                        <span class="button_group">
                         <button id="_bold_button"></button>
                         <button id="_italic_button"></button>
                         <button id="_link_button"></button>
                        </span>
                        <span class="button_group">
                         <button id="_header_button"></button>
                         <button id="_subheader_button"></button>
                         <button id="_quote_button"></button>
                       </span>
                    </div>
                    <div class="prompt">
                        <span class="close"></span>
                        <div class="prompt_input_wrap"><input type="url" class="prompt_input"></div>
                    </div>
                </div>
                <div id="_tl_blocks" class="tl_blocks" style="top: 90px;">
                    <div class="buttons">
                        <button id="_image_button"></button>

                        <button id="_embed_button"></button>
                    </div>
                </div>
            </article>


            <aside class="tl_article_buttons">
                <div class="account account_top"></div>
                <button id="_edit_button" class="button edit_button">Edit</button>

                <button id="_publish_button" class="button publish_button">Publish</button>
                <div class="account account_bottom"></div>
                <div id="_error_msg" class="error_msg"></div>
            </aside>
        </main>
    </div>
</div>
<div id="_tl_alerts" class="tl_alerts"></div>
<script>var T = {"apiUrl": "{{ \Illuminate\Support\Facades\URL::to('/') }}", "datetime": 0, "pageId": 0};
    (function () {
        var a = document.querySelector('time');
        if (a && T.datetime)try {
            var b = (new Date(1E3 * T.datetime)).toLocaleDateString(undefined, {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
            b && (a.innerText = b)
        } catch (c) {
        }
    })();</script>
<script src="/js/jquery.min.js"></script>
<script src="/js/jquery.selection.min.js"></script>
<script src="/js/autosize.min.js"></script>
<script src="/js/load-image.all.min.js"></script>
<script src="/js/quill.min.js"></script>
<script src="/js/core.min.js"></script>

</body>