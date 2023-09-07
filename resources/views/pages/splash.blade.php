@extends('layout.app')
@section('title', "Splash")
@section("body")
<form action="/" method="post">
    @csrf
    <div>
        Code :
        <input type="text" name="code">
    </div>
    @if($errors->has('code'))
        <div class="error" style="color: red;">{{ $errors->first('code') }}</div>
    @endif
    <button>Submit</button>
</form>
@endsection

