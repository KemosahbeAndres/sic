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

 * Task for send information of dedication and completion to API SIC

 *

 * @package   block_sic

 * @author    Andres Cubillos <andrestj1996@gmail.com>

 * @copyright Andres Cubillos 2021

 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

 */



namespace block_sic\task;



require_once("{$CFG->dirroot}/blocks/sic/block_sic_lib.php");



use block_sic_manager;

use block_sic_utils;

use FileRepository;

use \core\events;

use sic_api;

use stdClass;



defined('MOODLE_INTERNAL') || die();



/**

 * Task for send information of dedication and completion to API SIC

 *

 * @package   block_sic

 * @author    Andres Cubillos <andrestj1996@gmail.com>

 * @copyright Andres Cubillos 2021

 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

 */

class send_to_sic_api extends \core\task\scheduled_task {



    /**

     * Name for this task.

     *

     * @return string

     */

    public function get_name() {

        return get_string('apitask', 'block_sic');

    }



    // private static function get_queued_course(&$errors = null){

    //     global $DB;

    //     $queue = array();

    //     $registros = $DB->get_records('block_instances', ['blockname' => 'sic'], '', '*');

    //     // var_dump($registros);

    //     // echo '<br>';

    //     foreach($registros as $registro){

    //         $config = send_to_sic_api::get_course_config($registro);

    //         // var_dump($config);

    //         if($config->sic_status == '1'){

    //             $obj = new stdClass();

    //             $obj->rutOtec = $config->rutOtec;

    //             $obj->idSistema = 1350;

    //             $obj->token = $config->token;

    //             $obj->codigoOferta = $config->sic_codigo_oferta;

    //             $obj->codigoGrupo = $config->sic_codigo_grupo;

    //             $obj->listaAlumnos = array();

    //             $queue[intval($config->sic_courseid)] = $obj;

    //             $errors[intval($config->sic_courseid)] = array();

    //         }

    //     }

    //     return $queue;

    // }

    private static function get_course_config($registro){

        global $CFG, $DB;

        require_once("{$CFG->dirroot}/config.php");

        $config = new stdClass();

        $config = block_sic_utils::get_config($registro->configdata);

        $config->rutOtec = block_sic_utils::desencriptar($CFG->sence['rutotec']);

        $config->token = block_sic_utils::desencriptar($CFG->sence['token']);

        return $config;

    }



    /**

     * Send info

     */

    public function execute() {

        global $CFG, $DB;



        $errores = array();

        $queue = array();

        $tosend = array();



        $registros = $DB->get_records('block_instances', ['blockname' => 'sic'], '', '*');

        // var_dump($registros);

        // echo '<br>';

        foreach($registros as $registro){

            $config = send_to_sic_api::get_course_config($registro);

            // var_dump($config);

            if($config->sic_status == '1'){

                $tosend[] = intval($config->sic_courseid);

            }else{

                $tosave[] = intval($config->sic_courseid);

            }

            $obj = new stdClass();

            $obj->rutOtec = $config->rutOtec;

            $obj->idSistema = 1350;

            $obj->token = $config->token;

            $obj->codigoOferta = $config->sic_codigo_oferta;

            $obj->codigoGrupo = $config->sic_codigo_grupo;

            $obj->listaAlumnos = array();

            $queue[intval($config->sic_courseid)] = $obj;

            $errors[intval($config->sic_courseid)] = array();

        }



        if(count($queue) > 0){

            foreach($queue as $id=>$course){

                $info = (object) $DB->get_record('course', ['id' => $id], 'id, startdate, enddate');

                // echo 'info: ';

                // var_dump($info);

                $manager = new block_sic_manager($info, $info->startdate, time());

                $completion = $manager->get_course_completion();

                // echo 'Completion: ';

                // var_dump($completion);

                $inicio = 0;

                $fin = 0;

                foreach($completion as $index=>$alumno){

                    // echo ' |||  Alumno ||| ';

                    // var_dump($alumno);

                    // echo ' |||  Alumno ||| ';

                    $user = new stdClass();

                    $dedication = $manager->get_user_dedication($alumno->userid, true);

                    $infoAlumno = block_sic_utils::get_user_rut($alumno->userid);

                    if(!isset($infoAlumno->dv)){

                        $errores[$id][] = "El rut alumno ID '{$alumno->userid}' no esta en el formato correcto => RUT: {$infoAlumno->rut}";

                        continue;

                    }

                    $user->rutAlumno = $infoAlumno->rut;

                    $user->dv = $infoAlumno->dv;

                    $connect = 0;

                    $user->tiempoConectividad = 0;

                    // $user->tiempoConectividad = $dedication[0] + block_sic_utils::zoom_dedication($alumno->course);

                    $user->estado = 1;

                    $avance = block_sic_utils::get_formated_completion($alumno->course);

                    $user->porcentajeAvance = round(($avance->completed / $avance->total) * 100, 2);



                    $user->fechaInicio = block_sic_utils::get_fdate($info->startdate);

                    if($info->enddate == 0){

                        $info->enddate = time()+(60*60*24*60);

                    }

                    $user->fechaFin = block_sic_utils::get_fdate($info->enddate);

                    $user->fechaEjecucion = block_sic_utils::get_fdate(time());

                    $user->listaModulos = array();

                    $modulos = block_sic_utils::get_modules_from_course($id);

                    // echo '|| MODULOS ||';

                    // var_dump($modulos);

                    // echo '|| MODULOS ||';

                    foreach($modulos as $mod){

                        // echo '|| module ||';

                        // var_dump($mod);

                        // echo '|| module ||';

                        $modulo = new stdClass();

                        $modulo->codigoModulo = $mod->code;

                        $avance = array();

                        $conectividad = 0;

                        $access = array();

                        $actividades = array();

                        foreach(explode(',', $mod->sequence) as $secid){

                            echo '|| Sequence ID: '.$secid.' ||';

                            foreach($alumno->course as $section){

                                echo '|| Section ID: '.$section->id.' ||';

                                if($section->id == $secid){

                                    //$zoom = block_sic_utils::zoom_dedication($alumno->course, false, $section->id);
                                    $zoom = 0;

                                    foreach($dedication[1] as $key=>$value){

                                        if($key == $section->id){

                                            $conectividad += ($zoom + $value);

                                            break;

                                        }

                                    }

                                    $avance[] = block_sic_utils::get_formated_completion($alumno->course, $section->id);

                                    foreach($dedication[2] as $i=>$connection){

                                        if($i == $section->id){

                                            $access[] = array('first' => $connection->first, 'last' => $connection->last);

                                            break;

                                        }

                                    }

                                    // Actividades

                                    foreach($section->activitys as $activity){

                                        $actividades[] = $activity->name;

                                    }

                                }

                            }

                        }

                        // tiempo de conexion

                        $modulo->tiempoConectividad = $conectividad;

                        $connect += $conectividad;

                        // llenar avance

                        $count = 0;

                        $completed = 0;

                        foreach($avance as $response){

                            $count += $response->total;

                            $completed += $response->completed;

                        }

                        $modulo->porcentajeAvance = round(($completed / $count) * 100, 2);

                        // Llenar fechas

                        $primero = array_shift($access);

                        $first = $primero['first'];

                        $last = $primero['last'];

                        foreach($access as $par){

                            if($par['first'] < $first || $first == 0){

                                $first = $par['first'];

                            }

                            if(($par['last'] > $last && $par['last'] > $first) || ($last == 0 && $par['last'] > 0)){

                                $last = $par['last'];

                            }

                        }

                        if($first != 0){

                            $inicio = $first;

                        }

                        if($last != 0){

                            $fin = $last;

                        }

                        if($first == $last){

                            $first -= (60*60*24);

                        }

                        $modulo->fechaInicio = block_sic_utils::get_fdate($first);

                        $modulo->fechaFin = block_sic_utils::get_fdate($last);

                        $modulo->listaActividades = array();

                        // Llenar actividades

                        foreach($actividades as $actividad){

                            $act = new stdClass();

                            $act->codigoActividad = $actividad;

                            $modulo->listaActividades[] = $act;

                        }

                        $user->listaModulos[] = $modulo;

                    }

                    $queue[$id]->listaAlumnos[] = $user;

                    $user->tiempoConectividad += $connect;

                }

            }





            echo '[COURSE INFO] OK...';





            foreach($queue as $courseid=>$course){

                // $ready = array_filter($tosend, function($value, $key) use ($courseid){

                //     return $value == $courseid;

                // }, ARRAY_FILTER_USE_BOTH);

                //require_once("{$CFG->dirroot}/blocks/sic/api_lib.php");

                //$response = sic_api::check_connection($course->rut, $course->token);



                if(!in_array($courseid, $tosend)){

                    echo '|||| CHECK CONNECTION ||||';

                    //var_dump($response);

                    echo '|||| CHECK CONNECTION ||||';



                    echo '|||| COURSEID -> '.$courseid.' ||||';

                    $json = json_encode($course, JSON_UNESCAPED_UNICODE);


                    var_dump($course);

                    echo "\n\n" . $json . "\n\n";


                    echo '|||| COURSEID -> '.$courseid.' ||||';



                }else{

                    $json = json_encode($course, JSON_UNESCAPED_UNICODE);

                    //$response = $this->send($course->rutOtec, $course->token, $course);



                    echo '|||| FINAL TRACE RESPONSE ||||';

                    // list($header, $body) = explode("\r\n\r\n", $response, 2);

                    // echo $header;

                    echo ' |||| BODY ||||';

                    //var_dump($response);



                    echo ' |||| END BODY ||||';

                    echo '|||| FINAL TRACE RESPONSE ||||';
                    /***
                    if($response->is_error()){

                        //ES ERROR!!!!

                        foreach($response->get_errors() as $error){

                            echo 'ERROR: '.$error->mensaje.' |';

                            $errores[$courseid][] = $error->mensaje;

                        }

                    }
                     */

                }



                echo '[COURSE '.$courseid.'] OK...';

                // NO HAY ERRORES TODO CONTINUA BIEN

                //$errores['response'] = $response;



                $errors = json_encode($errores, JSON_UNESCAPED_UNICODE);

                $data = [

                    'courseid' => $courseid,

                    'data' => base64_encode($json),

                    'datetime' => time(),

                    'status' => 0,

                    'errors' => $errors

                ];

                $returnid = $DB->insert_record('sic', $data);

                $req = (object)[

                    'date' => $data['datetime'],

                    'request' => $course,

                    'response' => "response"

                ];

                // 'response' => $response->toJson()

                var_dump($req);

                // $path = $CFG->dirroot . "/block/sic/data/";

                // require_once($CFG->dirroot.'/FileRepository.php');

                // $repo = new FileRepository($path);

                // $repo->save_data($req);

                echo $returnid;

                // echo '|||||||||||| REGISTRO GUARDADO ||||||||||||';

                // echo ' | ID '.$returnid.' | COURSEID '.$courseid.' | ';

                // var_dump($json);

                // echo '|||||||||||| REGISTRO GUARDADO ||||||||||||';

            }

        }else{

            echo 'No se hizo nada';

        }

    }



    public function send($rut, $token, $data){

        global $CFG;

        require_once("{$CFG->dirroot}/blocks/sic/classes/api_lib.php");

        $response = sic_api::check_connection($rut, $token);

        // var_dump($response);

        return trim(sic_api::send_data($data));



    }

}







 ?>