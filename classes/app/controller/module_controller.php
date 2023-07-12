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

namespace block_sic\app\controller;

use block_sic\app\domain\request;
use block_sic\app\domain\response;
use block_sic\app\infraestructure\persistence\repository_context;
use block_sic\app\utils\Dates;
use core\session\exception;

class module_controller extends controller {
    public function __construct(repository_context $context) {
        parent::__construct($context);
        $this->content->coursepage = true;
    }

    public function creating(request $request): response {
        $this->content->courseid = intval($request->params->courseid);
        return $this->response('course/module/create');
    }

    public function save(request $request): response {
        $message = "No se realizo ningun cambio!";
        try{
            $courseid = intval($request->params->courseid);
            $id = 0;
            if(isset($request->params->moduleid)){
                $id = intval($request->params->moduleid);
            }
            $code = trim(strval($request->params->code));
            $startdate = strtotime($request->params->startdate);
            $enddate = strtotime($request->params->enddate);
            $sync = intval($request->params->sync);
            $async = intval($request->params->async);

            if(empty($code) or $startdate < 0 or $enddate < 0 or $sync < 0 or $async < 0){
                throw new \exception("Error");
            }

            $module = (object)[
                'id' => $id,
                'code' => $code,
                'startdate' => $startdate,
                'enddate' => $enddate,
                'sync' => $sync,
                'async' => $async,
            ];

            echo "<br>";
            echo "Timestamp: ".$startdate;
            echo "<br>";
            echo "Fecha:".Dates::format_date_time($startdate);
            echo "<br>";

            $this->context->modules->attach_to($module, $courseid);
            $message = "Modulo guardado con exito!";

        }catch (\exception $e){
            return $this->redirect('course', $request->params,'Error. Datos ingresados invalidos!');
        }
        return $this->redirect('course', $request->params,$message);
    }

}