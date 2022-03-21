FAVES_ID = "timecycle";

function search() {
    const args = {
        query: $("#search").val().trim(),
        page: Page,
        faves: $(".btn-faves").is(".on") ? 1 : 0,
        faves_data: $(".btn-faves").is(".on") ? faves_get(false) : "",
    };
    update();
    if (Page == 1) {
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
                    <span class="mx-4"><span class="text-bm font-weight-bold">${numberWithSpaces(response.count.total)}</span> <span class="ms-2"> Effects</span></span>
                </div>
            </div>
            `);
        }
        $("#loading").remove();
        var html = ``;
        for (let i = 0; i < response.result.length; i++) {
            const x = response.result[i];
            var bg = "contain";
            html += `
            <div class="col mb-4">
                <div class="card bg-dark">
                    <div class="fave" data-fave="${x.id}"></div>
                    <div class="card-img-top" style="background-image: url(../static/files/timecycle/${x.url2}); min-height: 300px; width: 100%; background-position: center; background-size: ${bg}; background-repeat: no-repeat; cursor: zoom-in;" data-view data-width="1000" data-height="1000" data-url50="../static/files/timecycle/${x.url}" data-url90="../static/files/timecycle/${x.url2}">
                        <img class="img-fake">
                    </div>
                    <div class="card-body pt-0">
                        <ul class="list-group list-group-flush bg-dark2 text-light">
                        <li class="list-group-item bg-dark2">
                            <small class="text-muted" style="font-size: 65%; text-transform: uppercase;">Name</small>
                            <p class="mb-0 text-bm copy">${x.name.replaceQuery(response.meta.query)}</p>
                        </li>
                        <li class="list-group-item bg-dark2">
                                <p class="mb-0">
                                    <label>Strength</label>
                                    <br>
                                    <input type="range" class="custom-range" min="0" max="2" step="1" value="2" data-range style="max-width: 200px;">
                                    <br>
                                    <span>90%</span>
                                </p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            `;
        }
        var shown = response.paging.current_page * response.paging.per_page;
        if (shown > response.count.total) {
            shown = response.count.total;
        }
        var bottom = `
        <div class="card bg-dark" id="bottom">
            <div class="card-body">
                <p>Showing <span class="font-weight-bold">${shown}</span> / ${response.count.total} textures</p>
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
            <div class="card d-block m-4 bg-dark2 text-white">
                <div class="card-body text-center">
                    <div class="row row-cols-1 row-cols-lg-2">
                        ${html}
                    </div>
                    ${bottom}
                </div>
            </div>
            `);
            faves_load();
            img_fake();
            $(".copy").tooltip({
                title: "Copy",
            });
        }
    });
}

$(document).on("click", "[data-view]", function () {
    const img = $(this).css("background-image");
    $("#img").css("background-image", img);
    $("#img .img-fake").removeAttr("src");
    img_fake();
    $("#modal").modal("show");
});
$(document).on("click", "#img", function () {
    $("#modal").modal("hide");
});
$(document).on("input", "[data-range]", function () {
    const val = $(this).val();
    const el = $(this).closest(".card").find(".card-img-top");
    const span = $(this).parent().find("span");
    if (val == 0) {
        el.css("background-image", `url('../static/files/timecycle/None_50.jpg')`);
        span.text("0%");
    } else if (val == 1) {
        el.css("background-image", `url(${el.attr("data-url50")})`);
        span.text("50%");
    } else {
        el.css("background-image", `url(${el.attr("data-url90")})`);
        span.text("90%");
    }
});
