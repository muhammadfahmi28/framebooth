<script>
    function videoViewerOpen(href) {
        $("#video-viewer").removeClass("d-none");
        $("#video-viewer-main-source").attr("src", href);
        $("#video-viewer-download").attr("href", href);
    }

    $("#video-viewer .video-viewer-bt-close").on("click", function (e) {
        $("#video-viewer").addClass("d-none");
        $(".gl-video-frame").removeClass("selected");
    });
</script>
