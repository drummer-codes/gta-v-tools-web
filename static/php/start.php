<?php
$_MAINTENANCE = false;
$_UPDATE = isset($_GET['Gk3vbDNdew4uqd4Y']);
$_RAW = isset($_GET['raw']);
$_RAW_2 = isset($_GET['raw2']);
$_TITLE = $_NAME;
if (isset($_GET['q'])) {
    //$_TITLE = $_GET['q'] . ": " . $_TITLE;
}

if ($_MAINTENANCE) {
    die('Maintenance');
}

if (!$_RAW_2) {
?>

    <!doctype html>
    <html lang="en">

    <head>
        <title><?php echo $_TITLE; ?></title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="shortcut icon" href="/gta/static/img/favicon.png" type="image/x-icon">
        <link rel="stylesheet" href="/static/css/bs5.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/css/bootstrap-select.min.css" />
        <link rel="stylesheet" href="https://kit-free.fontawesome.com/releases/latest/css/free.min.css">
        <link rel="stylesheet" href="/gta/static/css/common.css?<?php echo time(); ?>">
        <?php foreach ($_STYLES as $x) echo '<link rel="stylesheet" href="' . $x . '?' . time() . '">'; ?>
    </head>

    <body>
        <nav class="navbar sticky-top navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <p href="/gta" class="navbar-brand mb-0">
                    <a class="text-light me-4" href="/gta">All Tools</a>
                    <a class="text-white" href="?"><?php echo $_NAME; ?></a>
                </p>
            </div>
        </nav>

    <?php
}

if ($_UPDATE) {
    update();
    die;
}
if ($_RAW) {
    raw();
    die;
}
if ($_RAW_2) {
    header('Content-type: text/plain');
    raw2();
    die;
}

    ?>