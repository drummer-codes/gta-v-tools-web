<?php
include_once('../static/php/db.php');
$token = $_GET['token'];
$data = DB::queryFirstRow("SELECT * FROM `gta_tools_ingame_tokens` WHERE `token`='$token' LIMIT 1");
$tt = time() - $data['lastonline'];
$online = intval($data['connected'] && $tt < 5);
if (!$online && ($data['status'] != 0 || $data['connected'])) {
    DB::query("UPDATE `gta_tools_ingame_tokens` SET `status`=0,`connected`=0 WHERE `token`='$token' LIMIT 1");
}
if ($online && !$data['connected']) {
    DB::query("UPDATE `gta_tools_ingame_tokens` SET `connected`=1 WHERE `token`='$token' LIMIT 1");
}
echo "{$data['status']}|{$online}|{$data['value']}|$tt";
