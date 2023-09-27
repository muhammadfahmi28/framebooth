@extends('layout.app')
@section('title', "Gallery")
@section("body")
<div>
    uid : {{ $tuser->uid }} <br/>
    {{-- nanti ganti jadi nama dan tombol ganti nama --}}
    <a href="/logout">logout</a>
</div>

<div>
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

@endsection

@section("post_body")
@endsection

