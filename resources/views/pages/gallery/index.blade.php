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
                <div class="gl-photo-frame col" data-photo_id="{{$photo->id}}" data-details-url="{{$photo->qr_url}}">
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

<div id="modalConfirmDelete" class="modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Delete Photo</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Confirm delete this photo?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <a id="modalConfirmDeleteConfirm"  class="btn btn-danger" href="#" data-url="{{url("app/delete/")}}/" role="button">Delete</a>
        </div>
      </div>
    </div>
  </div>

@endsection

@section("post_body")

@include('components.logout-modal')

<!-- Button trigger modal -->
{{-- todo if !$tuser->canTakePhotos() disable, grtayscale dan bukan pointer, text berapa perberapa image yang diambil --}}

@if (env('FEATURE_CAPTURE_PRINT', false))

<h2 class="opacity-0 load-hide app-capture-counter">
    {{$photos->count()}}/{{$tuser->max_photos}}
</h2>
<a class="app-btn-capture load-hide opacity-0 {{$tuser->canTakePhotos() ? "" : "filer-grayscale"}}" href="{{$tuser->canTakePhotos() ? route('app.capture') : "#"}}">
    &nbsp;
</a>

@endif

<div id="gl-photo-tool" class="gl-photo-tool gl-photo-tool-hidden px-3 d-block prevent-select">
    <div class="d-block" style="margin: auto; text-align: center; width: 300px;">
        <a id="gl-tool-details" href="#" class="d-inline-block gl-icon-gl-view px-3">
            &nbsp;
            {{-- <img src="{{'assets/images/view-1.svg'}}" height="82px" alt=""> --}}
        </a>

        {{-- Print via gallery ga dulu --}}
        {{-- <a href="#" class="d-inline-block gl-icon-gl-print px-3">
            &nbsp;
        </a> --}}

        @if (env('FEATURE_PHOTOS_DELETE', false))
        <a id="gl-tool-delete" href="#" class="d-inline-block gl-icon-gl-del px-3" data-bs-toggle="modal" data-bs-target="#modalConfirmDelete">
            &nbsp;
            {{-- <img src="{{'assets/images/del-1.svg'}}" height="82px" alt=""> --}}
        </a>
        @endif


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
        photo_selected = parent.data("photo_id");

        $("#modalConfirmDeleteConfirm").attr("href", ("" + $("#modalConfirmDeleteConfirm").data("url") + photo_selected));
        $("#gl-tool-details").attr("href", ("" + parent.data("details-url")));

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

