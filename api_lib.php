<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
// namespace block_sic_api;

defined('MOODLE_INTERNAL') || die();

// use mod_sic\core\Domain\Response;

class sic_api{
    private static $HISTORY_ENDPOINT = "https://auladigital.sence.cl/gestor/API/avance-sic/historialEnvios";
    private static $POST_ENDPOINT = "https://auladigital.sence.cl/gestor/API/avance-sic/enviarAvance";

    private static function get_format_query($url, $array){
        $url = $url . "?";
        list($key, $value) = array_shift($array);
        $url .= "{$key}={$value}";
        foreach($array as $key=>$value){
            $url .= "&{$key}={$value}";
        }
        return $url;
    }

    public static function get_response_history($courseid, $date = null, $limit = 0, $offset = 0){
        global $DB;
        $where = "courseid = :courseid";
        $params = array(
            'courseid' => $courseid,
        );
        if(!is_null($date)){
            $params['min'] = $date->from;
            $params['max'] = $date->to;
            $where .= " AND datetime >= :min AND datetime <= :max";
        }
        $response = $DB->get_records_select('sic', $where, $params, 'datetime DESC', '*', $offset, $limit);
        
        return $response;
    }

    private static function init($url, $getdata, $post = false, $postdata = null, $put = false){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($curl, CURLOPT_HTTPGET, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        if(!is_null($getdata)){
            $url = self::get_format_query($url, $getdata);
            // $url = sprintf("%s?%s", $url, http_build_query($getdata));
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        if(!is_null($postdata) && $post){
            // curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            //curl_setopt($curl, CURLOPT_HTTPGET, 0);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type:application/json',
                'Accept:application/json'
            ));
        }
        return $curl;
    }
    public static function check_connection($rut, $token){
        return self::get_history($rut, $token);
    }
    
    public static function get_history($rut, $token, $fecha = null, $codigo = null, $proceso = null, $id = 1350){
        $data = array(
            'rutOtec' => $rut,
            'idSistema' => $id,
            'token' => $token
        );
        if(!is_null($fecha)) $data['fechaDesde'] = $fecha;
        if(!is_null($codigo)) $data['codigo_externo'] = $codigo;
        if(!is_null($proceso)) $data['id_proceso'] = $proceso;
        $curl = self::init(self::$HISTORY_ENDPOINT, $data);
        $response = new response(curl_exec($curl));
        curl_close($curl);
        return $response;
    }

    public static function send_data($json){
        $curl = self::init(self::$POST_ENDPOINT, null, true, json_encode($json));
        $response = new response(curl_exec($curl));
        // if(!$response){
        //     $error = (object) [ 'error' => [ (object) ['mensaje' => curl_error($curl)] ] ];
        //     // $error = [ 'error' => (object) ['mensaje' => curl_error($curl)] ];
        //     $response = json_encode($error);
        // }
        // if(is_null(json_decode($response))){
        //     $response = explode("</div>", $response)[1];
        // }
        curl_close($curl);
        return $response;
    }
}

?>