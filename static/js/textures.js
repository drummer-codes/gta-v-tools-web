FAVES_ID = "textures";

function search() {
    const args = {
        query: $("#search").val().trim(),
        only: $("#only").is(":checked") ? 1 : 0,
        rpf: $("#rpf").val().join(","),
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
                    <span class="mx-4"><span class="text-info font-weight-bold">${numberWithSpaces(response.count.rpf)}</span> <span class="ms-2">Archives</span></span>
                    <br class="d-block d-md-none">
                    <span class="mx-4"><span class="text-success font-weight-bold">${numberWithSpaces(response.count.dict)}</span> <span class="ms-2">Dictionaries</span></span>
                    <br class="d-block d-md-none">
                    <span class="mx-4"><span class="text-bm font-weight-bold">${numberWithSpaces(response.count.txt)}</span> <span class="ms-2">Textures</span></span>
                </div>
            </div>
            `);
        }
        $("#loading").remove();
        var html = ``;
        for (let i = 0; i < response.result.length; i++) {
            const x = response.result[i];
            var bg = "contain";
            if (Number(x.width) < 150 && Number(x.height) < 150) {
                bg = "auto";
            }
            html += `
            <div class="col mb-4">
                <div class="card bg-dark">
                    <div class="fave" data-fave="${x.id}"></div>
                    <div class="card-img-top" style="background-image: url(../static/files/textures/${x.url}); height: 200px; width: 100%; background-position: center; background-size: ${bg}; background-repeat: no-repeat; cursor: zoom-in;" data-view data-width="1000" data-height="1000">
                        <img class="img-fake">
                    </div>
                    <div class="card-body pt-0">
                        <ul class="list-group list-group-flush bg-dark2 text-light">
                            <li class="list-group-item bg-dark2">
                                <small class="text-muted" style="font-size: 65%; text-transform: uppercase;">Archive</small>
                                <p class="mb-0 text-info">${x.rpf.replaceQuery(response.meta.query)}</p>
                            </li>
                            <li class="list-group-item bg-dark2">
                                <small class="text-muted" style="font-size: 65%; text-transform: uppercase;">Dictionary</small>
                                <p class="mb-0 text-success copy">${x.dict.replaceQuery(response.meta.query)}</p>
                            </li>
                            <li class="list-group-item bg-dark2">
                                <small class="text-muted" style="font-size: 65%; text-transform: uppercase;">Name</small>
                                <p class="mb-0 text-bm copy">${x.name.replaceQuery(response.meta.query)}</p>
                            </li>
                            <li class="list-group-item bg-dark2">
                                <small class="text-muted" style="font-size: 65%; text-transform: uppercase;">Resolution</small>
                                <p class="mb-0 text-white">${x.width}x${x.height}</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            `;
        }
        var shown = response.paging.current_page * response.paging.per_page;
        if (shown > response.count.txt) {
            shown = response.count.txt;
        }
        var bottom = `
        <div class="card bg-dark" id="bottom">
            <div class="card-body">
                <p>Showing <span class="font-weight-bold">${shown}</span> / ${response.count.txt} textures</p>
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
        if (response.count.txt > 0) {
            $("#main").append(`
            <div class="card d-block m-4 bg-dark2 text-white">
                <div class="card-body text-center">
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-5">
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

function moreparams() {
    return {
        r: $("#rpf").val().join(","),
    };
}
$("#rpf").val((params.r == undefined ? "" : params.r).split(","));
$(document).on("change", "#rpf", function () {
    Page = 1;
    search();
});

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
