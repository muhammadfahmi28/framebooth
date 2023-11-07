@extends('layout.app')
@section('title', "Gallery - " . $title)
@section("body")

<div id="main-container" class="opacity-0" >

    <div id="header" class="mb-4 py-3" >
        <h2>{{$title}}</h2>
    </div>

    <div id="content">

        @if ($photo_urls)

            <div class="row">

                @foreach ($photo_urls as $key => $photo_url)
                <div class="gl-photo-frame col" data-index="{{$key}}" data-url="{{$photo_url['url']}}">
                    <div class="gl-photo" style="transform: rotate({{rand(0,6)-3}}deg)">
                        <img src="{{$photo_url['small']}}" alt="">
                    </div>
                </div>
                @endforeach

            </div>

        @else
            <div class="text-center">
                No Data
            </div>
        @endif

    </div>

</div>

@endsection

@section("post_body")

<!-- Button trigger modal -->

<div id="gl-photo-tool" class="gl-photo-tool gl-photo-tool-hidden px-3 d-block prevent-select">
    <div class="d-block" style="margin: auto; text-align: center; width: 300px;">
        <a id="gl-tool-view" href="#" target="_blank" class="d-inline-block gl-icon-gl-view px-3">
            &nbsp;
            {{-- <img src="{{'assets/images/view-1.svg'}}" height="82px" alt=""> --}}
        </a>
    </div>
</div>

<script>

    var photo_selected = null;

    const pre_image = [
        '/assets/images/btn-logout.svg',
        '/assets/images/btn-logout-hover.svg',
        '/assets/images/btn-logout-active.svg',
        '/assets/images/capture-1.svg',
        '/assets/images/capture-2.svg',
        '/assets/images/capture-3.svg',
        '/assets/images/view-1.svg',
        '/assets/images/print-1.svg',
        '/assets/images/del-1.svg',
        '/assets/images/view-2.svg',
        '/assets/images/print-2.svg',
        '/assets/images/del-2.svg',
        '/assets/images/view-3.svg',
        '/assets/images/print-3.svg',
        '/assets/images/del-3.svg'
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

    $(".gl-photo").on("click", function () {
        let parent = $(this).parent();
        $(".gl-photo-frame").removeClass("selected");
        parent.addClass("selected");
        photo_selected = parent.data("photo_index");
        full_url = parent.data("url");

        $("#gl-tool-view").attr("href", full_url);

        $("#gl-photo-tool").removeClass("disabled");
        $("#gl-photo-tool").removeClass("gl-photo-tool-hidden");
    });

    $(document).mouseup(function(e)
    {
        var container = $(".gl-photo, .gl-photo-tool");
        if (!container.is(e.target) && container.has(e.target).length === 0)
        {
            photo_selected = null;
            $("#gl-photo-tool").addClass("gl-photo-tool-hidden");
            $(".gl-photo-frame").removeClass("selected");
            $("#gl-photo-tool").addClass("disabled");
        }
    });

</script>
@endsection

