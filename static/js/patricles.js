FAVES_ID = 'particles';

function search() {
    const args = {
        query: $('#search').val().trim(),
        only: $('#only').is(':checked') ? 1 : 0,
        page: Page,
        faves: $('.btn-faves').is('.on') ? 1 : 0,
        faves_data: $('.btn-faves').is('.on') ? faves_get(false) : '',
    };
    update();
    if (Page == 1) {
        PrevDict = '';
        $('#main').html(``);
    }
    $('#bottom').remove();
    $('#main').append(`
    <div id="loading" class="d-flex justify-content-center">
        <div class="spinner-border text-light" role="status"></div>
    </div>
    `);
    console.log(args);
    $.get('?search', args, function (response) {
        console.log(response);
        if (Page == 1) {
            $('#main').append(`
            <div class="card d-block m-4 bg-dark2 text-white">
                <div class="card-body text-center">
                    <br class="d-block d-md-none">
                    <span class="mx-4"><span class="text-success font-weight-bold">${numberWithSpaces(response.count.dict)}</span> <span class="ms-2">Dictionaries</span></span>
                    <br class="d-block d-md-none">
                    <span class="mx-4"><span class="text-bm font-weight-bold">${numberWithSpaces(response.count.total)}</span> <span class="ms-2">Effects</span></span>
                </div>
            </div>
            `);
        }
        $('#loading').remove();
        var html = `
        <table class="table table-borderless mx-auto w-100 w-lg-75 w-xl-50" style="table-layout: fixed;">
        `;
        if (Page == 1) {
            html += `
            <thead class="text-light">
                <th></th>
                <th></th>
                <th style="width: 50px;"></th>
            </thead>
            `;
        } else {
            html += `
            <thead style="height: 0;">
                <th></th>
                <th></th>
                <th style="width: 50px;"></th>
            </thead>
            `;
        }
        for (let i = 0; i < response.result.length; i++) {
            const x = response.result[i];
            var border = '';
            if (x.dict != PrevDict) {
                border = ' style="border-top: 1px solid var(--gray);"';
            }
            html += `
            <tr>
                <td${border} class="copy ${x.dict == PrevDict ? ' hover-opacity text-muted' : 'text-success'}">
                    <span class="mb-0">${x.dict.replace(response.meta.query, '<span class="found">$&</span>')}</span>
                </td>
                <td${border} class="copy text-bm">
                    <span class="mb-0">${x.name.replace(response.meta.query, '<span class="found">$&</span>')}</span>
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
                <p>Showing <span class="font-weight-bold">${shown}</span> / ${response.count.total} effects</p>
                ${Number(response.paging.current_page) < Number(response.paging.total_pages) ? `
                <button type="button" class="btn btn-outline-warning w-100" id="more">Show more</button>
                ` : ``}
            </div>
        </div>
        `;
        if (response.count.total > 0) {
            $('#main').append(`
            <div class="card d-block mx-4 my-0 bg-dark2 text-light" style="border: none;">
                <div class="card-body text-center">
                    ${html}
                    ${bottom}
                </div>
            </div>
            `);
            faves_load();
            $('.copy').tooltip({
                title: 'Copy',
            });
        }
    });
}