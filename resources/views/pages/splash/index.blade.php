@extends('layout.app')
@section('title', "Splash")

@section("head")
<script src="{{asset('assets/js/html5-qrcode.min.js')}}" type="text/javascript"></script>
@endsection

@section("body")

<div id="main-container" class="opacity-0" >
    {{-- <div id="header" >
        header
    </div> --}}
    <div id="content" class="d-flex flex-column justify-content-center">
        <div class="splash-content text-center" style="width: 100%;">
            {{-- <img src="{{asset('assets/images/logo.png')}}" alt="logo" class="m-5" height="400px"> --}}
            <div style="margin: 72px 0">
                <div style="font-size: 96px; line-height: 80px; font-family: 'Cinzel'; font-weight: 500;">
                    Delfta & Odi
                </div>
                <div style="font-size: 28px; font-family: 'Cinzel'; font-weight: 400;">
                    15 Juni 2024
                </div>
            </div>

            <div style="height: 400px; width:400px; margin: auto;">
                <div id="reader" class="mb-3"></div>
                <form id="form_main" action="/submit_code" method="post">
                    @csrf
                    <input autocomplete="off" class="d-inline-block" id="input_code" type="text" name="code">
                    <button type="submit" class="btn button-primary">submit</button>

                    @if($errors->has('code'))
                        <div class="error" style="color: red;">{{ $errors->first('code') }}</div>
                    @endif
                </form>
            </div>
        </div>
    </div>
    {{-- <div id="footer" >
        footer
    </div> --}}
</div>
@endsection

@section('post_body')
<script>
    $(function () {
        showPage();
    });

    var submitting = false;
    function onScanSuccess(decodedText, decodedResult) {
    // handle the scanned code as you like, for example:
    // console.log(`Code matched = ${decodedText}`, decodedResult);
        if (!submitting) {
            $("#main-container").addClass("opacity-0");
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

