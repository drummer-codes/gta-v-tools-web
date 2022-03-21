const IS_MOBILE = /(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0, 4));
const INGAME_DISABLED = Cookies.get(`ingame_disabled`) == "1";
const COPY_WITH_QUOTES = Cookies.get(`copy_with_quotes`) == "1";

String.prototype.replaceQuery = function (query) {
    var s = this;
    query.split("&").forEach((x) => {
        s = s.replace(x, '<span class="found">$&</span>');
    });
    return s;
};
String.prototype.strim = function (needle) {
    var out = this;
    while (0 === out.indexOf(needle)) out = out.substr(needle.length);
    while (out.length === out.lastIndexOf(needle) + needle.length) out = out.slice(0, out.length - needle.length);
    return out;
};

$(document).on("click", 'a[href="#"], a[href="#/"]', function (e) {
    e.preventDefault();
});

function API(method, args, executeOnDone) {
    if (args === undefined) args = {};
    if (executeOnDone === undefined) executeOnDone = null;
    if (typeof loader_show === "function" && showLoader) loader_show();
    var url = "https://" + window.location.hostname + "/gta/api/" + method.strim("/") + "/";
    //console.log(url);
    var url_args = {
        token: Cookies.get(`ingame_token`),
    };
    for (key in args) url_args[key] = args[key];
    var done = function (data) {
        if (executeOnDone != null && typeof executeOnDone === "function") executeOnDone(data);
    };
    $.ajax({
        beforeSend: function (xhr, s) {},
        cache: false,
        complete: function (xhr, s) {},
        crossDomain: true,
        data: url_args,
        dataType: "text",
        error: function (xhr, status, thrown) {
            console.error(`API Error: ${method}`);
            console.log(thrown);
            done(xhr.responseText);
        },
        method: "GET",
        success: function (data, status, xhr) {
            // console.info(`API: ${method}`);
            //console.info(data);
            done(data);
        },
        timeout: 600000,
        url: url,
    });
}

$(document).on("click", "[data-copy], .copy", function () {
    const that = $(this);
    const hasIngame = that.find(".ingame-trigger").length > 0;
    if (that.find(".ingame-trigger.show:hover").length > 0) return;
    const tooltip = $("#" + that.attr("aria-describedby") + " .tooltip-inner");
    var text = $(this).is("[data-copy]") ? $(this).attr("data-copy") : hasIngame ? that.children("span").first().text().trim() : that.text().trim();
    text = text.trim();
    if (COPY_WITH_QUOTES) {
        text = '"' + text + '"';
    }
    copyTextToClipboard(text.trim());
    tooltip.text("Copied");
    that.addClass("copied");
    setTimeout(() => {
        that.removeClass("copied");
        //tooltip.text('Copy');
    }, 1000);
});

function numberWithSpaces(x) {
    if (x === undefined) x = "0";
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
}

function fallbackCopyTextToClipboard(text) {
    var textArea = document.createElement("textarea");
    textArea.value = text;

    // Avoid scrolling to bottom
    textArea.style.top = "0";
    textArea.style.left = "0";
    textArea.style.position = "fixed";

    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();

    try {
        var successful = document.execCommand("copy");
        var msg = successful ? "successful" : "unsuccessful";
        console.log("Fallback: Copying text command was " + msg);
    } catch (err) {
        console.error("Fallback: Oops, unable to copy", err);
    }

    document.body.removeChild(textArea);
}

function copyTextToClipboard(text) {
    if (!navigator.clipboard) {
        fallbackCopyTextToClipboard(text);
        return;
    }
    navigator.clipboard.writeText(text).then(
        function () {},
        function (err) {
            console.error("Async: Could not copy text: ", err);
        }
    );
}

function getParams(parent) {
    if (parent === undefined) parent = false;
    const w = parent ? window.parent : window;
    var r = {};
    new URLSearchParams(new URL(w.location.href).search).forEach(function (v, k, p) {
        r[k] = v;
    });
    return r;
}

function setParams(newParams, reload, parent) {
    if (reload === undefined) reload = false;
    if (parent === undefined) parent = false;
    const w = parent ? window.parent : window;
    var url = new URL(w.location.href);
    var query_string = url.search;
    var params = new URLSearchParams(query_string);
    for (const name in newParams) {
        if (newParams.hasOwnProperty(name)) {
            const value = newParams[name];
            if (value == null || value == undefined || String(value) == "") {
                if (params.has(name)) params.delete(name);
            } else {
                if (!params.has(name)) params.append(name, value);
                else params.set(name, value);
            }
        }
    }
    url.search = params.toString();
    var new_url = url.toString();
    if (reload) w.location.href = new_url;
    else w.history.pushState("", "", new_url);
}

var FAVES_ID = "undefined";
var TO = null;
var Page = 1;
var PrevDict = "";
const params = getParams(false);
$('[data-toggle="tooltip"]').tooltip();
$("#search").val(params.q);
$("#only").prop("checked", params.o == 1);
$(document).on("input", "#search", function () {
    if (TO != null) clearTimeout(TO);
    TO = setTimeout(() => {
        Page = 1;
        search();
    }, 500);
});
$(document).on("change", "#only", function () {
    if ($("#search").val().trim().length < 1) {
        update();
        return;
    }
    Page = 1;
    search();
});
$(document).on("click", "#more", function () {
    Page++;
    search();
});

function update() {
    var more = moreparams();
    var x = {
        q: $("#search").val().trim(),
        o: $("#only").is(":checked") ? 1 : null,
    };
    for (p in more) x[p] = more[p];
    setParams(x, false, false);
}

function moreparams() {
    return {};
}

$(function () {
    search();
});

function faves_load() {
    var data = Cookies.get(`faves_${FAVES_ID}`);
    if (data == undefined) data = "";
    data = data.split(",");
    $(`[data-fave]`).removeClass("on");
    for (let i = 0; i < data.length; i++) {
        $(`[data-fave="${data[i]}"]`).addClass("on");
    }
    return data;
}

function faves_get(array = true) {
    var data = Cookies.get(`faves_${FAVES_ID}`);
    if (data == undefined) data = "";
    if (array) {
        data = data.split(",");
    }
    return data;
}

function faves_set(data, array = true) {
    if (array) {
        data = data.join(",");
    }
    Cookies.set(`faves_${FAVES_ID}`, data, {
        path: "/gta/",
        domain: "." + window.location.hostname,
    });
}

function faves_add(item) {
    var x = faves_get(true);
    if (!x.includes(item)) {
        x.push(item);
        faves_set(x, true);
    }
}

function faves_remove(item) {
    var x = faves_get(true);
    if (x.includes(item)) {
        x.splice(x.indexOf(item), 1);
        faves_set(x, true);
    }
}

function faves_has(item) {
    return faves_get(true).includes(item);
}

$(document).on("click", "[data-fave]", function () {
    const on = $(this).hasClass("on");
    const id = $(this).attr("data-fave");
    if (on) faves_remove(id);
    else faves_add(id);
    $(this).toggleClass("on");
});

$(document).on("click", ".btn-faves", function () {
    $(this).toggleClass("on");
    Page = 1;
    search();
});

function img_fake() {
    $(".img-fake:not([src])").each(function () {
        const img = $(this).parent().css("background-image").replace('url("', "").replace('")', "");
        if (img == "none") return;
        $(this).attr("src", img);
    });
}
