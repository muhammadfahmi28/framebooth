<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')PhotoBooth @yield('post_title')</title>

    <script src="{{asset('assets/js/jquery-3.7.1.min.js')}}" type="text/javascript"></script>
    <link href="{{asset('assets/vendor/bootstrap-5.3.2-dist/css/bootstrap.min.css')}}" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN">
    <script src="{{asset('assets/vendor/bootstrap-5.3.2-dist/js/bootstrap.bundle.min.js')}}" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"></script>
    <link href="{{asset('assets/vendor/animate.css-4.1.1/animate.min.css')}}" rel="stylesheet">


    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Passion+One:wght@400;700;900&family=Urbanist:ital,wght@0,200;0,400;0,600;0,700;0,900;1,200;1,400;1,600;1,700;1,900&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/efae5deb11.js" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="{{asset('assets/css/app.css')}}">
    @yield('head')
</head>
<body>
    @yield('pre_body')
    @yield('body')

    <script>
        const HOME_URL = "{{url('')}}";
        function preloadImage(url) {
            var img=new Image();
            img.src=url;
        }

        $("body").on("click", "a", function () {
            if ($(this).attr("href") && $(this).attr("href") != "#") {
                hidePage();
            }
        });

        function showPage () {
            $("#main-container").removeClass("opacity-0");
            $(".load-hide").each(function (index, element) {
                $(this).removeClass("opacity-0");
            });
        }

        function hidePage () {
            $("#main-container").addClass("opacity-0");
            $(".load-hide").each(function (index, element) {
                $(this).addClass("opacity-0");
            });
        }

        $("body").on("click", ".modal a.app-modal-link", function () {
            if ($(this).attr("data-href") && $(this).attr("data-href") != "#") {
                hidePage();
                const redirect = setTimeout(() => {
                    window.location.href = $(this).attr("data-href");
                }, 400);
            }
            // console.log("test");
            // if ($(this).attr("data-modal")) {
            //     const modal = new bootstrap.Modal($(this).attr("data-modal"));
            //     console.log(modal);
            // }
        });

    </script>

    @yield('post_body')
</body>
</html>
