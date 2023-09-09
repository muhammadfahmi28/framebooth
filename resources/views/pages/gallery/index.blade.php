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
                {{$photo->filename}} <br/>
                {{$photo->created_at}}
            </div>
            @endforeach
        </div>
    @else
        <div>
            <a href=""></a>
        </div>
    @endif

</div>

@endsection
