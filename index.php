<?php

$_NAME = 'GTA V Developer Tools';
$_SCRIPTS = [
    'static/js/root.js',
];
?>

<?php include('static/php/start.php') ?>

<div class="card d-block m-4 bg-dark text-light">
    <div class="card-body" id="main" style="min-height: 72px;">
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 row-cols-xl-5 pt-4">
            <div class="col mb-4">
                <div class="card bg-dark2">
                    <div class="card-body text-center">
                        <a href="textures" class="btn btn-outline-bm">Textures</a>
                    </div>
                </div>
            </div>
            <div class="col mb-4">
                <div class="card bg-dark2">
                    <div class="card-body text-center">
                        <a href="animations" class="btn btn-outline-bm">Animations</a>
                    </div>
                </div>
            </div>
            <div class="col mb-4">
                <div class="card bg-dark2">
                    <div class="card-body text-center">
                        <a href="patricles" class="btn btn-outline-bm">Patricle Effects</a>
                    </div>
                </div>
            </div>
            <div class="col mb-4">
                <div class="card bg-dark2">
                    <div class="card-body text-center">
                        <a href="sounds" class="btn btn-outline-bm">Sounds</a>
                    </div>
                </div>
            </div>
            <div class="col mb-4">
                <div class="card bg-dark2">
                    <div class="card-body text-center">
                        <a href="speech" class="btn btn-outline-bm">Speeches</a>
                    </div>
                </div>
            </div>
            <div class="col mb-4">
                <div class="card bg-dark2">
                    <div class="card-body text-center">
                        <a href="postfx" class="btn btn-outline-bm">Screen Effects</a>
                    </div>
                </div>
            </div>
            <div class="col mb-4">
                <div class="card bg-dark2">
                    <div class="card-body text-center">
                        <a href="timecycle" class="btn btn-outline-bm">Timecycle Modifiers</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row row-cols-1">
            <div class="col mb-4">
                <div class="card bg-dark2">
                    <div class="card-body d-flex justify-content-center">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="igt_disable" <?php if ($_COOKIE['ingame_disabled'] == '1') echo ' checked'; ?>>
                            <label class="form-check-label" for="igt_disable"><span class="text-warning">Disable</span> <b>In-Game Tools</b> globally:</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col mb-4">
                <div class="card bg-dark2">
                    <div class="card-body d-flex justify-content-center">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="cwq_toggle" <?php if ($_COOKIE['copy_with_quotes'] == '1') echo ' checked'; ?>>
                            <label class="form-check-label" for="cwq_toggle"><span class="text-warning">Include</span> <b>quotes</b> in copied text:</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php include('static/php/end.php') ?>