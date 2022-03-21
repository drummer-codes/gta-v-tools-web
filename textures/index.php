<?php

$_NAME = 'GTA V Scripting Textures';
$_SCRIPTS = [
    '../static/js/textures.js'
];

if (isset($_GET['search'])) {
    header('Access-Control-Allow-Methods: GET, POST');
    header('Content-type: application/json; charset=utf-8');

    $per_page = 50;

    $query = $_GET['query'];
    $qparts = explode('&', $query);
    $only = intval($_GET['only']);
    $rpf = explode(',', $_GET['rpf']);
    $page = intval($_GET['page']);
    $faves = intval($_GET['faves']);
    $faves_data = explode(',', $_GET['faves_data']);
    if ($page < 1) $page = 1;
    if ($rpf[0] == '') $rpf = [];

    $order = "ORDER BY";
    if (strlen($query) < 1) {
        $q = 1;
        $order .= " `rpf`!='script_txds', `rpf`, `dict`, `name`, `id`";
    } else {
        $xqs = [];
        foreach ($qparts as $x) {
            $xq = "`name` LIKE '%$x%'";
            if (!$only) $xq .= " OR `dict` LIKE '%$x%'";
            $xqs[] = "(" . $xq . ")";
            $order .= " !(`name` LIKE '%$x%' AND `dict` LIKE '%$x%'), `name` NOT LIKE '%$x%', `dict` NOT LIKE '%$x%', `rpf`, `dict`, `name`, `id`,";
        }
        $order .= " 1";
        $q = implode(' AND ', $xqs);
        /*$q = "`name` LIKE '%$query%'";
        if (!$only) {
            $q .= " OR `dict` LIKE '%$query%'";
        }
        $order .= " `name` NOT LIKE '$query' AND `dict` NOT LIKE '$query', `name` NOT LIKE '$query', `dict` NOT LIKE '$query', `rpf`, `dict`, `name`, `id`";*/
    }
    $where = "WHERE ($q)";
    if (count($rpf) > 0) {
        $r = [];
        foreach ($rpf as $x) $r[] = "`rpf`='$x'";
        $r = join(" OR ", $r);
        $where .= " AND ($r)";
    }
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

    $sqlc = "SELECT COUNT(*) AS `total`, COUNT(DISTINCT `rpf`) AS `rpf`, COUNT(DISTINCT `dict`) AS `dict` FROM `gta_tools_images` $where";
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

    $sql = "SELECT * FROM `gta_tools_images` $where $order LIMIT $per_page OFFSET $offset";
    $data = DB::query($sql);

    $response = [
        'meta' => [
            'query' => $query,
            'only_textures' => strval($only),
            'rpf_archives' => $rpf,
        ],
        'count' => [
            'rpf' => $counts['rpf'],
            'dict' => $counts['dict'],
            'txt' => $counts['total'],
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

<?php include('../static/php/start.php'); ?>


<?php
$rpfs = DB::queryFirstColumn("SELECT DISTINCT `rpf` FROM `gta_tools_images`");
?>
<div class="card d-block m-4 bg-dark text-light">
    <div class="card-body">
        <div class="row">
            <div class="col-12 col-lg-6 col-xl-5">
                <div class="card d-block m-4 bg-dark2 text-white">
                    <div class="card-body text-center">
                        <div class="d-block d-md-none">Archives</div>
                        <div class="input-group justify-content-center">
                            <label class="input-group-text d-none d-md-block">Archives</label>
                            <select class="selectpicker" id="rpf" title="All" multiple>
                                <?php
                                foreach ($rpfs as $x) {
                                    echo '<option value="' . $x . '">' . $x . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6 col-xl-7">
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
                                    <span>Only textures <a href="#" data-toggle="tooltip" title="If checked, will search in texture names only. Otherwise searching in both dictionaries and textures." style="background: var(--bs-bm); color: #343800 !important; padding: 0 8px; border-radius: 50%;">?</a></span>
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

function raw()
{
    $data = DB::query("SELECT * FROM `gta_tools_images` ORDER BY `rpf`, `dict`, `name`");
    $count = count($data);
    $counts = DB::queryFirstRow("SELECT COUNT(DISTINCT `rpf`) AS `rpf`, COUNT(DISTINCT `dict`) AS `dict` FROM `gta_tools_images`");
    $html = '
        <div class="card d-block m-4 bg-dark text-light">
            <div class="card-body text-center">
                <span class="mx-4"><span class="text-info font-weight-bold">' . number_format($counts['rpf'], 0, '.', ' ') . '</span> <span class="ms-2">Archives</span></span>
                <span class="mx-4"><span class="text-success font-weight-bold">' . number_format($counts['dict'], 0, '.', ' ') . '</span> <span class="ms-2">Dictionaries</span></span>
                <span class="mx-4"><span class="text-bm font-weight-bold">' . number_format($count, 0, '.', ' ') . '</span> <span class="ms-2">Textures</span></span>
            </div>
        </div>
        ';
    $html .= "<table class='table table-dark table-striped table-hover text-center' style='margin-top: 3em;'>";
    $html .= "<thead class='thead-dark'><tr><th scope='col'>RPF Archive</th><th scope='col'>Dictionary</th><th scope='col'>Texture</th><th scope='col'>Resolution</th><th scope='col'></th><tr></thead>";
    $html .= '<tbody>';
    foreach ($data as $x) {
        $html .= '
        <tr>
            <td>' . $x['rpf'] . '</td>
            <td>' . $x['dict'] . '</td>
            <td>' . $x['name'] . '</td>
            <td>' . $x['width'] . 'x' . $x['height'] . '</td>
            <td><a class="btn btn-sm btn-primary" href="' . $x['url'] . '" target="_blank">View</a></td>
        </tr>
        ';
    }
    $html .= '</tbody>';
    $html .= '</table>';
    echo $html;
}

?>