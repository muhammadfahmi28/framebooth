@extends('layout.app')
@section('title', "Gallery")
@section('head')
<meta name="robots" content="noindex, nofollow">
@endsection
@section("body")

<div id="main-container" class="opacity-0" >
    {{-- <div id="header" >
        <div>
            nanti ganti jadi nama dan tombol ganti nama
            <a href="/logout">logout</a>
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#logoutModal">
                Logout
            </button>
        </div>
    </div> --}}

    <div id="header" class="mb-4 py-3" >
        <h1>Photos</h1>
    </div>

    <div id="content">

        @if ($photos)

            <div class="row">
                {{-- @foreach ($photos as $photo)
                <div>
                    <img src="{{asset('storage/'.$folder.'/'.$photo->filename)}}" alt="" width="250px">
                    <a href="{{url("app/print/{$photo->id}")}}">Print</a>
                    <br/>
                    <a href="{{url("app/delete/{$photo->id}")}}">Delete</a>
                    <br/>
                    <a href="{{url("app/view/{$photo->id}")}}">View</a>
                    <br/>
                    {{$photo->created_at}}
                </div>
                @endforeach --}}
                {{-- @for ($x = 0; $x <= 10; $x++) --}}

                @foreach ($photos as $photo)
                <div class="gl-photo-frame col" data-photo_id="{{$photo->id}}" data-details-url="{{url("/g/{$uid}/v/{$photo->id}")}}">
                    <div class="gl-photo" style="transform: rotate({{rand(0,6)-3}}deg)">
                        @if (count($photo->raws) > 0)
                            <img src="{{asset('storage/'.$folder.'/small\/'.$photo->raws[0])}}" alt="">
                        @else
                            <img src="{{asset('storage/'.$folder.'/small\/'.$photo->filename)}}" alt="">
                        @endif
                    </div>
                </div>
                @endforeach
                {{-- @endfor --}}
            </div>
        @else
            {{-- <div>
                <a href="{{route('app.capture')}}">Take Photo Big Button</a>
            </div> --}}
        @endif

        @if (!env("FEATURE_LOGIN", true))
        @include('components.div-socials')
        @endif

    </div>

    <div style="display:block; padding: 25px;">
        &nbsp;
    </div>

</div>

@endsection

@section("post_body")

<div id="gl-photo-tool" class="gl-photo-tool gl-photo-tool-hidden px-3 d-block prevent-select z-n1">
    <div class="d-block" style="margin: auto; text-align: center; width: 300px;">
        <a id="gl-tool-details" href="" class="d-inline-block gl-icon-gl-view px-3">
            &nbsp;
            {{-- <img src="{{'assets/images/view-1.svg'}}" height="82px" alt=""> --}}
        </a>

        {{-- Print via gallery ga dulu --}}
        {{-- <a href="#" class="d-inline-block gl-icon-gl-print px-3">
            &nbsp;
        </a> --}}

    </div>
</div>

<script>

    var photo_selected = null;
    var gl_tool_timeout;

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
        if (gl_tool_timeout) {
            clearTimeout(gl_tool_timeout);
        }
        let parent = $(this).parent();
        $(".gl-photo-frame").removeClass("selected");
        parent.addClass("selected");
        photo_selected = parent.data("photo_id");

        $("#gl-tool-details").attr("href", ("" + parent.data("details-url")));

        $("#gl-photo-tool").removeClass("z-n1");
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
            if (gl_tool_timeout) {
                clearTimeout(gl_tool_timeout);
            }
            gl_tool_timeout = setTimeout(() => {
                $("#gl-photo-tool").addClass("z-n1");
            }, 500);
        }
    });

</script>
@endsection

