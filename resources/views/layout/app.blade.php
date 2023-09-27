<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')PhotoBooth</title>

    <script src="{{asset('assets/js/jquery-3.7.1.min.js')}}" type="text/javascript"></script>

    @yield('head')
</head>
<body>
    @yield('pre_body')
    @yield('body')
    @yield('post_body')
</body>
</html>
