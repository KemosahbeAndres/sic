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

use block_sic\app\application\contracts\istates_repository;
use block_sic\app\application\contracts\iusers_repository;
use block_sic\app\domain\state;
use block_sic\app\domain\student;

class students_repository {
    private $states;
    private $users;
    public function __construct(iusers_repository $users, istates_repository $states) {
        $this->users = $users;
        $this->states = $states;
    }
    public function related_to(int $courseid): array {
        $students = array();
        $users = $this->users->students_of($courseid);
        foreach ($users as $u){
            //$role = $this->roles->between($u->id, $courseid);
            $student = new student($u->id, $u->name, $u->rut, $u->dv);
            $st = $this->states->between($student->get_id(), $courseid);
            $student->set_state(new state($st));
            $students[] = $student;
        }
        return $students;
    }

}