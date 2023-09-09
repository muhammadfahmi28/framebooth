@extends('layout.app')
@section('title', "Splash")

@section("head")
<script src="{{asset('assets/js/html5-qrcode.min.js')}}" type="text/javascript"></script>
@endsection

@section("body")

<div style="height: 420px; width: 420px;">
    <div id="reader" width="420px"></div>
</div>

<form id="form_main" action="/submit_code" method="post">
    Scan The Code
    @csrf
    <input id="input_code" type="hidden" name="code">
    {{-- <div>
        Code :
        <input type="text" name="code">
    </div> --}}

    {{-- <button>Submit</button> --}}

    @if($errors->has('code'))
        <div class="error" style="color: red;">{{ $errors->first('code') }}</div>
    @endif
</form>

<script>
    var submitting = false;
    function onScanSuccess(decodedText, decodedResult) {
    // handle the scanned code as you like, for example:
    // console.log(`Code matched = ${decodedText}`, decodedResult);
        if (!submitting) {
            submitting = true;
            $("#input_code").val(decodedText);
            $("#form_main").submit();
        }
    }

    function onScanFailure(error) {
    // handle scan failure, usually better to ignore and keep scanning.
    }

    let html5QrcodeScanner = new Html5QrcodeScanner(
    "reader",
    { fps: 10, qrbox: {width: 250, height: 250} },
    /* verbose= */ false);
    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
</script>

@endsection

