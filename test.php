<?php


$text = "Tzo4OiJzdGRDbGFzcyI6NTp7czoxMjoic2ljX2NvdXJzZWlkIjtzOjE6IjIiO3M6MTA6InNpY19zdGF0dXMiO3M6MToiMCI7czoxNjoic2ljX2NvZGlnb19ncnVwbyI7czowOiIiO3M6MTc6InNpY19jb2RpZ29fb2ZlcnRhIjtzOjA6IiI7czo3OiJzaWNfcm9sIjtzOjE6IjYiO30=";

var_dump(unserialize(base64_decode($text)));

$url = 'https://pokeapi.co/api/v2/pokemon';
$url = "https://auladigital.sence.cl/gestor/API/avance-sic/historialEnvios?idSistema=1350";

$curl = curl_init();

curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
curl_setopt($curl, CURLOPT_HTTPGET, 1);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_HEADER, 0);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($curl, CURLOPT_VERBOSE, 0);
curl_setopt($curl, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Accept: application/json'
));

// execute and return string (this should be an empty string '')
$str = curl_exec($curl);

$code = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);

$response = json_decode(curl_exec($curl), false);

curl_close($curl);

// the value of $str is actually bool(true), not empty string ''
echo "#############################################################\n";
echo "STATUS CODE: ". $code;
echo "\n#############################################################\n";
var_dump($str);
echo "#############################################################\n";
echo "STATUS CODE: ". $code . PHP_EOL;
echo "ALIVE: ";
echo (is_numeric($code) && $code == 200) ? "true" : "false";
echo "\n#############################################################\n";

if(isset($response->error)){
    var_dump($response->error);
}

