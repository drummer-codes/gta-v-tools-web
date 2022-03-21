<?php

$_NAME = 'GTA V Timecycle Modifiers';
$_SCRIPTS = [
    '../static/js/timecycle.js'
];

if (isset($_GET['search'])) {
    header('Access-Control-Allow-Methods: GET, POST');
    header('Content-type: application/json; charset=utf-8');

    $per_page = 50;

    $query = $_GET['query'];
    $qparts = explode('&', $query);
    $only = intval($_GET['only']);
    $page = intval($_GET['page']);
    $faves = intval($_GET['faves']);
    $faves_data = explode(',', $_GET['faves_data']);
    if ($page < 1) $page = 1;

    $order = "ORDER BY";
    if (strlen($query) < 1) {
        $q = 1;
        $order .= " `name`, `id`";
    } else {
        $xqs = [];
        foreach ($qparts as $x) {
            $xq = "`name` LIKE '%$x%'";
            $xqs[] = "(" . $xq . ")";
            $order .= " `name` NOT LIKE '%$x%', `name`, `id`,";
        }
        $order .= " 1";
        $q = implode(' AND ', $xqs);
    }
    $where = "WHERE ($q)";
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

    $sqlc = "SELECT COUNT(*) AS `total` FROM `gta_tools_timecycle` $where";
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

    $sql = "SELECT * FROM `gta_tools_timecycle` $where $order LIMIT $per_page OFFSET $offset";
    $data = DB::query($sql);

    $response = [
        'meta' => [
            'query' => $query,
        ],
        'count' => [
            'total' => $counts['total'],
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
<div class="modal fade" tabindex="-1" role="dialog" id="modal">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content bg-dark img-view" id="img">
            <img class="img-fake">
        </div>
    </div>
</div>
<style>
    .fave::after {
        top: -0.5rem;
        padding-right: .5rem;
    }
</style>
<?php include('../static/php/end.php') ?>

<?php

function update()
{
    $html = "";
    $html .= "<table class='table table-dark table-striped table-hover text-center' style='margin-top: 3em;'>";
    $html .= "<thead class='thead-dark'><tr><th scope='col'>Name</th></tr></thead>";
    $html .= '<tbody>';
    $json = json_decode(file_get_contents('https://raw.githubusercontent.com/DurtyFree/gta-v-data-dumps/master/timecycleModifiers.json'), true);
    $names = [];
    foreach ($json as $x) {
        if (!in_array($x['Name'], $names)) {
            $x = $x['Name'];
            $names[] = $x;
            DB::query("DELETE FROM `gta_tools_timecycle` WHERE `name`='$x'");
            DB::insert('timecycle', [
                'name' => $x,
                'url' => $x . '_50.jpg',
                'url2' => $x . '_90.jpg',
            ]);
            $html .= "
        <tr>
            <td>$x</td>
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
    $data = DB::query("SELECT * FROM `gta_tools_timecycle` ORDER BY `name`");
    $count = count($data);
    $html = '
        <div class="card d-block m-4 bg-dark text-light">
            <div class="card-body text-center">
                <span class="mx-4"><span class="text-bm font-weight-bold">' . number_format($count, 0, '.', ' ') . '</span> <span class="ms-2"> Effects</span></span>
            </div>
        </div>
        ';
    $html .= "<table class='table table-dark table-striped table-hover text-center' style='margin-top: 3em;'>";
    $html .= "<thead class='thead-dark'><tr><th scope='col'>Name</th><th scope='col'></th><th scope='col'></th><tr></thead>";
    $html .= '<tbody>';
    foreach ($data as $x) {
        $html .= '
        <tr>
            <td>' . $x['name'] . '</td>
            <td><a class="btn btn-sm btn-primary" href="' . $x['url'] . '" target="_blank">View 50%</a></td>
            <td><a class="btn btn-sm btn-primary" href="' . $x['url2'] . '" target="_blank">View 90%</a></td>
        </tr>
        ';
    }
    $html .= '</tbody>';
    $html .= '</table>';
    echo $html;
}
function raw2()
{
    $data = DB::query("SELECT * FROM `gta_tools_timecycle` ORDER BY `name`");
    foreach ($data as $x) {
        echo '"' . $x['name'] . '"' . "\n";
    }
}

?>