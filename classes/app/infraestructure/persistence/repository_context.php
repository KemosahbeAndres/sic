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

namespace block_sic\app\infraestructure\persistence;

final class repository_context {
    public $courses;
    public $modules;
    public $sections;
    public $activities;
    public $lessons;

    public $dedications;
    public $completions;
    public $grades;
    public $attendances;

    public $students;
    public $teachers;
    /**
     * @var callable
     */
    public $moderators;
    public $managers;
    public $states;

    public function __construct(){
        $this->courses = new courses_repository();
        $this->modules = new modules_repository();
        $this->sections = new sections_repository();
        $this->activities = new activities_repository();
        $this->lessons = new lessons_repository();

        $this->dedications = new dedications_repository();
        $this->completions = new completion_repository();
        $this->grades = new grades_repository();
        $this->attendances = new attendances_repository();

        $users = new users_repository();
        $states = new states_repository();
        $this->states = $states;
        $roles = new roles_repository();

        $this->students = new students_repository($users, $states, $roles);
        $this->teachers = new teachers_repository($users, $roles);
        $this->managers = new managers_repository($users, $roles);
        $this->moderators = new moderators_repository($users, $roles);
    }
}