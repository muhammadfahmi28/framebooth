@extends('layout.app')
@section('title', "Gallery")
@section("body")

<div id="main-container" class="opacity-0" >
    <div id="header" >
        <div>
            uid : {{ $tuser->uid }} <br/>
            {{-- nanti ganti jadi nama dan tombol ganti nama --}}
            {{-- <a href="/logout">logout</a> --}}
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#logoutModal">
                Logout
            </button>
        </div>
    </div>

    <div id="content">
        <div>
            <h2>Photos</h2>
        </div>

        @if ($photos)

            <div>
                @foreach ($photos as $photo)
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
                @endforeach
            </div>
            @if ($tuser->canTakePhotos())
            <div>
                <a href="{{route('app.capture')}}">Take Photo</a>
            </div>
            @endif
        @else
            <div>
                <a href="{{route('app.capture')}}">Take Photo Big Button</a>
            </div>
        @endif

    </div>

</div>

@endsection

@section("post_body")

@include('components.logout-modal')

<script>

    const pre_image = [
        '/assets/images/btn-logout.svg',
        '/assets/images/btn-logout-hover.svg',
        '/assets/images/btn-logout-active.svg'
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

