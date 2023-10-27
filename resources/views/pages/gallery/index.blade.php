@extends('layout.app')
@section('title', "Gallery")
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
                <div class="gl-photo-frame col" data-photo_id="{{$photo->id}}">
                    <div class="gl-photo" style="transform: rotate({{rand(0,6)-3}}deg)">
                        <img src="{{asset('storage/'.$folder.'/'.$photo->filename)}}" alt="" width="250px">
                    </div>
                </div>
                @endforeach
                {{-- @endfor --}}
            </div>
            @if ($tuser->canTakePhotos())
            {{-- <div>
                <a href="{{route('app.capture')}}">Take Photo</a>
            </div> --}}
            @endif
        @else
            {{-- <div>
                <a href="{{route('app.capture')}}">Take Photo Big Button</a>
            </div> --}}
        @endif

    </div>

</div>

@endsection

@section("post_body")

@include('components.logout-modal')

<!-- Button trigger modal -->
{{-- todo if !$tuser->canTakePhotos() disable, grtayscale dan bukan pointer, text berapa perberapa image yang diambil --}}
<a class="app-btn-capture load-hide opacity-0" href="{{route('app.capture')}}">
    &nbsp;
</a>

<div id="gl-photo-tool" class="gl-photo-tool gl-photo-tool-hidden px-3 d-block prevent-select">
    <div class="d-block" style="margin: auto; text-align: center; width: 300px;">
        <a href="#" class="d-inline-block gl-icon-gl-view px-3">
            &nbsp;
            {{-- <img src="{{'assets/images/view-1.svg'}}" height="82px" alt=""> --}}
        </a>

        <a href="#" class="d-inline-block gl-icon-gl-print px-3">
            &nbsp;
            {{-- <img src="{{'assets/images/print-1.svg'}}" height="82px" alt=""> --}}
        </a>

        <a href="#" class="d-inline-block gl-icon-gl-del px-3">
            &nbsp;
            {{-- <img src="{{'assets/images/del-1.svg'}}" height="82px" alt=""> --}}
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
        'assets/images/view-1.svg',
        'assets/images/print-1.svg',
        'assets/images/del-1.svg',
        'assets/images/view-2.svg',
        'assets/images/print-2.svg',
        'assets/images/del-2.svg',
        'assets/images/view-3.svg',
        'assets/images/print-3.svg',
        'assets/images/del-3.svg'
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

    $(".gl-photo-frame").on("click", function () {
        $(".gl-photo-frame").removeClass("selected");
        $(this).addClass("selected");
        photo_selected = $(this).data("photo_id");
        $("#gl-photo-tool").removeClass("disabled");
        $("#gl-photo-tool").removeClass("gl-photo-tool-hidden");
    });

    $(document).mouseup(function(e)
    {
        var container = $(".gl-photo-frame, .gl-photo-tool");
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

