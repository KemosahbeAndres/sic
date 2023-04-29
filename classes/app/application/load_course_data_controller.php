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

namespace block_sic\app\application;

use block_sic\app\application\contracts\iattendance_repository;
use block_sic\app\application\contracts\idedication_repository;
use block_sic\app\application\contracts\igrades_repository;
use block_sic\app\application\contracts\icompletion_repository;
use block_sic\app\domain\course;
use block_sic\app\domain\section_dedication;
use block_sic\app\domain\state;
use block_sic\app\domain\activity_grade;
use block_sic\app\domain\activity_completion;
use block_sic\app\domain\section;
use block_sic\app\domain\student;
use block_sic\app\domain\user;

class load_course_data_controller {
    private $sectiondataloader;

    /**
     * @param idedication_repository $dedications
     * @param icompletion_repository $progress
     * @param igrades_repository $grades
     * @param iattendance_repository $attendances
     */

    public function __construct(
        idedication_repository $dedications,
        icompletion_repository $progress,
        igrades_repository     $grades,
        iattendance_repository $attendances
    ) {
        $this->sectiondataloader = new load_section_data_controller($dedications, $progress, $grades, $attendances);
    }

    public function execute(student $user): student {

        $course = $user->get_course();

        if (is_null($course)) {
            return $user;
        }

        if (empty($course->get_modules())) {
            $mdlsections = $course->get_mdl_sections();
            $this->sectiondataloader->execute($user, $mdlsections);
        } else {
            // Modules found.
            foreach ($course->get_modules() as $module) {
                $sections = $module->get_sections();
                $this->sectiondataloader->execute($user, $sections);
            }
        }

        return $user;
    }

}
