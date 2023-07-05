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

use block_sic\app\application\section_finder;
use block_sic\app\domain\request;
use block_sic\app\domain\response;
use block_sic\app\infraestructure\persistence\repository_context;

class section_controller extends controller {

    private $sectionFinder;

    public function __construct(repository_context $context) {
        parent::__construct($context);

        $this->sectionFinder = new section_finder($context);
    }
    public function details(request $request): response {
        $this->content->coursepage = true;
        try {
            $sid = intval($request->params->id);
            $this->content->section = $this->sectionFinder->execute($sid)->__toObject();
        } catch (\Exception $e) {
        }
        return $this->response('course/sections/details');
    }
}