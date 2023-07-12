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

use block_sic\app\controller\module_controller;
use block_sic\app\controller\section_controller;
use block_sic\app\controller\student_controller;
use block_sic\app\SicApplication;
use block_sic\app\controller\course_controller;

global $PAGE, $OUTPUT, $COURSE, $USER;

$courseid = required_param("courseid", PARAM_INT);

$instance = required_param("instance", PARAM_INT);

$url = new moodle_url('/blocks/sic/dashboard.php', array('courseid' => $courseid, 'instance' => $instance));

$PAGE->set_url($url);

$PAGE->set_context(\context_course::instance($courseid));

$PAGE->set_pagelayout("standard");

$PAGE->set_title('Integracion API SIC');

$PAGE->set_heading('Integracion API SIC');

echo $OUTPUT->header();

$app = new SicApplication();

$app->default('course', course_controller::class, 'index');

$app->get('freesections', course_controller::class, 'free_sections');

$app->get('participants', course_controller::class, 'participants');

$app->get('sic', course_controller::class, 'sicpanel');

$app->get('resume', course_controller::class, 'resume');

$app->get('sectiondetail', section_controller::class, 'details');

$app->get('studentdetail', student_controller::class, 'details');

//modulos

$app->get('create_module', module_controller::class, 'creating');

$app->post('save_module', module_controller::class, 'save');

$app->run();

echo $OUTPUT->footer();