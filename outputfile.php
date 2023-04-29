<?php
/*
require_once(__DIR__ . '/../../config.php');

require_once("{$CFG->libdir}/formslib.php");

function desencriptar($valor) {

    global $CFG;

    $clave  = $CFG->dbpass;

    $method = 'aes-256-cbc';

    $iv = base64_decode("C9fBxl1EWtYTL1/M8jfstw==");

    $encrypted_data = base64_decode($valor);

    return openssl_decrypt($valor, $method, $clave, 0, $iv);

}

function get_config($crypted){

    $config = new stdClass();

    $config = unserialize(base64_decode($crypted));

    return $config;

}

function get_course_config($courseid){

    global $CFG, $DB;

    require_once("{$CFG->dirroot}/config.php");

    $config = new stdClass();

    $registros = $DB->get_records('block_instances', ['blockname' => 'sic'], '', '*');

    foreach($registros as $registro){
        if($registro->sic_courseid){
            $config = get_config($registro->configdata);
            break;
        }
    }

    $config->rutOtec = desencriptar($CFG->sence['rutotec']);

    $config->token = desencriptar($CFG->sence['token']);

    return $config;

}*/

$data = base64_decode($_GET['students']);
//$config = get_course_config($id);
//$json = array('config' => $config);
//$data = json_encode($json, JSON_UNESCAPED_UNICODE);

header('Content-Type: application/json');
header('Content-Disposition: attachment; filename=data.json');
header('Expires: 0'); //No caching allowed
header('Cache-Control: must-revalidate');
header('Content-Length: ' . strlen($data));

file_put_contents('php://output', $data);
