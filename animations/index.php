<?php

$_NAME = 'GTA V Animations';
$_SCRIPTS = [
    '../static/js/animations.js',
    '../static/js/ingame.js'
];

if (isset($_GET['search'])) {
    header('Access-Control-Allow-Methods: GET, POST');
    header('Content-type: application/json; charset=utf-8');

    $per_page = 100;

    $query = str_replace('_', '[_]', str_replace('%', '[%]', strval($_GET['query'])));
    $only = intval($_GET['only']);
    $peds = intval($_GET['peds']);
    $page = intval($_GET['page']);
    $faves = intval($_GET['faves']);
    $faves_data = explode(',', $_GET['faves_data']);
    if ($page < 1) $page = 1;

    $order = "ORDER BY";
    if (strlen($query) < 1) {
        $q = 1;
        $order .= " `dict`, `name`, `id`";
    } else {
        if (strlen($query) < 3) {
            $q = 1;
            $order .= " `dict`, `name`, `id`";
        } else {
            if (strpos($query, '+') !== false) {
                $sub = [];
                foreach (explode('+', $query) as $part) {
                    $subq = "`name` LIKE '%$part%'";
                    if (!$only) {
                        $subq .= " OR `dict` LIKE '%$part%'";
                    }
                    $subq = "($subq)";
                    $sub[] = $subq;
                }
                $q = implode(' AND ', $sub);
            } else {
                $q = "`name` LIKE '%$query%'";
                if (!$only) {
                    $q .= " OR `dict` LIKE '%$query%'";
                }
            }
            $order .= " `name` LIKE '$query' AND `dict` LIKE 'query', `name` LIKE '$query', `dict` LIKE 'query', `dict`, `name`, `id`";
        }
    }
    $pedsFilter = '1';
    if ($peds) {
        $pedsFilter = "`name` NOT LIKE 'prop[_]%' AND `name` NOT LIKE 'w[_]%' AND `name` NOT LIKE '%camera%' AND `name` NOT LIKE '%player[_]zero%' AND `name` NOT LIKE '%player[_]one%' AND `name` NOT LIKE '%player[_]two%' AND `dict` NOT LIKE '%[_]int-%' AND `dict` NOT LIKE '%[_]ext-%' AND `dict` NOT LIKE '%[_]int[_]%' AND `dict` NOT LIKE '%[_]ext[_]%' AND `name` NOT LIKE '%p[_]cs%' AND `name` NOT LIKE 'p[_]%' AND `name` NOT LIKE 'v[_]%'";
    }
    $where = "WHERE ($q) AND ($pedsFilter)";
    if ($faves) {
        $faves_query = [];
        foreach ($faves_data as $x) {
            if (intval($x) != 0) {
                $faves_query[] = $x;
            }
        }
        if (count($faves_query) > 0) {
            $where .= " AND `id` IN (" . implode(',', $faves_query) . ")";
        }
    }

    $sqlc = "SELECT COUNT(DISTINCT `dict`) AS `dict`, COUNT(*) as `total` FROM `gta_tools_animations` $where";
    $counts = DB::queryFirstRow($sqlc);
    $total_pages = intval(ceil($counts['total'] / $per_page));
    if ($page > $total_pages) {
        $page = $total_pages;
    }
    if ($total_pages < 1) {
        $total_pages = 1;
        $page = 1;
    }
    $offset = $per_page * ($page - 1);

    $sql = "SELECT * FROM `gta_tools_animations` $where $order LIMIT $per_page OFFSET $offset";
    $data = DB::query($sql);

    $response = [
        'meta' => [
            'query' => $query,
            'only' => strval($only),
        ],
        'count' => [
            'dict' => $counts['dict'],
            'total' => strval(intval($counts['total'])),
        ],
        'paging' => [
            'total_pages' => strval($total_pages),
            'current_page' => strval($page),
            'per_page' => strval($per_page),
            'offset' => strval($offset),
        ],
        'result' => $data,
        'sql' => $sql,
    ];

    echo json_encode($response);
    die;
}

?>

<?php include('../static/php/start.php') ?>
<div class="card d-block m-4 bg-dark text-light">
    <div class="card-body">
        <div class="row">
            <div class="col">
                <div class="card d-block m-4 bg-dark2 text-white">
                    <div class="card-body text-center">
                        <div class="input-group d-block d-md-flex">
                            <div class="input-group-prepend">
                                <span class="input-group-text d-flex w-100 justify-content-center py-0">
                                    <a class="btn btn-sm btn-outline-bm btn-faves my-1" data-toggle="tooltip" title="Show only favourites">
                                        <span class="off">☆</span>
                                        <span class="on">★</span>
                                    </a>
                                </span>
                            </div>
                            <input type="text" class="form-control text-white" placeholder="Search" id="search">
                            <div class="input-group-append d-flex flex-column justify-content-center bg-dark ps-1 pe-2 rounded">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="only">
                                    <label class="form-check-label" for="only"><span class="text-warning"></label>
                                    <span>Only animations <a href="#" data-toggle="tooltip" title="If checked, will search in animation names only. Otherwise searching in both dictionaries and animations." style="background: var(--bs-bm); color: #343800 !important; padding: 0 8px; border-radius: 50%;">?</a></span>
                                </div>
                            </div>
                            <div class="input-group-append d-flex flex-column justify-content-center bg-dark ps-1 pe-2 rounded">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="peds">
                                    <label class="form-check-label" for="peds"><span class="text-warning"></label>
                                    <span>Only peds <a href="#" data-toggle="tooltip" title="If checked, will TRY TO filter out prop and camera anims." style="background: var(--bs-bm); color: #343800 !important; padding: 0 8px; border-radius: 50%;">?</a></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card d-block m-4 bg-dark text-light">
    <div class="card-body" id="main" style="min-height: 72px;">
    </div>
</div>
<p class="text-center text-muted">Animations list by <a class="text-light" href="https://github.com/DurtyFree/gta-v-data-dumps/blob/master/animDictsCompact.json" target="_blank">DurtyFree</a></p>
<?php include('../static/php/end.php') ?>

<?php

function update()
{
    $html = "";
    $html .= "<table class='table table-dark table-striped table-hover text-center' style='margin-top: 3em;'>";
    $html .= "<thead class='thead-dark'><tr><th scope='col'>Dictionary</th><th scope='col'>Animation</th></tr></thead>";
    $html .= '<tbody>';
    $json = json_decode(file_get_contents('https://raw.githubusercontent.com/DurtyFree/gta-v-data-dumps/master/animDictsCompact.json'), true);
    $dbcount = DB::query("SELECT `dict`, COUNT(*) AS `count` FROM `gta_tools_animations` GROUP BY `dict`");
    $dbcount = array_combine(array_map(function ($x) {
        return $x['dict'];
    }, $dbcount), array_map(function ($x) {
        return $x['count'];
    }, $dbcount));
    foreach ($json as $x) {
        $dict = $x['DictionaryName'];
        if ($dbcount[$dict] >= count($x['Animations'])) continue;
        foreach ($x['Animations'] as $name) {
            DB::query("DELETE FROM `gta_tools_animations` WHERE `dict`='$dict' AND `name`='$name'");
            DB::insert('anim', [
                'dict' => $dict,
                'name' => $name,
            ]);
            $html .= "
                <tr>
                    <td>$dict</td>
                    <td>$name</td>
                </tr>
                ";
        }
    }
    $html .= '</tbody>';
    $html .= '</table>';
    echo $html;
}
function raw()
{
    $data = DB::query("SELECT * FROM `gta_tools_animations` ORDER BY `dict`, `name`");
    $count = count($data);
    $counts = DB::queryFirstRow("SELECT COUNT(DISTINCT `dict`) AS `dict` FROM `gta_tools_animations`");
    $html = '
        <div class="card d-block m-4 bg-dark text-light">
            <div class="card-body text-center">
                <span class="mx-4"><span class="text-success font-weight-bold">' . number_format($counts['dict'], 0, '.', ' ') . '</span> <span class="ms-2">Dictionaries</span></span>
                <span class="mx-4"><span class="text-bm font-weight-bold">' . number_format($count, 0, '.', ' ') . '</span> <span class="ms-2">Animations</span></span>
            </div>
        </div>
        ';
    $html .= "<table class='table table-dark table-striped table-hover text-center' style='margin-top: 3em;'>";
    $html .= "<thead class='thead-dark'><tr><th scope='col'>Dictionary</th><th scope='col'>Animation</th></tr></thead>";
    $html .= '<tbody>';
    foreach ($data as $x) {
        $html .= '
        <tr>
            <td>' . $x['dict'] . '</td>
            <td>' . $x['name'] . '</td>
        </tr>
        ';
    }
    $html .= '</tbody>';
    $html .= '</table>';
    echo $html;
}

?>