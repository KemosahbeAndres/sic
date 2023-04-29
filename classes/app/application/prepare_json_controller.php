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

use block_sic\app\application\sic\activities_handler;
use block_sic\app\application\sic\course_handler;
use block_sic\app\application\sic\modules_handler;
use block_sic\app\application\sic\users_handler;

class prepare_json_controller {
    private $coursehandler;

    /**
     * @param consult_course_controller $courseloader
     * @param load_course_data_controller $userdataloader
     */
    public function __construct(consult_course_controller $courseloader, load_course_data_controller $userdataloader) {
        $this->coursehandler = new course_handler($courseloader);
        $this->coursehandler
            ->set_next(new users_handler($userdataloader))
            ->set_next(new modules_handler())
            ->set_next(new activities_handler());
    }

    public function execute(object $request): ?object {
        return $this->coursehandler->handle($request);
    }

}