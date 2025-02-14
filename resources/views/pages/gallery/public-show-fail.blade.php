@extends('layout.app')
@section('title', "Gallery - Not Found")
@section('head')
<meta name="robots" content="noindex, nofollow">
@endsection
@section("body")

<div id="main-container" class="opacity-0" >

    {{-- <div id="header" class="mb-4 py-3" >
        <h1>{{$title}}</h1>
    </div> --}}

    <div id="content" class="text-center" style="margin-top: 200px">

        <h2> Content Not Ready</h2>
        <h3>
            Sorry content does not yet available. Please come back after a few moments
        </h3>

    </div>

</div>

@endsection

@section("post_body")

<script>

    const pre_image = [
    ];

    function preloadImages(images) {
        pre_image.forEach(image => {
            preloadImage(image);
        });
    }

    async function renderPage () {
        await preloadImages();
        setTimeout(() => {
            showPage();
        }, 800);
    }

    $(function () {
        renderPage();
    });

</script>
@endsection

