@extends('layout.app')
@section('title', "Gallery - " . $title)
@section('head')
<meta name="robots" content="noindex, nofollow">
@endsection
@section("body")

<div id="main-container" class="opacity-0" >

    <div id="header" class="mb-4 py-3" >
        <div class="d-flex flex-row justify-content-between">
            <h2>{{$title}}</h2>
            <a href="{{url("/g/{$uid}")}}" class="d-block p-4" >
                <i class="fa-solid fa-xmark fa-2x"></i>
            </a>
        </div>
    </div>

    <div id="content">

        @if ($photo_urls)

            <div class="row">

                @foreach ($photo_urls as $key => $photo_url)
                <div class="gl-photo-frame col" data-index="{{$key}}" data-url="{{$photo_url['url']}}" data-type="{{($photo_url['type'] ?? 'other')}}">
                    <div class="gl-photo cursor-pointer" style="transform: rotate({{rand(0,6)-3}}deg)"
                        @if (!env('FEATURE_CAPTURE_PRINT', false))
                            data-direct="true"
                        @endif
                    >
                        <img src="{{$photo_url['small']}}" alt="">
                        @if ($photo_url['type'] === 'mp4')
                            <div class="gl-photo-text-overlay">
                                MP4
                            </div>
                        @endif
                        @if ($photo_url['type'] === 'gif')
                            <div class="gl-photo-text-overlay">
                                GIF
                            </div>
                        @endif
                    </div>
                </div>
                @endforeach

            </div>

        @else
            <div class="text-center">
                No Data
            </div>
        @endif

        @if (!env("FEATURE_LOGIN", true))
        @include('components.div-socials')
        @endif
    </div>

    <div style="display:block; padding: 25px;">
        &nbsp;
    </div>

</div>

@include('pages.gallery.components.photo_viewer_base')
@include('pages.gallery.components.video_viewer_base')
@include('pages.gallery.components.tutorial_buttom_base', ["className" => "tutorial-pick-photo", "tutorial" => "Pilih salah satu foto"])

@endsection

@section("post_body")

<!-- Button trigger modal -->

<div id="gl-photo-tool" class="gl-photo-tool gl-photo-tool-hidden px-3 d-block prevent-select z-n1">
    <div class="d-block" style="margin: auto; text-align: center; width: 300px;">
        <a id="gl-tool-view" href="#" target="_blank" class="d-inline-block gl-icon-gl-view px-3">
            &nbsp;
            {{-- <img src="{{'assets/images/view-1.svg'}}" height="82px" alt=""> --}}
        </a>
        <a id="gl-tool-download" href="#" class="d-inline-block gl-icon-gl-download px-3" download>
            &nbsp;
            {{-- <img src="{{'assets/images/download-1.svg'}}" height="82px" alt=""> --}}
        </a>
    </div>
</div>

<script>
    var gl_tool_timeout;
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
        '/assets/images/del-3.svg',
        '/assets/images/download-1.svg',
        '/assets/images/download-2.svg',
        '/assets/images/download-3.svg',
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

    $(".gl-photo").on("click", function (e) {
        $(".tutorial-pick-photo").addClass("opacity-0");
        let parent = $(this).parent();
        photo_selected = parent.data("photo_index");
        full_url = parent.data("url");

        if ($(e.target).data('direct')) {
            let type = $(e.target).data('type') ?? 'other';
            $(".gl-photo-frame").removeClass("selected");
            parent.addClass("selected");
            if (type === 'mp4') {
                photoViewerOpen(full_url);
            } else {
                videoViewerOpen(full_url);
            }
            return;
        }

        if (gl_tool_timeout) {
            clearTimeout(gl_tool_timeout);
        }
        $(".gl-photo-frame").removeClass("selected");
        parent.addClass("selected");

        $("#gl-tool-view").attr("href", full_url);
        $("#gl-tool-download").attr("href", full_url);

        $("#gl-photo-tool").removeClass("disabled");
        $("#gl-photo-tool").removeClass("z-n1");
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
            if (gl_tool_timeout) {
                clearTimeout(gl_tool_timeout);
            }
            gl_tool_timeout = setTimeout(() => {
                $("#gl-photo-tool").addClass("z-n1");
            }, 500);
        }
    });

</script>

@include('pages.gallery.components.photo_viewer_script')
@include('pages.gallery.components.video_viewer_script')


@endsection

