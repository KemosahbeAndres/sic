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
use block_sic\app\domain\request;
use block_sic\app\domain\response;
use block_sic\app\infraestructure\persistence\repository_context;

class course_controller extends controller {
    private $courseLoader;
    public function __construct(repository_context $context) {
        parent::__construct($context);
        $this->courseLoader = new consult_course_controller($this->context);
    }

    public function index(request $request): response {
        $course = $this->courseLoader->execute($request->params->courseid);
        $this->content->course = $course->__toObject();
        //var_dump($this->content);
        return $this->response('course');
    }

    public function participants(request $request): response {
        return $this->response('participants');
    }

    public function sicpanel(request $request): response {
        return $this->response('sicpanel');
    }

}