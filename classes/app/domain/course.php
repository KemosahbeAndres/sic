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

class course {
    private $id;
    private $codigo;
    private $fechainicio;
    private $fechafin;
    private $modulos;
    private $mdlsections;

    public function __construct(int $id, string $code, int $startdate, int $enddate) {
        $this->id = $id;
        $this->codigo = $code;
        $this->fechainicio = $startdate;
        $this->fechafin = $enddate;
        //echo "INSIDE MODEL ## {$this->id} ## {$this->code} ## {$this->startdate} ## {$this->enddate} ## <br>";
        $this->modulos = Arrays::void();
        $this->mdlsections = Arrays::void();
    }

    public function get_id(): int {
        return $this->id;
    }

    public function get_code(): string {
        return $this->codigo;
    }

    public function get_startdate(): int {
        return $this->fechainicio;
    }

    public function get_enddate(): int {
        return $this->fechafin;
    }

    public function add_module(module $module) {
        $this->modulos[] = $module;
    }

    public function get_modules(): array {
        return $this->modulos;
    }

    public function get_mdl_sections(): array {
        return $this->mdlsections;
    }

    public function add_mdl_section(section $section) {
        $this->mdlsections[] = $section;
    }

    public function get_mdl_activities(): array {
        $activities = array();
        foreach ($this->get_mdl_sections() as $section) {
            foreach ($section->get_activities() as $activity) {
                $activities[] = $activity;
            }
        }
        return $activities;
    }

   public function get_activities(): array {
        $activities = array();
       /** @var module $module */
       foreach ($this->modulos as $module) {
           /** @var activity $activity */
           foreach ($module->get_activities() as $activity) {
                $activities[] = $activity;
            }
        }
        return $activities;
   }

   public function get_excluded_sections(): array {
        return array_filter($this->mdlsections, function (section $section) {
            return !$section->assigned();
        });
   }
}
