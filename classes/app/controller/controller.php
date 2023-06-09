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

use block_sic\app\domain\redirect_response;
use block_sic\app\domain\view_response;
use block_sic\app\infraestructure\persistence\repository_context;

class controller {
    protected $context;
    protected $content;

    protected function __construct(repository_context $context){
        $this->context = $context;
        $this->content = new \stdClass();
    }

    protected function response(string $viewname): view_response {
        $path = preg_split('/(\/)/', $viewname);
        $name = $viewname;
        if(is_array($path)){
            $name = strval($path[array_key_last($path)]);
        }
        $this->content->{trim($name)."page"} = true;
        return new view_response(trim($viewname), $this->content);
    }

    protected function redirect(string $action, object $params, string $message): redirect_response {
        return new redirect_response($action, $message, $params);
    }

}