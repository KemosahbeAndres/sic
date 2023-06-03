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

namespace block_sic\app\infraestructure\web;

use block_sic\app\domain\session;
use block_sic\app\utils\Arrays;
use block_sic\app\utils\Dates;
use stdClass;

class manager_view extends view {

    public function __construct() {
        parent::__construct();
        self::$source = 'block_sic/controlpanel';
        self::$template = new stdClass();
    }

    public function render(session $params): string {
        global $CFG;
        $template = new stdClass();

        $tab = $params->get_tab();

        $template->tab = $tab;

        //$template->tabcourse = $tab < 1 || $tab == 1 || $tab > 5 ? true : false;

        //$template->tabmodules = $tab == 2 ? true : false;

        //$template->tabsections = $tab == 3 ? true : false;

        //$template->tabactivities = $tab == 4 ? true : false;

        //$template->tablessons = $tab == 5 ? true : false;

        $curso = $params->get_course();

        // LOAD COURSE TO TEMPLATE.
        $course = new stdClass();

        $course->id = $curso->get_id();
        $course->code = $curso->get_code();
        $course->startdate = Dates::format($curso->get_startdate());
        $course->enddate = Dates::format($curso->get_enddate());

        $template->course = $course;

        $studentslist = $params->get_students();

        $students = Arrays::void();

        $manager = new stdClass();

        foreach ($studentslist as $user) {
                $student = new stdClass();
                $student->id = $user->get_id();
                $student->nombre = $user->get_name();
                $student->run = $user->get_full_rut();
                $student->avance = $user->get_progress();
                $student->tiempo = $user->get_connection_time();
                $state = $user->get_state();
                $student->completions = $user->count_completions();
                $student->estado = $state->get_state();
                $student->studying = $state->studying();
                $student->reproved = $state->reproved();
                $student->approved = $state->approved();
                $students[] = $student;
        }

        $template->students = $students;

        $managerfound = $params->get_manager();

        $manager->id = $managerfound->get_id();
        $manager->name = $managerfound->get_name();
        $manager->run = $managerfound->get_full_rut();

        $template->manager = $manager;

        $template->countstudents = count($students);

        $teachers = array();

        $teachersfound = $params->get_teachers();

        foreach ($teachersfound as $teacher) {
            $teachers[] = $teacher->object();
        }

        $template->teachers = $teachers;

        // Modulos.

        $modulos = Arrays::void();
        $secciones = Arrays::void();
        $allSections = Arrays::void();
        $actividades = Arrays::void();
        $lessons = Arrays::void();

        foreach ($curso->get_modules() as $module) {
            $modulo = new stdClass;
            $modulo->editting = false;
            $modulo->id = $module->get_id();
            $modulo->code = $module->get_code();
            $modulo->startdate = Dates::format($module->get_startdate());
            $modulo->enddate = Dates::format($module->get_enddate());
            $modulo->sync = $module->get_sync_amount();
            $modulo->async = $module->get_async_amount();
            $modulo->sections = Arrays::void();

            foreach ($module->get_sections() as $section) {
                $seccion = new stdClass();
                $seccion->id = $section->get_id();
                $seccion->name = $section->get_name();
                $seccion->activitiesCount = $section->count_activities();
                $seccion->lessonsCount = $section->count_lessons();

                $seccion->activities = Arrays::void();
                foreach ($section->get_activities() as $activity) {
                    $actividad = new stdClass();
                    $actividad->id = $activity->get_id();
                    $actividad->name = $activity->get_code();
                    $actividad->mandatory = $activity->is_mandatory();
                    $actividad->section = $section->get_name();
                    $actividades[] = $actividad;
                    $seccion->activities[] = $actividad;
                }

                $seccion->lessons = Arrays::void();
                foreach ($section->get_lessons() as $lesson) {
                    $leccion = new stdClass();
                    $leccion->id = $lesson->get_id();
                    $leccion->name = $lesson->get_name();
                    $leccion->date = Dates::format($lesson->get_date());
                    $leccion->duration = $lesson->get_duration();
                    $lessons[] = $leccion;
                    $seccion->lessons[] = $leccion;
                }

                $allSections[] = $seccion;
                $modulo->sections[] = $seccion;
            }

            $modulo->sectionsCount = count($modulo->sections);
            $modulos[] = $modulo;
        }

        // Secciones.
        // Actividades.
        foreach ($curso->get_excluded_sections() as $section) {
            $seccion = new stdClass();
            $seccion->id = $section->get_id();
            $seccion->name = trim($section->get_name()) == "" ? "Sin nombre" : $section->get_name();

            $seccion->activities = Arrays::void();
            foreach ($section->get_activities() as $activity) {
                $actividad = new stdClass();
                $actividad->id = $activity->get_id();
                $actividad->name = $activity->get_code();
                $actividad->mandatory = $activity->is_mandatory();
                $actividad->section = $section->get_name();
                $actividades[] = $actividad;
                $seccion->activities[] = $actividad;
            }
            $seccion->activitiesCount = count($seccion->activities);

            $seccion->lessons = Arrays::void();
            foreach ($section->get_lessons() as $lesson) {
                $leccion = new stdClass();
                $leccion->id = $lesson->get_id();
                $leccion->name = $lesson->get_name();
                $leccion->date = Dates::format($lesson->get_date());
                $leccion->duration = $lesson->get_duration();
                $leccion->section = $section->get_name();
                $lessons[] = $leccion;
                $seccion->lessons[] = $leccion;
            }
            $seccion->lessonsCount = count($seccion->lessons);
            $allSections[] = $seccion;
            $secciones[] = $seccion;
        }

        $template->modules = $modulos;
        $template->excluded = $secciones;
        $template->sections = $allSections;
        $template->activities = $actividades;
        var_dump($lessons);
        $template->lessons = $lessons;
        $template->totalact = count($actividades);
        $template->courseid = $curso->get_id();
        $template->redirecturl = $CFG->wwwroot . "/blocks/sic/controlpanel.php?courseid=$course->id&instance={$params->get_block()}";
        $template->redirectmoduleurl = $CFG->wwwroot . "/blocks/sic/controlpanel.php?tab=2&courseid=$course->id&instance={$params->get_block()}";
        $template->data = json_encode($template);

        self::$template = $template;
        return <<<HTML
            <span id="templatedata" json='$template->data'></span>
            <div id="app">
                <div id="course-data" class="card mb-3">
                    <div class="card-body">
                        <h4 class="table-title">Curso</h4>
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Codigo</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Termino</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{content.course.id}}</td>
                                    <td>{{content.course.code}}</td>
                                    <td>{{content.course.startdate}}</td>
                                    <td>{{content.course.enddate}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <ul class="nav nav-tabs nav-fill ml-0">
                  <li class="nav-item">
                    <a class="nav-link" :class="{ active: courseActive }" :href="content.redirecturl+'&tab=1'">Participantes</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" :class="{ active: moduleActive }" :href="content.redirecturl+'&tab=2'">Modulos</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" :class="{ active: sectionActive }"  :href="content.redirecturl+'&tab=3'">Secciones</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" :class="{ active: lessonActive }"  :href="content.redirecturl+'&tab=4'">Clases</a>
                  </li>
                </ul>
                <div id="tab-content">
                    <transition name="fade">
                        <div id="course-content" v-show="courseActive">
                            <h4 class="table-title">Gestor</h4>
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>RUT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{content.manager.id}}</td>
                                        <td>{{content.manager.name}}</td>
                                        <td>{{content.manager.run}}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <h4 class="table-title">Profesores</h4>
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>RUT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="teacher in content.teachers" :key="teacher.id">
                                        <td>{{teacher.id}}</td>
                                        <td>{{teacher.name}}</td>
                                        <td>{{teacher.run}}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <h4 class="table-title">Alumnos</h4>
                            <table id="alumnos" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>RUT</th>
                                        <th>Avance</th>
                                        <th>Conexion</th>
                                        <th>Estado
                                            <button
                                                type="button"
                                                class="btn btn-primary"
                                                @click="edittingStates = !edittingStates"
                                            ><i class="bi" :class="{'bi-pencil': !edittingStates, 'bi-x-lg': edittingStates}"></i></button>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                        <tr v-for="student in content.students" :key="student.id">
                                            <td>{{student.id}}</td>
                                            <td>{{student.nombre}}</td>
                                            <td>{{student.run}}</td>
                                            <td>{{student.avance}}</td>
                                            <td>{{student.tiempo}}</td>
                                            <td v-show="!edittingStates">{{student.estado}}</td>
                                            <td v-show="edittingStates">
                                                <select v-model="student.estado" class="form-select">
                                                    <option :selected="student.estado=='cursando'" value="cursando">cursando</option>
                                                    <option :selected="student.estado=='reprobado'" value="reprobado">reprobado</option>
                                                    <option :selected="student.estado=='aprobado'" value="aprobado">aprobado</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr v-show="edittingStates">
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td><button
                                                type="button"
                                                class="btn btn-success"
                                                @click="submitAll()"
                                            >Guardar <i class="bi bi-check2-square" ></i></button></td>
                                        </tr>
                                </tbody>
                            </table>
                            <form name="statesForm" :action="content.redirecturl+'&tab=1'" method="post">
                                <input type="hidden" name="action" value="change_states" required>
                                <input type="hidden" name="data" required>
                            </form>
                        </div>
                    </transition>
                    <transition name="fade">
                        <div id="module-content" v-show="moduleActive">
                            <h4 class="table-title">Modulos</h4>
                            <table class="table table-striped" :class="{'table-hover': !addingModules}">
                                <thead>
                                    <tr>
                                        <th>Acciones</th>
                                        <th>ID</th>
                                        <th>Codigo</th>
                                        <th>Fecha Inicio</th>
                                        <th>Fecha Termino</th>
                                        <th>Activitdades Sincronas</th>
                                        <th>Actividades Asincronas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="module of content.modules" :key="module.id">
                                        <td>
                                            <input v-show="!module.editting" type="button" class="btn btn-primary" value="Editar" @click="editModule(module.id)" :disabled="!canEditModule"/>
                                            <input v-show="module.editting" type="button" class="btn btn-success" value="Guardar" @click="submitEditModule(module.id)"/>
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#deleteconfirmmodal" @click="selectModule(module.id)">
                                                Borrar
                                            </button>
                                        </td>
                                        <td v-show="!module.editting">{{module.id}}</td><td v-show="module.editting"><input type="number" class="form-control" :value="module.id" disabled/></td>
                                        <td v-show="!module.editting">{{module.code}}</td><td v-show="module.editting"><input type="text" class="form-control" v-model="module.code" required/></td>
                                        <td v-show="!module.editting">{{module.startdate}}</td><td v-show="module.editting"><input type="date" class="form-control" v-model="module.startdate" required/></td>
                                        <td v-show="!module.editting">{{module.enddate}}</td><td v-show="module.editting"><input type="date" class="form-control" v-model="module.enddate" required/></td>
                                        <td v-show="!module.editting">{{module.sync}}</td><td v-show="module.editting"><input type="number" class="form-control" v-model="module.sync" required/></td>
                                        <td v-show="!module.editting">{{module.async}}</td><td v-show="module.editting"><input type="number" class="form-control" v-model="module.async" required/></td>
                                    </tr>
                
                                    <tr v-show="addingModules">
                                        <td></td>
                                        <td><input class="form-control" type="number" v-model="newmodule.id" value="0" disabled></td>
                                        <td><input class="form-control" type="text" v-model="newmodule.code" required></td>
                                        <td><input class="form-control" type="date" v-model="newmodule.startdate" required></td>
                                        <td><input class="form-control" type="date" v-model="newmodule.enddate" required></td>
                                        <td><input class="form-control" type="number" v-model="newmodule.sync" required></td>
                                        <td><input class="form-control" type="number" v-model="newmodule.async" required></td>
                                    </tr>
                                    <tr>
                                        <td v-show="!addingModules" colspan="7" class="btn-like text-center clickable" @click="addingModules = !addingModules">Nuevo Modulo <i class="bi bi-plus-lg"></i></td>
                                        <td v-show="addingModules" colspan="3" class="btn-danger text-center clickable" @click="resetModuleData()">Cancelar</td>
                                        <td v-show="addingModules" colspan="4" class="btn-success text-center clickable" @click="submitAll()">Guardar Modulo</td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="alert alert-warning" role="alert" v-show="noModules && !addingModules">
                                No hay modulos registrados!
                            </div>
                            <form name="moduleForm" method="post" :action="content.redirecturl+'&tab=2'">
                                <input type="hidden" name="action" value="" />
                                <input type="hidden" name="data" value="" />
                            </form>
                            <div class="modal fade" id="deleteconfirmmodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteconfirmmodal">Modal title</h5>
                                        </div>
                                        <div class="modal-body">
                                            <h4>Desea eliminar este modulo ID {{moduleid}}</h4>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="button" class="btn btn-danger" @click="deleteModule()">Borrar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </transition>
                    <transition name="fade">
                        <div id="sections-content" v-show="sectionActive">
                            <h4 class="table-title">Secciones Sin Asignar</h4>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Acciones</th>
                                        <th>ID</th>
                                        <th>Titulo</th>
                                        <th>Actividades</th>
                                        <th>Clases</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="section in content.excluded" :key="section.id">
                                        <td>
                                            <input type="button" value="Abrir" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#sectionDetailsModal" @click="openSection(section)">
                                            <input type="button" data-bs-toggle="modal" data-bs-target="#sectionModal" class="btn btn-primary" value="Asignar" @click="openSection(section)">
                                        </td>
                                        <td>{{section.id}}</td>
                                        <td>{{section.name}}</td>
                                        <td>{{section.activitiesCount}}</td>
                                        <td>{{section.lessonsCount}}</td>
                                    </tr>                                    
                                </tbody>
                            </table>
                            <h4 class="table-title">Secciones Asignadas</h4>
                            <table class="table table-striped" v-for="module in content.modules" :key="module.id">
                                <thead>
                                    <tr>
                                        <th colspan="5">Modulo: {{module.code}} (ID {{module.id}})</th>
                                    </tr>
                                    <tr>
                                        <th>Acciones</th>
                                        <th>ID</th>
                                        <th>Titulo</th>
                                        <th>Actividades</th>
                                        <th>Clases</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="section in module.sections" :key="section.id">
                                        <td>
                                            <input type="button" value="Abrir" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#sectionDetailsModal" @click="openSection(section)">
                                            <input type="button" class="btn btn-danger" value="Liberar" @click="dettachSection(section.id)">
                                        </td>
                                        <td>{{section.id}}</td>
                                        <td>{{section.name}}</td>
                                        <td>{{section.activitiesCount}}</td>
                                        <td>{{section.lessonsCount}}</td>
                                    </tr> 
                                    <tr v-show="module.sectionsCount <= 0">
                                        <td colspan="5" class="text-center">No hay ninguna seccion asignada en este modulo!</td>
                                    </tr>                                   
                                </tbody>
                            </table>
                        </div>
                    </transition>   
                    <transition name="fade">
                        <div id="lessonContent" v-show="lessonActive">
                            <h4 class="table-title">Clases</h4>
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Acciones</th>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Fecha</th>
                                        <th>Duracion (minutos)</th>
                                        <th>Seccion</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="lesson in content.lessons" :key="lesson.id">
                                        <td></td>
                                        <td>{{lesson.id}}</td>
                                        <td>{{lesson.name}}</td>
                                        <td>{{lesson.date}}</td>
                                        <td>{{lesson.duration}}</td>
                                        <td>{{lesson.section}}</td>
                                    </tr>
                                    <tr v-show="addingLesson">
                                        <td></td>
                                        <td><input type="number" v-model="newlesson.id" value="0" disabled></td>
                                        <td>
                                            <select v-model="newlesson.activityid">
                                                <option value="0" selected>Selecciona una actividad</option>
                                                <option v-for="activity in content.activities" v-if="activity.mandatory" :value="activity.id" :key="activity.id">{{activity.name}} ({{activity.section}})</option>
                                            </select>
                                        </td>
                                        <td><input type="date" v-model="newlesson.date"></td>
                                        <td><input type="number" v-model="newlesson.duration"></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td v-show="!addingLesson" colspan="6" class="btn-like text-center clickable" @click="addingLesson = !addingLesson">Nueva Clase</td>
                                        <td v-show="addingLesson" colspan="3" class="btn-danger text-center clickable" @click="addingLesson = false">Cancelar</td>
                                        <td v-show="addingLesson" colspan="3" class="btn-success text-center clickable" @click="saveLesson()">Guardar</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </transition>
                    
                    <div class="modal fade" id="sectionModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Selecciona Modulo</h5>
                                </div>
                                <div class="modal-body">
                                    <h4 class="text-center">Seccion: {{sectionSelected.name}} (ID {{sectionSelected.id}})</h4>                                        
                                    <select class="form-control" name="moduleSelect" id="moduleSelect">
                                        <option v-for="module in content.modules" :key="module.id" :value="module.id">ID: {{module.id}} | {{module.code}}</option>
                                    </select>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" href="#sectionModal" @click="attachSection()">Guardar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal fade" id="sectionDetailsModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Detalles Seccion "{{sectionSelected.name}}"</h5>
                                </div>
                                <div class="modal-body">
                                    <div class="container-fluid mb-2">
                                        <table class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Nombre</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>{{sectionSelected.id}}</td>
                                                    <td>{{sectionSelected.name}}</td>                                                            
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="container-fluid">
                                        <h4 class="table-title">Actividades</h4>
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Nombre</th>
                                                    <th>Obligatoria</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="activity in sectionSelected.activities" :key="activity.id">
                                                    <td>{{activity.id}}</td>
                                                    <td>{{activity.name}}</td>
                                                    <td>{{activity.mandatory ? "Si" : "No"}}</td>                                                             
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <form :action="content.redirecturl + '&tab=3'" method="post" id="sectionForm">
                        <input type="hidden" name="action" value="" />
                        <input type="hidden" name="data" />
                    </form>
                    <form :action="content.redirecturl + '&tab=4'" method="post" id="lessonForm">
                        <input type="hidden" name="action" value="" />
                        <input type="hidden" name="data" value="" />
                    </form>
                </div>
            </div>
            
            <script type="module">
            
                //window.bootstrap = require('bootstrap/dist/js/bootstrap.bundle.js')
            
                Date.prototype.dateToInput = function(){
                    return this.getFullYear() + "-" + ("0" + (this.getMonth() + 1)).substr(-2,2) + "-" + ("0" + this.getDate()).substr(-2,2);
                }
            
                var templatedata = document.getElementById("templatedata");
                var json = JSON.parse(templatedata.getAttribute("json"));
            
                let statesForm = document.forms.statesForm;
                let moduleForm = document.forms.moduleForm;
                
                let modalEl = document.querySelector('#sectionModal')
                let sectionModal = bootstrap.Modal.getOrCreateInstance(modalEl)
                
                let sectionForm = document.querySelector("#sectionForm")
                
                let lessonForm = document.querySelector("#lessonForm")
                           
                var app = new Vue({
                    el: "#app",
                    data: {
                        content: json,
                        newmodule: {
                            id: 0,
                            code: "",
                            startdate: "",
                            enddate: "",
                            sync: 0,
                            async: 0,
                        },
                        newlesson: {
                            id: 0,
                            activityid: 0,
                            date: "",
                            duration: 0,
                        },
                        edittingStates: false,
                        addingModules: false,
                        addingLesson: false,
                        canEditModule: true,
                        moduleid: 0,
                        sectionOpen: false,
                        sectionSelected: {
                            id: 0,
                            name: "",
                            activitiesCount: 0,
                            lessonsCount: 0,
                            activities: [],
                            lessons: [],
                        },
                    },
                    computed: {
                        courseActive: function () {
                            return this.content.tab == 1
                        },
                        moduleActive: function () {
                            return this.content.tab == 2
                        },
                        sectionActive: function () {
                            return this.content.tab == 3
                        },
                        lessonActive: function () {
                            return this.content.tab == 4
                        },
                        maxEditting: function () {
                            return this.addingModules ^ this.edittingStates;
                        },
                        noModules: function () {
                            return this.content.modules.length <= 0;
                        }
                    },
                    methods: {
                        saveLesson: function (){
                            let data = {
                                id: parseInt(this.newlesson.id),
                                activityid: parseInt(this.newlesson.activityid),
                                date: this.toTimestamp(this.newlesson.date),
                                duration: parseInt(this.newlesson.duration)
                            }
                            
                            console.log(data)
                            
                            lessonForm.children.action.value = "attach_lesson"
                            lessonForm.children.data.value = JSON.stringify(data)
                            document.body.append(lessonForm)
                            console.log(lessonForm)
                            lessonForm.submit()
                        },
                        dettachSection: function (sectionid) {
                            let data = {
                                sectionid: parseInt(sectionid)
                            }
                            sectionForm.children.action.value = "dettach_section"
                            sectionForm.children.data.value = JSON.stringify(data)
                            document.body.append(sectionForm)
                            sectionForm.submit()
                        },                        
                        attachSection: function (){
                            let select = document.querySelector("#moduleSelect")
                            let moduleid = parseInt(select.selectedOptions[0].value)
                            let data = {
                                sectionid: parseInt(this.sectionSelected.id),
                                moduleid: moduleid
                            }
                            sectionForm.children.action.value = "attach_section"
                            sectionForm.children.data.value = JSON.stringify(data)
                            document.body.append(sectionForm);
                            sectionForm.submit();
                        },
                        openSection: function (section){
                            this.sectionSelected.id = section.id;
                            this.sectionSelected.name = section.name;
                            this.sectionSelected.activitiesCount = section.activitiesCount;
                            this.sectionSelected.lessonsCount = section.lessonsCount;
                            this.sectionSelected.activities = section.activities;
                            //sectionModal.toggle()
                        },
                        deleteModule: function () {
                            let data = {
                                moduleid: this.moduleid
                            }
                            moduleForm.children.action.value = "delete_module"
                            moduleForm.children.data.value = JSON.stringify(data)
                            document.body.append(moduleForm);
                            moduleForm.submit();
                        },
                        selectModule: function (id) {
                            this.moduleid = parseInt(id)
                        },
                        toTimestamp: function (date) {
                            return Math.trunc(new Date(date).getTime() / 1000)
                        },
                        toHours: function (time) {
                            return Math.trunc( time / 60 / 60 )
                        },
                        submitEditModule: function (id) {
                            let dataobject = {
                                id: 0,
                                code: "",
                                startdate: 0,
                                enddate: 0,
                                sync: 0,
                                async: 0
                            }
                            for(let module of this.content.modules) {
                                if(module.id == id) {
                                    dataobject.id = parseInt(module.id),
                                    dataobject.code = String(module.code),
                                    dataobject.startdate = this.toTimestamp(module.startdate),
                                    dataobject.enddate = this.toTimestamp(module.enddate),
                                    dataobject.sync = parseInt(module.sync),
                                    dataobject.async = parseInt(module.async)
                                    break
                                }
                            }
                            moduleForm.children.action.value = "modify_module";
                            moduleForm.children.data.value = JSON.stringify(dataobject);
                            document.body.append(moduleForm);
                            moduleForm.submit();
                        },
                        resetModuleData: function () {
                            this.newmodule = {
                                id: 0,
                                code: "",
                                startdate: "",
                                enddate: "",
                                sync: 0,
                                async: 0,
                            };
                            this.addingModules = false;
                        },
                        submitStatesForm: function () {
                            //alert("submitting states data")
                            statesForm.children.data.value = JSON.stringify(this.content.students);
                            //this.content.redirecturl += "#alumnos"
                            document.body.append(statesForm)
                            statesForm.submit();
                        },
                        submitNewModuleForm: function () {
                            let dataobject = {
                                id: parseInt(this.newmodule.id),
                                code: String(this.newmodule.code),
                                startdate: this.toTimestamp(this.newmodule.startdate),
                                enddate: this.toTimestamp(this.newmodule.enddate),
                                sync: parseInt(this.newmodule.sync),
                                async: parseInt(this.newmodule.async)
                            };
                            //console.warn(dataobject);
                            moduleForm.children.action.value = "create_module";
                            moduleForm.children.data.value = JSON.stringify(dataobject);
                            document.body.append(moduleForm);
                            moduleForm.submit();
                        },
                        submitAll: function () {
                            //alert("submit activado")
                            if(!this.maxEditting) return;
            
                            //alert("Max edittin ok")
                            if(this.edittingStates) {
                                this.submitStatesForm()
                            } else if (this.addingModules) {
                                this.submitNewModuleForm()
                            }
                        },
                        editModule: function (id) {
                            if(!this.canEditModule) return
                            for(let module of this.content.modules) {
                                if(module.id == id && this.canEditModule) {
                                    module.editting = true
                                    this.canEditModule = false
                                } else if(module.id == id && !this.canEditModule) {
                                    module.editting = false
                                    this.canEditModule = true
                                } else {
                                    module.editting = false
                                }
                            }
                        },
                    },
                })
            </script>
        HTML;
    }
}
