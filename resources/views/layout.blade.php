<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>搜索测试页</title>
    <link rel="stylesheet" href="{{ elixir('css/app.css') }}" rel="stylesheet">
    <script src="{{ elixir('js/app.js') }}"></script>
</head>
<body>
@yield('content')

</body>
</html>