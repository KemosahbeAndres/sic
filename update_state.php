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

use block_sic\app\application\change_state_controller;
use block_sic\app\application\create_module_controller;
use block_sic\app\application\login_controller;
 use block_sic\app\application\consult_course_controller;
use block_sic\app\application\list_students_controller;
use block_sic\app\application\load_course_data_controller;
use block_sic\app\application\structures\course;
use block_sic\app\application\structures\module;
use block_sic\app\application\structures\user;
use block_sic\app\domain\state;
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

global $CFG, $DB;

$courseid = required_param("courseid", PARAM_INT);

$users = new users_repository();
$states = new states_repository();
$dedications = new dedications_repository();
$progress = new completion_repository();
$grades = new grades_repository();
$attendances = new attendances_repository();
$roles = new roles_repository();
$courses = new courses_repository();
$modules = new modules_repository();
$sections = new sections_repository();
$activities = new activities_repository();
$lessons = new lessons_repository();

$userdataloader = new load_course_data_controller(
    $states,
    $dedications,
    $progress,
    $grades,
    $attendances
);

$identity = new login_controller($users, $roles, $courses);

$me = $identity->execute($USER->id, $courseid);

$url = $CFG->wwwroot . "/blocks/sic/controlpanel.php?courseid={$courseid}";

if ($me->is_manager()) {

    $courseloader = new consult_course_controller(
        $courses,
        $modules,
        $sections,
        $activities,
        $lessons
    );

    $course = $courseloader->execute($courseid);

    $userfinder = new list_students_controller($courses, $users, $identity);

    $userlist = $userfinder->execute($courseid);

    $statecontroller = new change_state_controller($states);

    foreach ($userlist as $user) {
        if ($user->is_student()) {
            $userdata = $userdataloader->execute($user, $course);
            $userid = $userdata->get_id();
            $key = 'codefor' . $userid;
            if (isset($_POST[$key])) {
                $value = intval($_POST[$key]);
                $state = new state([
                    'id' => 0,
                    'codigo' => $value,
                    'state' => ""
                ]);
                $statecontroller->execute($userdata, $state, $course);
            }
        }
    }
    if (isset($_POST['redirect'])) {
        $url = $_POST['redirect'];
    }
}

header("Location: " . $url);
die();
