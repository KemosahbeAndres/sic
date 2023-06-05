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

use block_sic\app\utils\Arrays;

class lesson {
    private $id;
    private $activity;
    //private $name;
    private $date;
    private $duration;

    /**
     * @param $id
     * @param $activity
     * @param $date
     * @param $duration
     */
    public function __construct(int $id, activity $activity, $date, $duration) {
        $this->id = $id;
        $this->activity = $activity;
        //$this->name = $name;
        $this->date = $date;
        $this->duration = $duration;
    }

    public function toObject(): object {
        return (object) [
            'id' => $this->get_id(),
            'name' => $this->get_name(),
            'date' => $this->get_date(),
            'duration' => $this->get_duration(),
            'activity' => $this->get_activity()->toObject()
        ];
    }

    public function equal(lesson $lesson): bool {
        return $lesson->get_id() == $this->get_id();
    }

    public function get_id(): int {
        return $this->id;
    }

    public function get_name(): string {
        return $this->activity->get_code();
    }

    public function get_date(): int {
        return $this->date;
    }

    public function get_duration(): int {
        return intval($this->duration);
    }

    public function present(): ?bool {
        return is_null($this->asistencia) ? false : $this->asistencia->present();
    }

    public function get_activity(): activity {
        return $this->activity;
    }

}
