@extends('layout.app')


@section('head')
<style>
    #main-container {
        max-width: 1400px;
    }
    .available-photo {
        border: solid 0 white;
        transition: transform 400ms, border-width 400ms
    }
    .available-photo.active {
        border-width: 4px;
        transform: scale(1.075)
    }
</style>
@endsection

@section('body')

<div id="main-container" class="opacity-0" >
    <div id="content">
        <div id="step-1" class="row align-items-center h-100">
            <div class="col-12 col-lg-9 p-5">
                <div class="ratio__f4 d-block rounded overflow-hidden mb-3" style="background-color: rgba(165, 42, 42, 0.548)">
                    <video id="video" class="w-100 h-auto">Video stream not available.</video>
                </div>
                {{-- <video id="video" class="ratio__f4 d-block rounded w-100 mb-3" style=" background-color: rgba(165, 42, 42, 0.548)">Video stream not available.</video> --}}
                <button id="capturebtn" class="d-block m-auto btn btn-success fs-1">
                    <i class="fa-solid fa-camera"></i>
                </button>
            </div>
            <div class="col-12 col-lg-3 p-5">
                <div class="available-photo active ratio__f4 d-block rounded overflow-hidden mb-5" style="background-color: rgba(165, 42, 42, 0.548)">
                    <img id="image1st" src="" alt="" class="w-100">
                </div>

                <div class="available-photo ratio__f4 d-block rounded overflow-hidden mb-5" style="background-color: rgba(165, 42, 42, 0.548)">
                    <img id="image2nd" src="" alt="" class="w-100">
                </div>

                <div class="available-photo ratio__f4 d-block rounded overflow-hidden " style="background-color: rgba(165, 42, 42, 0.548)">
                    <img id="image3rd" src="" alt="" class="w-100">
                </div>
            </div>
            <div class="col-12">
                <button id="capture_done" class="d-block m-auto btn btn-success fs-1" disabled>
                    <i class="fa-solid fa-check" aria-hidden="true"></i> Done
                </button>
            </div>
        </div>
        <div id="step-2" class="row align-items-center h-100" style="display: none;">
            <div class="col-2 fs-1 text-end" onclick="prevCollage();">
                <button class="btn btn-primary">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
            </div>

            <div class="col-8">
                <div id="image-main" class="mx-auto mb-4 hidden text-left" style="width: fit-content; background-color: rgba(165, 42, 42, 0.548)">
                    <img src="" alt="" height="1024px">
                </div>
                <form id="form_main" action="javascript:void(0);" data-url="{{route("app.save_and_print")}}">
                    @csrf
                    <input id="main_photo" type="hidden" name="main_photo">
                    <input id="raw1th" type="hidden" name="raw[]">
                    <input id="raw2nd" type="hidden" name="raw[]">
                    <input id="raw3rd" type="hidden" name="raw[]">
                    <button  type="button" id="edit_done" class="d-block m-auto btn btn-success fs-1" disabled>
                        <i class="fa-solid fa-print"></i> Print
                    </button>
                </form>
            </div>
            <div class="col-2 fs-1 text-start text-start">
                <button class="btn btn-primary"onclick="nextCollage();">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>
        </div>

    </div>
</div>
<canvas id="canvas-collage" style="display: none;"> </canvas>
<canvas id="canvas" style="display: none;"> </canvas>

<div id="modalLoading" class="modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Printing</h5>
            </div>
            <div class="modal-body fs-1 text-center">
                <i class="fa-solid fa-spinner fa-spin-pulse"></i>
            </div>
                <div class="modal-footer">
            </div>
        </div>
    </div>
</div>

<div id="modalSuccess" class="modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Success</h5>
            </div>
            <div class="modal-body text-center">
                You'll be redirected
            </div>
                <div class="modal-footer">
            </div>
        </div>
    </div>
</div>

<div id="modalFailed" class="modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Failed</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body fs-1 text-center">
                <i class="fa-solid fa-spinner fa-spin-pulse"></i>
            </div>
                <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
@endsection

@section("post_body")

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

<script>
    const width = 1920; // We will scale the photo width to this
    let height = null; // This will be computed based on the input stream
    let streaming = false;

    let video = null;
    let canvas = null;
    let photo = null;
    let capturebtn = null; //take Photo

    var modalLoading = null;
    var modalSuccess = null;
    var modalFailed = null;

    // F
    let canvascollage = null;
    let ratio = (33 / 21);
    const defaultPos = [
        [36, 24, 777, 525],
        [36, 597, 777, 525],
        [36, 1170, 777, 525],
    ]; //x, y, w, h
    const collageArray = [
        ["1.png", null],
        ["2.png", null],
        ["3.png", null]
    ];
    var collageIndex = 0;

    var bgImages = [];

    function startup() {
        video = document.getElementById("video");
        canvas = document.getElementById("canvas");
        // photo = document.getElementById("photo");
        // capturebtn = document.getElementById("capturebtn");

        navigator.mediaDevices.getUserMedia({
            video: true,
            audio: false
        }).then((stream) => {
            video.srcObject = stream;
            video.play();
        })
        .catch(() => {
            console.error(`An error occurred: ${err}`);
        });

        video.addEventListener(
            "canplay",
            (ev) => {
                if (height == null) {
                    height = width / (ratio);
                }
                console.log("canplay", height);
                video.setAttribute("width", width);
                video.setAttribute("height", height);
                canvas.setAttribute("width", width);
                canvas.setAttribute("height", height);
                streaming = true;
            },
            false,
        );
    }

    function toggleActive(el, query) {
        $(query).removeClass("active");
        $(el).addClass("active");
    }

    function takePhoto() {
        const context = canvas.getContext("2d");
        console.log("takepicture", width, height);
        if (width && height) { //if camera active
            canvas.width = width;
            canvas.height = height;
            context.drawImage(video, 0, 0, width, height);

            const data = canvas.toDataURL('image/jpeg', 0.95);
            $(".available-photo.active>img").first().attr("src", data);
            console.log("takepicture", data);
        } else {
            // clear
        }
    }

    async function startCollage() {
        canvascollage = document.getElementById("canvas-collage");
        await loadImages();
        refreshCollage();
    }

    function loadImages() {
        return new Promise((resolve, reject) => {
            var loadedImages = 0;
            console.log("collageArray", collageArray);
            collageArray.forEach(el => {
                let bgPath = "/assets/images/frame/" + el[0];
                let bg = new Image;
                bg.src = bgPath;
                bg.onload = function() {
                    canvascollage.width = this.width;
                    canvascollage.height = this.height;
                    loadedImages += 1;
                    bgImages.push(bg);
                    if (loadedImages >= collageArray.length) {
                        resolve();
                    }
                    console.log("bgImages", bg, bgImages);
                };
            });
        })

    }

    function drawBackground() {
        const collageContext = canvascollage.getContext("2d");
        canvascollage.width = bgImages[collageIndex].width;
        canvascollage.height = bgImages[collageIndex].height;
    }


    function drawForeground() {
        const collageContext = canvascollage.getContext("2d");
        collageContext.drawImage(bgImages[collageIndex], 0, 0);
    }

    function drawPhotos() {
        const collageContext = canvascollage.getContext("2d");
        canvascollage = document.getElementById("canvas-collage");
        const image1st = $("#raw1th").val();
        const image2nd = $("#raw2nd").val();
        const image3rd = $("#raw3rd").val();
        var pos = collageArray[collageIndex][1];
        if (pos == null) {
            pos = defaultPos;
        }
        console.log("pos", pos);
        return new Promise((resolve, reject) => {
            var drawCount = 0;

            const I_1 = new Image;
            I_1.src = image1st;
            I_1.onload = function() {
                collageContext.drawImage(I_1, pos[0][0], pos[0][1], pos[0][2], pos[0][3]);
                console.log("0 drawed");
                drawCount += 1;
                if (drawCount >= 3) {
                    resolve();
                }
            };

            const I_2 = new Image;
            I_2.src = image2nd;
            I_2.onload = function() {
                collageContext.drawImage(I_2, pos[1][0], pos[1][1], pos[1][2], pos[1][3]);
                console.log("1 drawed");
                drawCount += 1;
                if (drawCount >= 3) {
                    resolve();
                }
            };

            const I_3 = new Image;
            I_3.src = image3rd;
            I_3.onload = function() {
                collageContext.drawImage(I_3, pos[2][0], pos[2][1], pos[2][2], pos[2][3]);
                console.log("2 drawed");
                drawCount += 1;
                if (drawCount >= 3) {
                    resolve();
                }
            };
        })

    }

    async function refreshCollage() {
        await drawBackground();
        await drawPhotos();
        await drawForeground();
        const collageContext = canvascollage.getContext("2d");
        const data = canvascollage.toDataURL('image/jpeg', 0.95);
        $("#image-main>img").attr("src", data);
        $("#main_photo").val(data);
        $("#image-main").removeClass("hidden");
        $("#edit_done").removeClass("disabled").removeAttr("disabled");
    }

    function nextCollage() {
        collageIndex += 1;
        if (collageIndex >= bgImages.length) {
            collageIndex = 0;
        }
        refreshCollage();
    }
    function prevCollage() {
        collageIndex -= 1;
        if (collageIndex < 0) {
            collageIndex = bgImages.length - 1;
        }
        refreshCollage();
    }

    $(function () {
        startup();
        modalLoading = new bootstrap.Modal('#modalLoading', {backdrop: "static", keyboard: false, focus: true});
        modalSuccess = new bootstrap.Modal('#modalSuccess', {backdrop: "static", keyboard: false, focus: true});
        modalFailed = new bootstrap.Modal('#modalFailed', {focus: true});
    });

    $(".available-photo").on("click", function () {
        toggleActive(this, ".available-photo");
    });

    $("#capturebtn").on("click", function () {
        takePhoto();
        let cur = $(".available-photo.active").first();
        console.log(cur.next());
        console.log(cur.next().length);
        if (cur.next().length > 0) {
            toggleActive(cur.next(), ".available-photo");
        } else {
            toggleActive($(".available-photo").first(), ".available-photo");
        }
        const image1st = $("#image1st").attr("src");
        const image2nd = $("#image2nd").attr("src");
        const image3rd = $("#image3rd").attr("src");
        if (image1st && image2nd && image3rd) {
            $("#capture_done").removeAttr("disabled");
        }
    });

    $("#capture_done").click(function (e) {
        e.preventDefault();
        const image1st = $("#image1st").attr("src");
        const image2nd = $("#image2nd").attr("src");
        const image3rd = $("#image3rd").attr("src");
        if (image1st && image2nd && image3rd) {
            $("#raw1th").val(image1st);
            $("#raw2nd").val(image2nd);
            $("#raw3rd").val(image3rd);

            $("#step-1").addClass("animate__animated animate__fadeOutUp");
            setTimeout(() => {
                $("#step-1").hide();
                $("#step-2").show();
                $("#step-2").addClass("animate__animated animate__fadeInUp");
            }, 1000);
            startCollage();
        }

    });

    $("#edit_done").on("click", function () {
        modalLoading.show();
        $(this).attr('disabled');
        console.log("SENDING");
        const url = $("#form_main").data("url");
        const token = $('#form_main input[name="_token"]').val();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': token
            }
        });
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: token,
                main_photo: $("#main_photo").val(),
                raw: [
                    $("#raw1th").val(),
                    $("#raw2nd").val(),
                    $("#raw3rd").val(),
                ]
            },
            success: function (data) {
                modalLoading.hide();
                modalSuccess.show();
                setTimeout(() => {
                    window.location.replace(HOME_URL);
                }, 2000);
            },
            always: function (data) {
                modalLoading.hide();
                modalFailed.show();
                $("#edit_done").removeAttr("disabled");
            }
        });
    });

</script>
@endsection
