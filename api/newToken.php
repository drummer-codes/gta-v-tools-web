<?php

include_once('../static/php/db.php');

$old = $_GET['token'];
if ($old != '') {
    DB::query("DELETE FROM `gta_tools_ingame_tokens` WHERE `token`='$old'");
}

$token = generateRandomString();
while (DB::queryFirstField("SELECT COUNT(*) FROM `gta_tools_ingame_tokens` WHERE `token`='$token' LIMIT 1") > 0) {
    $token = generateRandomString();
}
DB::insert('gta_tools_ingame_tokens', [
    'token' => $token,
]);
echo $token;

function generateRandomString($length = 256)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
