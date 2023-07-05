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

use block_sic\app\application\consult_course_controller;
use block_sic\app\application\load_course_data_controller;
use block_sic\app\application\student_finder;
use block_sic\app\domain\activity;
use block_sic\app\domain\lesson;
use block_sic\app\domain\request;
use block_sic\app\domain\response;
use block_sic\app\infraestructure\persistence\repository_context;

class student_controller extends controller {

    private $studentFinder;
    /**
     * @param repository_context $context
     */
    public function __construct(repository_context $context) {
        parent::__construct($context);
        $this->studentFinder = new student_finder($context);
    }

    public function details(request $request): response {
        $this->content->participantspage = true;
        $model = new \stdClass();
        try{
            $id = intval($request->params->id);
            $courseid = intval($request->params->courseid);
            $student = $this->studentFinder->execute($id, $courseid);
            $model = $student->__toObject();
            $model->activities = array();
            /** @var activity $activity */
            foreach ($student->get_course()->get_activities() as $activity){
                $completion = $student->get_completion($activity);
                $grade = $student->get_grade($activity);
                $data = $activity->__toObject();
                $data->completed = "NO";
                $data->grade = 0;
                if(!is_null($completion)){
                    $data->completed = $completion->completed() ? "SI" : "NO";
                }
                if(!is_null($grade)){
                    $data->grade = $grade->get_grade();
                }
                $model->activities[] = $data;
            }
            $model->lessons = array();
            /** @var lesson $lesson */
            foreach ($student->get_course()->get_lessons() as $lesson){
                $attendance = $student->get_attendance($lesson);
                $data = $lesson->__toObject();
                $data->assist = "NO";
                if(!is_null($attendance)){
                    $data->assist = $attendance->is_present() ? "SI" : "NO";
                }
                $model->lessons[] = $data;
            }
        }catch (\exception $e){}
        $this->content->student = $model;
        return $this->response('participants/students/details');
    }


}