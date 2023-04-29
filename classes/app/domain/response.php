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

/**
 * Provides meta-data about the plugin.
 *
 * @package     block_sic
 * @author      {2023} {Andres Cubillos Salazar}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_sic\app\domain;

class response {
    private $id;
    private $message;
    private $errors;
    private $content;
    private $error;

    public function __construct(string $response) {
        // if(!$response){
        //     $error = (object) [ 'error' => [ (object) ['mensaje' => curl_error($curl)] ] ];
        //     // $error = [ 'error' => (object) ['mensaje' => curl_error($curl)] ];
        //     $this->query = json_encode($error);
        // }
        if(is_null(json_decode($response))){
            $data = json_decode(explode("</div>", $response)[1]);
        }else{
            $data = json_decode($response);
        }
        $this->error = false;
        $this->id = $data['id_proceso'];
        $this->message = $data['respuesta_SIC'];
        if(isset($data['datosError'])){
            $this->data = $data['datosError'];
            if(isset($data['datosEnviados'])) $this->content = $data['datosEnviados'];
            $this->error = true;
        }else{
            if(isset($data['envio'])) $this->content = $data['envio'];
            if(isset($data['errores'])) $this->errors = $data['errores'];
        }
    }

    public function is_error()
    {
        return $this->error;
    }

    public function get_errors()
    {
        return $this->errors;
    }
}
