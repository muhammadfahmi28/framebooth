<div id="video-viewer" class="bg-black bg-opacity-75 h-100 w-100 d-flex flex-column top-0 left-0 position-fixed d-none" style="z-index: 10">
    <div class="flex-grow-0 d-flex flex-row justify-content-between">
        <h2>&nbsp;</h2>
        <a class="video-viewer-bt-close d-block p-4 cursor-pointer"  >
            <i class="fa-solid fa-xmark fa-2x"></i>
        </a>
    </div>
    <div class="flex-grow-1 flex-shrink-1 h-0 position-relative">
        <div id="video-viewer-main" class="h-100 p-4">
            <video controls src="#" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
    </div>
    <div class="flex-grow-0 text-center p-4">
        <a id="video-viewer-download" class="text-white cursor-pointer" style="font-weight: 800; font-size: 2rem" download>
            Download
        </a>
    </div>
</div>
