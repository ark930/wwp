<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <table>
        <thead>
            <th>标题</th>
            <th>作者</th>
            <th>正文</th>
            <th>阅读时间</th>
            <th>创建时间</th>
        </thead>
            @foreach($articles as $article)
                <tbody>
                    <td>{{ $article['title'] }}</td>
                    <td>{{ $article['author'] }}</td>
                    <td>{{ $article['text_content'] }}</td>
                    <td>{{ $article['read_time'] }}</td>
                    <td>{{ $article['created_at'] }}</td>
                </tbody>
        @endforeach

    </table>
</body>
</html>