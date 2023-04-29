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

use block_sic\app\application\dettach_section_controller;
use block_sic\app\application\login_controller;
 use block_sic\app\application\consult_course_controller;
 use block_sic\app\infraestructure\persistence\activities_repository;
 use block_sic\app\infraestructure\persistence\courses_repository;
 use block_sic\app\infraestructure\persistence\lessons_repository;
 use block_sic\app\infraestructure\persistence\modules_repository;
 use block_sic\app\infraestructure\persistence\roles_repository;
 use block_sic\app\infraestructure\persistence\sections_repository;
 use block_sic\app\infraestructure\persistence\states_repository;
 use block_sic\app\infraestructure\persistence\users_repository;
 use block_sic\app\utils\Arrays;

global $CFG, $DB;

$courseid = required_param("courseid", PARAM_INT);

$users = new users_repository();
$states = new states_repository();
$roles = new roles_repository();
$courses = new courses_repository();
$modules = new modules_repository();
$sections = new sections_repository();
$activities = new activities_repository();
$lessons = new lessons_repository();

$sectiondettacher = new dettach_section_controller($sections);

$identity = new login_controller($users, $roles, $courses);

$me = $identity->execute($USER->id, $courseid);

$url = $CFG->wwwroot . "/blocks/sic/controlpanel.php?courseid={$courseid}";

if ($me->is_manager()) {

    if (!isset($_POST['sectionid'])) {
        header("Location: " . $url . "&errorcode=1");
        die();
    }

    $sectionid = $_POST['sectionid'];

    if ($sectionid <= 0) {
        header("Location: " . $url);
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

    $modules = $course->get_modules();

    foreach ($modules as $module) {
        foreach ($module->get_sections() as $section) {
            if ($section->get_id() == $sectionid) {
                $sectiondettacher->execute($section);
            }
        }
    }

}

header("Location: " . $url);
die();


