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

use block_sic\app\application\contracts\iconfig_repository;

class configuration_repository implements iconfig_repository {


    /**
     * @throws \dml_exception
     */
    public function from_global(): object {
        global $DB;
        $table = 'config_plugins';
        $conditions = array(
            'plugin' => 'block_sic'
        );
        $records = $DB->get_records($table, $conditions, 'id ASC');
        $config = new \stdClass();
        foreach ($records as $record){
            $config->{$record->name} = $record->value;
        }
        return $config;
    }

    public function from_instance(int $instanceid): object {
        global $DB;
        $table = 'block_instances';
        $conditions = array(
            'id' => $instanceid,
            'blockname' => 'sic'
        );
        $record = $DB->get_record($table, $conditions);
        $config = new \stdClass();
        if($record){
            $config = unserialize(base64_decode($record->configdata));
        }
        return $config;
    }

    public function from_course(int $courseid): object {
        global $DB;
        $table = 'block_instances';
        $conditions = array(
            'blockname' => 'sic'
        );
        $records = $DB->get_records($table, $conditions, 'id ASC');
        $config = new \stdClass();
        foreach ($records as $record){
            $data = unserialize(base64_decode($record->configdata));
            if(intval($data->sic_courseid) == $courseid){
                return $data;
            }
        }
        return $config;
    }
}