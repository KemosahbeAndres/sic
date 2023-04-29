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

use block_sic\app\application\contracts\igrades_repository;

class grades_repository implements igrades_repository {

    public function between(int $userid, object $activity): ?object {
        global $DB;
        $key = $activity->type;
        if ($activity->type == "lesson") {
            $key .= "id";
        }
        $table = $activity->type . "_grades";
        $instance = $activity->instance;
        if ($table != "lesson" || $table != "assign" || $table != "quiz") {
            return null;
        }
        $record = $DB->get_record($table, [$key => $instance, 'userid' => $userid], '*', IGNORE_MISSING);
        if (is_bool($record) && $record == false) {
            return null;
        }
        $output = new \stdClass();
        $output->grade = $record->grade;
        return $output;

    }

}
