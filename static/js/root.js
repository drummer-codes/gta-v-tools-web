$(document).on("input", "#igt_disable", function () {
    Cookies.set(`ingame_disabled`, $(this).is(":checked") ? "1" : "0", {
        path: "/gta/",
        domain: "." + window.location.hostname,
    });
});
$(document).on("input", "#cwq_toggle", function () {
    Cookies.set(`copy_with_quotes`, $(this).is(":checked") ? "1" : "0", {
        path: "/gta/",
        domain: "." + window.location.hostname,
    });
});
