FAVES_ID = "sounds";

function search() {
    const args = {
        query: $("#search").val().trim(),
        only: $("#only").is(":checked") ? 1 : 0,
        inv: $("#inv").is(":checked") ? 0 : 1,
        loop: $("#loop").is(":checked") ? 1 : 0,
        page: Page,
        faves: $(".btn-faves").is(".on") ? 1 : 0,
        faves_data: $(".btn-faves").is(".on") ? faves_get(false) : "",
    };
    update();
    if (Page == 1) {
        PrevDict = "";
        $("#main").html(``);
    }
    $("#bottom").remove();
    $("#main").append(`
    <div id="loading" class="d-flex justify-content-center">
        <div class="spinner-border text-light" role="status"></div>
    </div>
    `);
    console.log(args);
    $.get("?search", args, function (response) {
        console.log(response);
        if (Page == 1) {
            $("#main").append(`
            <div class="card d-block m-4 bg-dark2 text-white">
                <div class="card-body text-center">
                    <br class="d-block d-md-none">
                    <span class="mx-4"><span class="text-success font-weight-bold">${numberWithSpaces(response.count.dict)}</span> <span class="ms-2">Dictionaries</span></span>
                    <br class="d-block d-md-none">
                    <span class="mx-4"><span class="text-bm font-weight-bold">${numberWithSpaces(response.count.total)}</span> <span class="ms-2">Sounds</span></span>
                </div>
            </div>
            `);
        }
        $("#loading").remove();
        var html = `
        <table class="table table-borderless mx-auto w-100" style="table-layout: fixed;">
        `;
        if (Page == 1) {
            html += `
            <thead class="text-light">
                <th></th>
                <th></th>
                <th>Duration</th>
                <th>Volume</th>
                <th></th>
                <th style="width: 50px;"></th>
            </thead>
            `;
        } else {
            html += `
            <thead style="height: 0;">
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th style="width: 50px;"></th>
            </thead>
            `;
        }
        for (let i = 0; i < response.result.length; i++) {
            const x = response.result[i];
            var border = "";
            if (x.dict != PrevDict) {
                border = ' style="border-top: 1px solid var(--gray);"';
            }
            if (x.dur >= 15000) {
                x.dur = "> 15";
            } else {
                if (x.dur < 10) x.dur = "0";
                else x.dur = x.dur / 1000;
            }
            if (x.vol >= 0.15) {
                x.vol = `<div class="meter on"></div><div class="meter on"></div><div class="meter on"></div>`;
            } else if (x.vol >= 0.1) {
                x.vol = `<div class="meter on"></div><div class="meter on"></div><div class="meter"></div>`;
            } else if (x.vol == 0) {
                x.vol = `<div class="meter"></div><div class="meter"></div><div class="meter"></div>`;
            } else {
                x.vol = `<div class="meter on"></div><div class="meter"></div><div class="meter"></div>`;
            }
            html += `
            <tr>
                <td${border} class="copy ${x.dict == PrevDict ? " hover-opacity text-muted" : "text-success"}">
                    <span class="mb-0">${x.dict.replace(response.meta.query, '<span class="found">$&</span>')}</span>
                </td>
                <td${border} class="copy text-bm">
                    <span class="mb-0">${x.name.replace(response.meta.query, '<span class="found">$&</span>')}</span>
                </td>
                <td${border} class="text-white">
                    <span class="mb-0"><span class="font-weight-bold">${x.dur}</span><small class="text-muted ms-1">SEC</small></span>
                </td>
                <td${border} class="text-white">
                    <span class="mb-0">${x.vol}</span>
                </td>
                <td${border} class="text-white">
                    <div data-player="../static/files/audio/${x.url}"></div>
                    <div class="ingame-trigger">
                        <a href="#" class="btn btn-sm btn-success" data-ingame="audio:${x.dict},${x.name}"><i class="fas fa-play-circle"></i> In-Game</a>    
                    </div>
                </td> 
                <td${border} class="text-white">
                    <div class="fave" data-fave="${x.id}"></div>
                </td>
            <tr>
            `;
            PrevDict = x.dict;
        }
        html += `
        </table>
        `;
        var shown = response.paging.current_page * response.paging.per_page;
        if (shown > response.count.total) {
            shown = response.count.total;
        }
        var bottom = `
        <div class="card bg-dark mt-4" id="bottom">
            <div class="card-body">
                <p>Showing <span class="font-weight-bold">${shown}</span> / ${response.count.total} sounds</p>
                ${
                    Number(response.paging.current_page) < Number(response.paging.total_pages)
                        ? `
                <button type="button" class="btn btn-outline-warning w-100" id="more">Show more</button>
                `
                        : ``
                }
            </div>
        </div>
        `;
        if (response.count.total > 0) {
            $("#main").append(`
            <div class="card d-block mx-4 my-0 bg-dark2 text-light" style="border: none;">
                <div class="card-body text-center">
                    ${html}
                    ${bottom}
                </div>
            </div>
            `);
            faves_load();
            $(".copy").tooltip({
                title: "Copy",
            });
            $("[data-player]").each(function () {
                const file = $(this).attr("data-player");
                $(this).removeAttr("data-player");
                var cap = new CircleAudioPlayer({
                    audio: file,
                    size: 40,
                    borderWidth: 2,
                });
                cap.appendTo($(this)[0]);
            });
        }
    });
}

function moreparams() {
    return {
        i: $("#inv").is(":checked") ? 1 : null,
        l: $("#loop").is(":checked") ? 1 : null,
    };
}
$("#inv").prop("checked", params.i == undefined ? false : params.i == 1);
$("#loop").prop("checked", params.l == undefined ? false : params.l == 1);
$(document).on("click", "#inv", function () {
    Page = 1;
    search();
});
$(document).on("click", "#loop", function () {
    Page = 1;
    search();
});
