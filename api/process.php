<?php

include_once('../static/php/db.php');

$token = $_GET['token'];
$time = time();
DB::query("UPDATE `gta_tools_ingame_tokens` SET `lastonline`=$time WHERE `token`='$token' LIMIT 1");
$data = DB::queryFirstRow("SELECT * FROM `gta_tools_ingame_tokens` WHERE `token`='$token' LIMIT 1");
echo "{$data['status']}|{$data['connected']}|{$data['value']}";
