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

 require_once(__DIR__ . '/../../config.php');

use block_sic\app\application\edit_module_controller;
use block_sic\app\application\login_controller;
 use block_sic\app\application\consult_course_controller;
 use block_sic\app\application\list_students_controller;
 use block_sic\app\application\load_course_data_controller;
use block_sic\app\application\structures\course;
use block_sic\app\application\structures\module;
use block_sic\app\application\structures\user;
 use block_sic\app\infraestructure\persistence\activities_repository;
 use block_sic\app\infraestructure\persistence\attendances_repository;
 use block_sic\app\infraestructure\persistence\courses_repository;
 use block_sic\app\infraestructure\persistence\dedications_repository;
 use block_sic\app\infraestructure\persistence\grades_repository;
 use block_sic\app\infraestructure\persistence\lessons_repository;
 use block_sic\app\infraestructure\persistence\modules_repository;
 use block_sic\app\infraestructure\persistence\completion_repository;
 use block_sic\app\infraestructure\persistence\roles_repository;
 use block_sic\app\infraestructure\persistence\sections_repository;
 use block_sic\app\infraestructure\persistence\states_repository;
 use block_sic\app\infraestructure\persistence\users_repository;
 use block_sic\app\utils\Arrays;

global $CFG, $DB, $USER;

$courseid = required_param("courseid", PARAM_INT);
$tab = required_param("tab", PARAM_INT);
$instance = required_param("instance", PARAM_INT);

$users = new users_repository();
$states = new states_repository();
$roles = new roles_repository();
$courses = new courses_repository();
$modules = new modules_repository();
$sections = new sections_repository();
$activities = new activities_repository();
$lessons = new lessons_repository();

$moduleeditor = new edit_module_controller($modules, $courses);

$identity = new login_controller($users, $roles, $courses);

$me = $identity->execute($USER->id, $courseid);

$url = $CFG->wwwroot . "/blocks/sic/controlpanel.php?courseid={$courseid}";

if ($me->is_manager()) {

    if (!isset($_POST['codigo']) || !isset($_POST['startdate']) ||
     !isset($_POST['enddate']) || !isset($_POST['sincronas']) ||
     !isset($_POST['asincronas']) || !isset($_POST['moduleid'])) {
        header("Location: " . $url . "&errorcode=1");
        die();
    }

    $courseloader = new consult_course_controller(
        $courses,
        $modules,
        $sections,
        $activities,
        $lessons
    );

    $course = $courseloader->execute($courseid);

    $id = intval($_POST['moduleid']);
    //var_dump($id);

    $codigo = strval($_POST['codigo']);

    $startdate = strtotime($_POST['startdate']);

    $enddate = strtotime($_POST['enddate']);

    $sync = intval($_POST['sincronas']);

    $async = intval($_POST['asincronas']);

    if (empty($codigo) || $startdate <= 0 || $enddate <= 0 || $sync < 0 || $async < 0 || $id <= 0) {
        redirect(new moodle_url('/block/sic/controlpanel.php', array('courseid'=> $courseid, 'tab' => 2, 'instance' => $instance)), 'Datos incorrectos');
    }

    $module = new module($id, $codigo, $startdate, $enddate, $sync, $async);

    $moduleeditor->execute(filter_var_array(INPUT_POST));

}

header("Location: " . $url);
die();


