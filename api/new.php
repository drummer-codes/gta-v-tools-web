<?php

include_once('../static/php/db.php');

$token = $_GET['token'];
$value = $_GET['value'];

if (DB::queryFirstField("SELECT COUNT(*) FROM `gta_tools_ingame_tokens` WHERE `token`='$token' LIMIT 1") > 0) {
    DB::query("UPDATE `gta_tools_ingame_tokens` SET `value`='$value', `status`=1 WHERE `token`='$token' LIMIT 1");
    DB::insert('gta_tools_ingame_log', [
        'token' => $token,
        'value' => $value,
    ]);
    echo 'ok';
} else {
    echo 'Token Does Not Exist';
}
