<?php

header('Content-type: text/plain');

$data = json_decode(str_replace('\n', '~n~', file_get_contents("https://raw.githubusercontent.com/alloc8or/gta5-nativedb-data/master/natives.json")), true);
$settings = [
    'comments' => $_GET['comments'] == '1',
    'hashes' => $_GET['hashes'] == '1',
    'combine' => $_GET['combine'] == '1',
    'unused' => $_GET['unused'] == '1',
    'unknown' => $_GET['unknown'] == '1',
    'camelcase' => $_GET['camelcase'] == '1',
    'namespace' => $_GET['namespace'],
    'class' => $_GET['class'],
];
$class = 'Natives';
if ($settings['class'] != '') $class = $settings['class'];
$tc = "\t";
$tn = "\t\t";
$tf = "\t\t\t";
if ($settings['combine']) {
    $tc = "\t";
    $tn = "";
    $tf = "\t\t";
}
if ($settings['namespace'] == '') {
    $tc = "";
    $tn = "\t";
    $tf = "\t\t";
    if ($settings['combine']) {
        $tc = "";
        $tn = "";
        $tf = "\t";
    }
}

$types = [
    "int" => 'int',
    "const char*" => 'string',
    "Any*" => 'dynamic',
    "Hash" => 'uint',
    "float" => 'float',
    "Ped" => 'Ped',
    "BOOL" => 'bool',
    "Any" => 'dynamic',
    "Entity" => 'Entity',
    "Vehicle" => 'Vehicle',
    "float*" => 'out float',
    "int*" => 'out int',
    "Object" => 'Object',
    "Cam" => 'Camera',
    "Player" => 'uint',
    "BOOL*" => 'IntPtr',
    "Vector3*" => 'out Vector3',
    "ScrHandle*" => 'IntPtr',
    "Entity*" => 'IntPtr',
    "Ped*" => 'IntPtr',
    "Vehicle*" => 'IntPtr',
    "Object*" => 'IntPtr',
    "Pickup" => 'uint',
    "ScrHandle" => 'IntPtr',
    "Hash*" => 'out uint',
    "FireId" => 'Fire',
    "Blip" => 'Blip',
    "Blip*" => 'IntPtr',
    "Interior" => 'uint',
    "char*" => 'IntPtr',
    "Vector3" => 'Vector3',
    "void" => 'void',
];
$camel = [
    'TIMERA' => 'TimerA',
    'TIMERA' => 'TimerB',
    'SETTIMERA' => 'SetTimerA',
    'SETTIMERB' => 'SetTimerB',
    'TIMERSTEP' => 'TimerStep',
];
$reffull = [];
$refshort = [];
foreach ($data as $nsname => $namespace) {
    foreach ($namespace as $func) {
        $reffull[] = $nsname . '::' . $func['name'];
        $refshort = $func['name'];
    }
}



$rtypes = [];
$x = false;
$code = "";
$unused = 0;
$unknown = 0;
foreach ($data as $nsname => $namespace) {
    $nscode = "";
    foreach ($namespace as $hash => $func) {
        if (!$settings['unused'] && $func['unused']) {
            $unused++;
            continue;
        }
        if (!$settings['unknown'] && $func['name'] == '_' . $hash) {
            $unknown++;
            continue;
        }
        $before = "";
        $after = "";
        $hash = substr($hash, 1);

        if ($settings['camelcase']) {
            if (array_key_exists($func['name'], $camel)) {
                $func['name'] = $camel[$func['name']];
            } else {
                $func['name'] = toCamelCase($func['name'], '_', true);
            }
        }

        $ret = $types[$func['return_type']];

        $nret = '';
        if ($ret != 'void') $nret = "<$ret>";

        #region RETURN TYPES

        if ($ret == 'Entity') {
            $nret = 'uint';
            $before = "World.GetEntityByHandle<Entity>(";
            $after = ")";
        }
        if ($ret == 'Vehicle') {
            $nret = 'uint';
            $before = "World.GetEntityByHandle<Vehicle>(";
            $after = ")";
        }
        if ($ret == 'Object') {
            $nret = 'uint';
            $before = "World.GetEntityByHandle<Object>(";
            $after = ")";
        }
        if ($ret == 'Camera') {
            $nret = 'uint';
            $before = "World.GetEntityByHandle<Camera>(";
            $after = ")";
        }
        if ($ret == 'Blip') {
            $nret = 'uint';
            $before = "World.GetBlipByHandle(";
            $after = ")";
        }
        if ($ret == 'Fire') {
            $nret = 'uint';
            $before = "World.GetAllFires()[";
            $after = "]";
        }

        #endregion

        $func['comment'] = str_replace("\n", "", $func['comment']);

        $paramnames = [];
        $params = [];
        $nparams = [];
        $pindex = 0;
        foreach ($func['params'] as $param) {
            $pt = $types[$param['type']];
            $pbn = $param['name'];
            if ($pbn == 'base') $pbn = 'p';
            if ($pbn == 'out') $pbn = 'p';
            if ($pbn == 'string') $pbn = 'str';
            if ($pbn == 'override') $pbn = 'ovrrd';
            if ($pbn == 'event') $pbn = 'evnt';
            if ($pbn == 'object') $pbn = 'obj';
            $pn = $pbn;
            $pni = 1;
            while (in_array($pn, $paramnames)) {
                $pni++;
                $pn = $pbn . $pni;
            }
            $paramnames[] = $pn;
            $px = $pn;
            $npre = '';
            if (substr($pt, 0, 3) == 'out') $npre = 'out ';

            #region PARAMS

            if ($pt == 'Entity') $px = "$pn ? $pn.Handle : 0";
            if ($pt == 'Vehicle') $px = "$pn ? $pn.Handle : 0";
            if ($pt == 'Object') $px = "$pn ? $pn.Handle : 0";
            if ($pt == 'Camera') $px = "$pn ? $pn.Handle : 0";
            if ($pt == 'Blip') $px = "$pn ? $pn.Handle : 0";

            #endregion

            $params[] = $pt . " " . $pn;
            $nparams[] = $npre . $px;
            $pindex++;
        }
        $params = implode(', ', $params);
        $nparams = implode(', ', $nparams);

        $summ = '';
        if ($settings['hashes']) {
            $summ .= "{$tf}/// <c>0{$hash}  {$func['jhash']}</c><br/>\n";
        }
        if ($settings['comments']) {
            $fcomment = explode('~n~', $func['comment']);
            for ($i = 0; $i < count($fcomment); $i++) {
                $line = $fcomment[$i];
                if (false) {
                    for ($j = 0; $j < count($reffull); $j++) {
                        if (strpos($line, $reffull[$j]) !== false) {
                            $line = str_replace($reffull[$j], '<see cref="' . $reffull[$j] . '"/>', $line);
                        }
                        if (strpos($line, $refshort[$j]) !== false) {
                            $line = str_replace($refshort[$j], '<see cref="' . $refshort[$j] . '"/>', $line);
                        }
                    }
                }
                $fcomment[$i] = "{$tf}/// " . $fcomment[$i] . " <br/>\n";
            }
            $summ .= implode('', $fcomment);
        }
        if ($summ != '') {
            $summ = "{$tf}/// <summary>\n" . $summ . "{$tf}/// </summary>\n";
        }

        $fcode = $summ . "{$tf}public static {$ret} {$func['name']}({$params}) => {$before}RN.Natives.{$hash}{$nret}({$nparams}){$after};\n";
        $nscode .= $fcode;
    }
    if (!$settings['combine']) {
        if ($settings['camelcase']) {
            $nsname = ucfirst(strtolower($nsname));
        }
        $nscode =
            "{$tn}public static class {$nsname}\n" .
            "{$tn}{\n" .
            $nscode . "\n" .
            "{$tn}}\n";
    }
    $code .= $nscode;
}
if ($settings['namespace'] == '') {
    $code =
        "{$tc}public static class {$class}\n" .
        "{$tc}{\n" .
        $code . "\n" .
        "{$tc}}";
} else {
    $code =
        "namespace {$settings['namespace']}\n" .
        "{\n" .
        "{$tc}public static class {$class}\n" .
        "{$tc}{\n" .
        $code . "\n" .
        "{$tc}}\n" .
        "}";
}
$license = "
/*!
*  GTA V Natives
*  This code was generated.
*  
*  $_WSS/gta/
*
*  (C) 2021 NerdyDev, badm.dev
*
*  MIT License
*/";
echo $license . "\n\n\n\n" .
    "#pragma warning disable IDE1006 // Naming Styles\n\n" .
    "#pragma warning disable IDE0060 // Naming Styles\n\n" .
    "using Rage;\n" .
    "using RN = Rage.Native.NativeFunction;\n" .
    "using IntPtr = System.IntPtr;\n" .
    "\n" .
    "\n" .
    $code . "\n\n" .
    "#pragma warning restore IDE1006 // Naming Styles\n" .
    "#pragma warning restore IDE0060 // Naming Styles";



function toCamelCase($str, $separator = '_', $capitalizeFirstCharacter = false)
{
    $str = strtolower($str);
    $str = str_replace($separator, '', ucwords($str, $separator));
    if (!$capitalizeFirstCharacter) $str = lcfirst($str);
    return $str;
}
