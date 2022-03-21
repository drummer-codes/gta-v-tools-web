var panel_open = false;
var panel_width = 0;
var ingame_prev_val = "";

$(document).on("click", "#ingame_token_new", function () {
    const that = $(this);
    that.addClass("disabled");
    getNewToken();
    setTimeout(() => {
        that.removeClass("disabled");
    }, 5000);
});
$(document).on("click", "#ingame_token_copy_doc", function () {
    $("#ingame_token_copy").trigger("click");
});
$(document).on("click", "[data-ingame]", function () {
    const that = $(this);
    var value = $(this).attr("data-ingame");
    if (value.startsWith("anim")) {
        value += ",X";
        $("[data-anim-flag]:checked").each(function () {
            value += `+${$(this).attr("data-anim-flag")}`;
        });
    }
    $("[data-ingame]").addClass("disabled");
    if (value != "clear") {
        $('[data-ingame="clear"]').removeClass("disabled");
    }
    API(
        "new",
        {
            value: value,
        },
        function (data) {
            api_update();
        }
    );
});

$(document).on("click", ".ingame-toggle", function () {
    if (panel_open) {
        close_panel();
    } else {
        open_panel();
    }
});

function get_token(secret) {
    var token = Cookies.get(`ingame_token`);
    if (token == undefined) token = "";
    if (secret == true) {
        token = "****" + token.substr(token.length - 5);
    }
    return token;
}

function getNewToken(onDone) {
    API("newToken", {}, function (data) {
        Cookies.set(`ingame_token`, data, {
            path: "/gta/",
            domain: "." + window.location.hostname,
        });
        $("#ingame_token").text(get_token(true));
        $("#ingame_token_copy").attr("data-copy", get_token());
        if (onDone != undefined) {
            onDone();
        }
    });
}

function open_panel() {
    panel_open = true;
    $(".ingame-toggle i").removeClass("fa-chevron-left").addClass("fa-chevron-right");
    $(".ingame").css("right", "10px");
}

function close_panel() {
    panel_open = false;
    $(".ingame-toggle i").removeClass("fa-chevron-right").addClass("fa-chevron-left");
    $(".ingame").css("right", -panel_width - 5 + "px");
}

const statuses = {
    0: "Ready",
    1: "Sending",
    2: "Running",
};

function update_panel(x) {
    panel_width = $(".ingame").width();
    $("#ingame_status").text(statuses[x.status]);
    if (x.connected) {
        if (x.status == 0) {
            $("[data-ingame]").removeClass("disabled");
        } else {
            if (x.status == 2 && ingame_prev_val == "clear") {
                $("#ingame_status").text("Stopping");
            }
            $("[data-ingame]").addClass("disabled");
            if (x.status == 2) {
                $('[data-ingame="clear"]').removeClass("disabled");
            }
        }
        $("#ingame_connect").removeClass("text-warning").addClass("text-success");
        $("#ingame_connect span").text("Connected");
        $(".ingame-trigger").addClass("show");
    } else {
        if (x.status == 0) {
            $("#ingame_status").text("Not Connected");
            $('[data-ingame="clear"]').addClass("disabled");
        }
        $("#ingame_connect").removeClass("text-success").addClass("text-warning");
        $("#ingame_connect span").text("Not Connected");
        $(".ingame-trigger").removeClass("show");
    }
    if (x.value != ingame_prev_val) {
        ingame_prev_val = x.value;
    }
}

function api_update() {
    API("web", {}, function (data) {
        data = data.split("|");
        update_panel({
            status: data[0],
            connected: data[1] == 1,
            value: data[2],
        });
    });
}

$(function () {
    if (IS_MOBILE || INGAME_DISABLED) return;
    if (window.location.hash == "#ingame_guide") {
        $("#ingame_guide").modal("show");
        window.location.hash = "";
    }
    var add = ``;
    if (FAVES_ID == "animations") {
        const flags = ["Loop", "DisableRootMotion", "Idle", "SecondaryTask", "StayInEndFrame", "RagdollOnCollision", "UpperBodyOnly"];
        var flags_html = "";
        for (let i = 0; i < flags.length; i++) {
            const x = flags[i];
            flags_html += `
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" data-anim-flag="${x}" id="anim_flag_${i}">
                <label class="custom-control-label" for="anim_flag_${i}">${x}</label>
            </div>
            `;
        }
        add = `
        <div class="row">
            <div class="col ingame-title">Anim Flags</div>
            <div class="col">
                <p class="mb-2 pe-2" style="white-space: nowrap; text-align: right;">
                    ${flags_html}
                </p>
            </div>
        </div>
        `;
    }
    $("body").append(`
    <div class="card ingame">
        <a href="#" class="btn btn-lg ingame-toggle text-light"><i class="fas fa-chevron-right"></i></a>
        <div class="card-body py-3">
            <h5 class="text-center">In-Game Tools</h5>
            <div class="row">
                <div class="col ingame-title">Token</div>
                <div class="col">
                    <p class="mb-2" style="white-space: nowrap;">
                        <span id="ingame_token">${get_token(true)}</span>
                        <a href="#" class="btn btn-sm btn-success" id="ingame_token_copy" data-copy="${get_token()}"><i class="fas fa-copy"></i></a>
                        <a href="#" class="btn btn-sm btn-warning" id="ingame_token_new"><i class="fas fa-sync-alt"></i></a>
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col ingame-title">Connection</div>
                <div class="col">
                    <p class="mb-2 pe-2" style="white-space: nowrap; text-align: right;">
                        <span class="text-warning" id="ingame_connect"><i class="fas fa-dot-circle"></i> <span>Not Connected</span></span>
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col ingame-title">Status</div>
                <div class="col">
                    <p class="mb-2 pe-2" style="white-space: nowrap; text-align: right;">
                        <span class="text-bm" id="ingame_status">Not Connected</span>
                    </p>
                </div>
            </div>
            ${add}
            <a href="#" class="btn btn-sm btn-info w-100 disabled" data-ingame="clear"><i class="fas fa-exclamation-triangle me-2"></i> Stop all tasks</a> 
            <p class="mb-0 mt-2 text-center text-bm">
                <small><a class="text-bm" href="#ingame_guide" data-toggle="modal" data-target="#ingame_guide">How To Use</a></small>
            </p>
        </div>
    </div>
    `);
    panel_width = $(".ingame").width();
    API(
        "isTokenValid",
        {
            token: get_token(),
        },
        function (data) {
            if (data == 1) {
                open_panel();
            } else {
                getNewToken(function () {
                    open_panel();
                });
            }
        }
    );
    setInterval(() => {
        api_update();
    }, 500);
});
