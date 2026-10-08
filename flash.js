document.addEventListener("DOMContentLoaded", function () {
    var flash = document.getElementById("flashMessages");

    if (!flash) {
        return;
    }

    if (flash.getAttribute("data-success")) {
        alert(flash.getAttribute("data-success"));
    }

    if (flash.getAttribute("data-error")) {
        alert(flash.getAttribute("data-error"));
    }
});
