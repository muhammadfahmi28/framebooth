<script>
    function photoViewerOpen(href) {
        console.log('photoViewerOpen(href)', href);
        const css = {
            "background-image": `url('${href}')`,
            "background-size": "contain",
            "background-repeat": "no-repeat",
            "background-position": "center"
        };
        $("#photo-viewer").removeClass("d-none");
        $("#photo-viewer-download").attr("href", href);
        $("#photo-viewer-main-image").css(css);
    }

    $("#photo-viewer .photo-viewer-bt-close").on("click", function (e) {
        $("#photo-viewer").addClass("d-none");
        $(".gl-photo-frame").removeClass("selected");
    });
</script>
