<?php

include_once('../static/php/db.php');

$token = $_GET['token'];
$time = time();

if (strlen($token) == 256) {
    if (DB::queryFirstField("SELECT COUNT(*) FROM `gta_tools_ingame_tokens` WHERE `token`='$token' LIMIT 1") > 0) {
        DB::query("UPDATE `gta_tools_ingame_tokens` SET `connected`=0, `lastonline`=$time WHERE `token`='$token' LIMIT 1");
        echo 'ok';
    } else {
        echo 'Token Does Not Exist';
    }
} else {
    echo 'Token must be 256 characters long';
}
