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

namespace block_sic\app\domain;

final class student extends user {

    private $state;
    private $active;
    private $dedications;
    private $completions;
    private $attendances;
    private $grades;

    /**
     * @param int $id
     * @param string $name
     * @param int $rut
     * @param string $dv
     */
    public function __construct(int $id, string $name, int $rut, string $dv, string $role, bool $active) {
        parent::__construct($id, $name, $rut, $dv, $role);
        $this->state = null;
        $this->dedications = array();
        $this->completions = array();
        $this->attendances = array();
        $this->grades = array();
        $this->active = $active;
    }
    /**
     * @return state
     */
    public function get_state(): ?state {
        return $this->state;
    }

    /**
     * @param state $state
     */
    public function set_state(state $state) {
        $this->state = $state;
    }

    public function is_active(): bool {
        return $this->active;
    }

    /**
     * @return array
     */
    public function get_dedication(section $section): ?section_dedication {
        foreach ($this->dedications as $dedication) {
            if ($section->equal($dedication->get_section())) {
                return $dedication;
            }
        }
        return null;
    }

    /**
     * @param section_dedication $dedication
     */
    public function add_dedication(section_dedication $dedication) {
        if (in_array($dedication, $this->dedications, true)) {
            return false;
        }
        $this->dedications[] = $dedication;
    }

    /**
     * @param activity $activity
     * @return activity_completion
     */
    public function get_completion(activity $activity): ?activity_completion {
        foreach ($this->completions as $completion) {
            if ($activity->equal($completion->get_activity())) {
                return $completion;
            }
        }
        return null;
    }

    /**
     * @param activity_completion $completion
     */
    public function add_completion(activity_completion $completion) {
        if (in_array($completion, $this->completions, true)) {
            return false;
        }
        $this->completions[] = $completion;
    }

    /**
     * @return lesson_attendance|null
     */
    public function get_attendance(lesson $lesson): ?lesson_attendance {
        foreach ($this->attendances as $attendance) {
            if ($attendance->get_lesson()->equal($lesson)) {
                return $attendance;
            }
        }
        return null;
    }

    /**
     * @param lesson_attendance $attendance
     * @return false|void
     */
    public function add_attendance(lesson_attendance $attendance) {
        if (in_array($attendance, $this->attendances, true)) {
            return false;
        }
        $this->attendances[] = $attendance;
    }

    /**
     * @param activity $activity
     * @return activity_grade|null
     */
    public function get_grade(activity $activity): ?activity_grade {
        foreach ($this->grades as $grade) {
            if ($grade->get_activity()->equal($activity)) {
                return $grade;
            }
        }
        return null;
    }

    /**
     * @param activity_grade $grade
     * @return false|void
     */
    public function add_grade(activity_grade $grade) {
        if (in_array($grade, $this->grades, true)) {
            return false;
        }
        $this->grades[] = $grade;
    }

    public function get_average(): int {
        $average = 0;
        $count = count($this->grades);
        $grade_amount = 0;
        /** @var activity_grade $grade */
        foreach ($this->grades as $grade){
            $grade_amount += $grade->get_grade();
        }
        if($grade_amount > 0 && $count > 0) {
            $average = $grade_amount / $count;
        }
        if($average > 100) {
            return 100;
        }
        return $average;
    }

    public function get_progress(): int {
        $total = $this->count_completions();
        $completed = 0;
        foreach ($this->get_course()->get_activities() as $activity) {
            if(!$activity->is_mandatory()) {
                continue;
            }
            $completion = $this->get_completion($activity);
            $completed += $completion->completed() ? 1 : 0;
        }
        return intval($completed / $total) * 100;
    }
    public function count_dedications(): int {
        return count($this->dedications);
    }

    public function get_connection_time(): int {
        $total = 0;
        foreach ($this->dedications as $dedication) {
            $total += $dedication->get_time();
        }
        foreach ($this->attendances as $attendance) {
            $lesson = $attendance->get_lesson();
            $total += $attendance->is_present() ? $lesson->get_duration() : 0;
        }
        return $total;
    }

    public function count_completions(): int {
        return count($this->completions);
    }

    public function __toObject(): object {
        $modules = array();
        /** @var module $module */
        foreach ($this->get_course()->get_modules() as $module){
            $modulo = $module->__toObject();
            $activities = array();
            /** @var activity $activity */
            foreach ($module->get_activities() as $activity) {
                if($activity->is_mandatory()){
                    /** @var activity_completion $completion */
                    foreach ($this->completions as $completion) {
                        if($completion->get_activity()->equal($activity)){
                            $actividad = $activity->__toObject();
                            $actividad->completed = $completion->completed() ? "Si" : "No";
                            $activities[] = $actividad;
                            break;
                        }
                    }
                }
            }
            $modulo->activities = $activities;
            $lessons = array();
            /** @var lesson $lesson */
            foreach ($module->get_lessons() as $lesson) {
                $clase = $lesson->__toObject();
                /** @var lesson_attendance $attendance */
                foreach ($this->attendances as $attendance) {
                    if($attendance->get_lesson()->equal($lesson)) {
                        $clase->present = $attendance->is_present() ? "Si" : "No";
                        $lessons[] = $clase;
                        break;
                    }
                }
            }
            $modulo->lessons = $lessons;
            $modules[] = $modulo;
        }
        return (object) [
            'id' => $this->get_id(),
            'name' => $this->get_name(),
            'rut' => $this->get_full_rut(),
            'role' => $this->get_role(),
            'active' => $this->is_active(),
            'progress' => $this->get_progress(),
            'time' => $this->get_connection_time(),
            'hours' => number_format($this->get_connection_time() / 60 / 60, 2),
            'state' => $this->get_state()->get_state(),
            'average' => intval($this->get_average()),
            'studying' => $this->get_state()->studying(),
            'reproved' => $this->get_state()->reproved(),
            'approved' => $this->get_state()->approved(),
            'completions' => $this->count_completions(),
            'modules' => $modules
        ];
    }

    /**
     * @return array
     */
    public function get_dedications(): array {
        return $this->dedications;
    }

    /**
     * @return array
     */
    public function get_completions(): array {
        return $this->completions;
    }

    /**
     * @return array
     */
    public function get_attendances(): array {
        return $this->attendances;
    }

    /**
     * @return array
     */
    public function get_grades(): array {
        return $this->grades;
    }



}