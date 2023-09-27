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
        <h2>Photo</h2>
    </div>
    <div>
        <div>
            <img src="{{$photo->getAssetPath()}}" alt="" width="250px">
            <a href="{{url("app/print/{$photo->id}")}}">Print</a>
            <br/>
            <a href="{{url("app/delete/{$photo->id}")}}">Delete</a>
            <br/>
            {{$photo->created_at}}
        </div>
    </div>
</div>

@endsection

@section("post_body")
@endsection

