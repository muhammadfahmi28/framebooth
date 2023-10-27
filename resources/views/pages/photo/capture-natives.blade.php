@extends('layout.app')


@section('head')
    <style>
        #video {

        border: 1px solid black;

        box-shadow: 2px 2px 3px black;

        width: 320px;

        height: 240px;

        }



        #photo {

        border: 1px solid black;

        box-shadow: 2px 2px 3px black;

        width: 320px;

        height: 240px;

        }



        #canvas {

        display: none;

        }



        .camera {

        width: 340px;

        display: inline-block;

        }



        .output {

        width: 340px;

        display: inline-block;

        vertical-align: top;

        }



        #startbutton {

        display: block;

        position: relative;

        margin-left: auto;

        margin-right: auto;

        bottom: 32px;

        background-color: rgba(0, 150, 0, 0.5);

        border: 1px solid rgba(255, 255, 255, 0.7);

        box-shadow: 0px 0px 1px 2px rgba(0, 0, 0, 0.2);

        font-size: 14px;

        font-family: "Lucida Grande", "Arial", sans-serif;

        color: rgba(255, 255, 255, 1);

        }



        .contentarea {

        font-size: 16px;

        font-family: "Lucida Grande", "Arial", sans-serif;

        width: 760px;

        }
    </style>
@endsection

@section('body')
    <div class="contentarea">
        <h1>MDN - navigator.mediaDevices.getUserMedia(): Still photo capture demo</h1>
        <p>
            This example demonstrates how to set up a media stream using your built-in
            webcam, fetch an image from that stream, and create a PNG using that image.
        </p>
        <div class="camera">
            <video id="video">Video stream not available.</video>
            <button id="startbutton">Take photo</button>
        </div>
        <canvas id="canvas"> </canvas>
        <div class="output">
            <img id="photo" alt="The screen capture will appear in this box." />
        </div>
        <p>
            Visit our article
            <a href="https://developer.mozilla.org/en-US/docs/Web/API/WebRTC_API/Taking_still_photos">
                Taking still photos with WebRTC</a>
            to learn more about the technologies used here.
        </p>
    </div>

    <div>
        <canvas id="canvas-collage" data-background="{{asset('assets/images/frame/1.png')}}"> </canvas>
    </div>

    <div>
        <h2>Test Form CSRF</h2>
        <form id="form-test" action="javascript:void(0);" data-url="{{route("app.testcsrf")}}">
            @csrf
            <input id="text" name="text" type="text">
            <button id="test" type="button">
                Send
            </button>
        </form>

    </div>

@endsection

@section("post_body")
<script>
    $("#test").on("click", function () {
        console.log("SENDING");
        const url = $("#form-test").data("url");
        const token = $('#form-test input[name="_token"]').val();
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
                text: $("#text").val(),
                photo: $("#photo").attr("src")
            },
            success: function (data) {
                console.log("SUCCESS", data)
            },
            always: function (data) {
                console.log("DONE", data)
            }
        });
    });

    (() => {

        // The width and height of the captured photo. We will set the

        // width to the value defined here, but the height will be

        // calculated based on the aspect ratio of the input stream.


        const width = 1080; // We will scale the photo width to this

        let height = 0; // This will be computed based on the input stream


        // |streaming| indicates whether or not we're currently streaming

        // video from the camera. Obviously, we start at false.


        let streaming = false;


        // The various HTML elements we need to configure or control. These

        // will be set by the startup() function.


        let video = null;

        let canvas = null;

        let photo = null;

        let startbutton = null;

        // F
        let canvascollage = null;
        let ratio = (16 / 9);
        const collageArray = [[105, 71, 450.04, 337.53], [555, 531, 450.04, 337.53], [105, 931, 450.04, 337.53]];
        let collageImages = [];
        let collageIndex = 0;

        function showViewLiveResultButton() {

            if (window.self !== window.top) {

                // Ensure that if our document is in a frame, we get the user

                // to first open it in its own tab or window. Otherwise, it

                // won't be able to request permission for camera access.

                document.querySelector(".contentarea").remove();

                const button = document.createElement("button");

                button.textContent = "View live result of the example code above";

                document.body.append(button);

                button.addEventListener("click", () => window.open(location.href));

                return true;

            }

            return false;

        }


        function startup() {

            if (showViewLiveResultButton()) {

                return;

            }

            video = document.getElementById("video");

            canvas = document.getElementById("canvas");

            photo = document.getElementById("photo");

            startbutton = document.getElementById("startbutton");

            // F
            canvascollage = document.getElementById("canvas-collage");

            navigator.mediaDevices
                .getUserMedia({
                    video: true,
                    audio: false
                })

                .then((stream) => {

                    video.srcObject = stream;

                    video.play();

                })

                .catch((err) => {

                    console.error(`An error occurred: ${err}`);

                });


            video.addEventListener(

                "canplay",

                (ev) => {

                    if (!streaming) {

                        height = video.videoHeight / (video.videoWidth / width);


                        // Firefox currently has a bug where the height can't be read from

                        // the video, so we will make assumptions if this happens.


                        if (isNaN(height)) {

                            height = width / (ratio);

                        }


                        video.setAttribute("width", width);

                        video.setAttribute("height", height);

                        canvas.setAttribute("width", width);

                        canvas.setAttribute("height", height);

                        streaming = true;

                        // F
                        canvascollage.setAttribute("width", 1206);
                        canvascollage.setAttribute("height", 1375);
                        redrawBackground();

                    }

                },

                false,

            );


            startbutton.addEventListener(

                "click",

                (ev) => {

                    takepicture();

                    ev.preventDefault();

                },

                false,

            );


            clearphoto();

        }


        // Fill the photo with an indication that none has been

        // captured.


        function clearphoto() {

            const context = canvas.getContext("2d");

            context.fillStyle = "#AAA";

            context.fillRect(0, 0, canvas.width, canvas.height);


            const data = canvas.toDataURL("image/png");

            photo.setAttribute("src", data);

        }

        // Capture a photo by fetching the current contents of the video

        // and drawing it into a canvas, then converting that to a PNG

        // format data URL. By drawing it on an offscreen canvas and then

        // drawing that to the screen, we can change its size and/or apply

        // other changes before drawing it.


        function takepicture() {

            const context = canvas.getContext("2d");

            if (width && height) {

                canvas.width = width;

                canvas.height = height;

                context.drawImage(video, 0, 0, width, height);


                const data = canvas.toDataURL("image/png");

                photo.setAttribute("src", data);

                console.log("takepicture", data);
                takeCollage(data); //F

            } else {

                clearphoto();

            }

        }

        async function takeCollage(dataUrl) {
            collageImages[collageIndex] = dataUrl;
            console.log("takeCollage", collageImages);

            // redrawBackground(
            //     redrawCollageImages
            // );


            // redrawCollageImages();

            await redrawBackground();
            await redrawCollageImages();


            collageIndex++;
            if (collageIndex >= collageArray.length) {
                collageIndex = 0;
            }
        }

        function redrawBackground(onLoad = null) { //F
            console.log("ACTION redrawBackground =======================", onLoad);
            const collageContext = canvascollage.getContext("2d");
            // collageContext.clearRect(0, 0, canvascollage.width, canvascollage.height);
            const img = new Image;
            img.src = canvascollage.getAttribute("data-background");
            img.onload = function() {
                collageContext.drawImage(img, 0, 0);
                if (onLoad) {
                    onLoad();
                }
            };
            // collageContext.drawImage(img,0,0);
        }

        function redrawCollageImages() {
            console.log("ACTION redrawCollageImages =======================");
            let collageContext = canvascollage.getContext("2d");
            console.log("forEach =======================");
            collageImages.forEach((e, i, arr) => {
                if (e) {
                    console.log("forEach", i, collageArray[i]);
                    const img = new Image;
                    img.src = e;
                    img.onload = function() {
                        collageContext.drawImage(img, collageArray[i][0], collageArray[i][1], collageArray[i][2], collageArray[i][3]);
                    };
                }
            });
        }



        // Set up our event listener to run the startup process

        // once loading is complete.

        window.addEventListener("load", startup, false);

    })();
</script>
@endsection
