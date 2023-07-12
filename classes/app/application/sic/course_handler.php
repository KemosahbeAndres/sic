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

namespace block_sic\app\application\sic;

use block_sic\app\application\consult_course_controller;
use block_sic\app\domain\course;

class course_handler extends abstract_handler {
    /**
     * @var course
     */
    private $course;
    private $rut;
    private $systemid;
    private $token;
    private $oferta;
    private $grupo;

    /**
     * @param course $course
     */
    public function __construct(course $course, object $config) {
        $this->course = $course;
        $this->rut = trim(strval($config->rut_otec));
        $this->systemid = 1350;
        $this->token = trim(strval($config->token));
        $this->oferta = trim(strval($config->codigo_oferta));
        $this->grupo = trim(strval($config->codigo_grupo));
    }

    public function handle(object $request): ?object {

        $request->rutOtec = $this->rut;
        $request->idSistema = $this->systemid;
        $request->token = $this->token;
        $request->codigoOferta = $this->oferta;
        $request->codigoGrupo = $this->grupo;
        $request->cantActividadSincronica = $this->course->get_sync_activities();
        $request->cantActividadAsincronica = $this->course->get_async_activities();

        return parent::handle($request);
    }

}