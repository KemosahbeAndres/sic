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

use block_sic\app\application\contracts\iactivities_repository;
use block_sic\app\application\contracts\icourses_repository;
use block_sic\app\application\contracts\ilessons_repository;
use block_sic\app\application\contracts\imodules_repository;
use block_sic\app\application\contracts\isections_repository;
use block_sic\app\domain\course;
use block_sic\app\domain\module;

class consult_course_controller {
    private $courses;
    private $modules;
    private $sections;
    private $sectionloader;

    public function __construct(
        icourses_repository $courses,
        imodules_repository $modules,
        isections_repository $sections,
        iactivities_repository $activities,
        ilessons_repository $lessons
        ) {
        $this->courses = $courses;
        $this->modules = $modules;
        $this->sections = $sections;
        $this->sectionloader = new list_sections_controller($activities, $lessons);
    }

    public function execute(int $courseid): course {
        $course = $this->courses->by_id($courseid);
        $output = new course(
            $course->id,
            $course->code,
            $course->startdate,
            $course->enddate
        );
        //echo "CONTROLLER ## {$course->id} ## {$course->code} ## {$course->startdate} ## {$course->enddate} ## <br>";

        //echo "OUTSIDE MODEL ## {$output->get_id()} ## {$output->get_code()} ## {$output->get_startdate()} ## {$output->get_enddate()} ## <br>";

        $modulelist = $this->modules->related_to($courseid);

        $secciones = $this->sectionloader->execute(
            $this->sections->from($courseid)
        );
        foreach ($secciones as $seccion) {
            $output->add_mdl_section($seccion);
        }
        if (!empty($modulelist)) {
            foreach ($modulelist as $module) {
                $modulo = new module(
                    $module->id,
                    $module->code,
                    $module->startdate,
                    $module->enddate,
                    $module->sync,
                    $module->async
                );
                $secciones = $this->sectionloader->execute(
                    $this->sections->related_to($modulo->get_id())
                );
                foreach ($secciones as $seccion) {
                    // Agregar al modulo
                    if ($seccion->assigned()) {
                        $modulo->add_section($seccion);
                    }
                }
                // Agregar al curso.
                $output->add_module($modulo);
            }
        }

        return $output;
    }

}