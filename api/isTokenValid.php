<?php

include_once('../static/php/db.php');

$token = $_GET['token'];

if (DB::queryFirstField("SELECT COUNT(*) FROM `gta_tools_ingame_tokens` WHERE `token`='$token' LIMIT 1") > 0) {
    echo '1';
} else {
    echo '0';
}
