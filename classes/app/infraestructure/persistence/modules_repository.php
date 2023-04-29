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

use block_sic\app\application\contracts\imodules_repository;
use block_sic\app\utils\Arrays;
use stdClass;

class modules_repository implements imodules_repository {
    public function by_id(int $id): object {
        global $DB;
        $module = $DB->get_record('sic_modulos', ['id' => $id], '*', IGNORE_MISSING);
        $output = new stdClass();
        $output->id = $id;
        $output->code = strval($module->codigo);
        $output->startdate = intval($module->fecha_inicio);
        $output->enddate = intval($module->fecha_fin);
        $output->sync = intval($module->act_sincronas);
        $output->async = intval($module->act_asincronas);
        return $output;
    }
    public function related_to(int $courseid): array {
        global $DB;
        $list = Arrays::void();
        $modules = $DB->get_records('sic_modulos', ['course_id' => $courseid], 'id ASC', '*');
        foreach ($modules as $module) {
            $list[] = $this->by_id($module->id);
        }
        return $list;
    }
    public function attach_to(object $module, int $courseid) {
        global $DB;
        $table = 'sic_modulos';
        $object = new stdClass();
        $object->id = $module->id;
        $object->course_id = $courseid;
        $object->codigo = $module->code;
        $object->fecha_inicio = $module->startdate;
        $object->fecha_fin = $module->enddate;
        $object->act_sincronas = $module->sync;
        $object->act_asincronas = $module->async;

        if (empty(trim($module->code)) || $module->startdate <= 0 || $module->enddate <= 0
        || $module->sync <= 0 || $module->async <= 0) {
            return;
        }
        if ($DB->record_exists($table, ['id' => $module->id])) {
            $record = $DB->get_record($table, ['id' => $object->id], '*', 'MUST_EXISTS');
            $object->created = $record->created;
            $DB->update_record($table, $object);
        } else {
            unset($object->id);
            $object->created = time();
            $DB->insert_record($table, $object);
        }
    }
    public function dettach(int $moduleid) {
        global $DB;
        $table = 'sic_modulos';
        if ($DB->record_exists($table, ['id' => $moduleid])) {
            $DB->delete_records($table, ['id' => $moduleid]);
        }
    }

}
