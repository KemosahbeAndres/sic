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

namespace block_sic\app;

use block_sic\app\application\login_controller;
use block_sic\app\domain\request;
use block_sic\app\domain\response;
use block_sic\app\domain\route;
use block_sic\app\infraestructure\persistence\repository_context;
use block_sic\app\infraestructure\persistence\roles_repository;
use block_sic\app\infraestructure\persistence\users_repository;
use phpDocumentor\Reflection\Types\ClassString;

class SicApplication {
    public $request;
    protected $routes;
    protected $default;
    protected $context;

    public function __construct(){
        $this->request = new request();
        $this->routes = array();
        $this->default = new \stdClass();
        $this->context = new repository_context();
    }

    private function __init(login_controller $logged){
        global $USER;
        $this->request->cookies = filter_input_array(INPUT_COOKIE);
        $post = filter_input_array(INPUT_POST);
        $get = filter_input_array(INPUT_GET);
        if($post && $get){
            $this->request->params = (object) array_merge($get, $post);
        }elseif ($get){
            $this->request->params = (object) $get;
        }elseif ($post){
            $this->request->params = (object) $post;
        }
        $this->request->action = strval($this->request->params->action);
        $courseid = intval($this->request->params->courseid);
        $this->request->user = $logged->execute($USER->id, $courseid);
    }

    public function get(string $action, string $controller, string $function) {
        $this->routes[] = new route('get', $action, new $controller($this->context), $function);
    }

    public function post(string $action, string $controller, string $function) {
        $this->routes[] = new route('post', $action, new $controller($this->context), $function);
    }

    public function default(string $action, string $controller, string $function){
        if($this->default instanceof route) return;
        $this->default = new route('get', $action, new $controller($this->context), $function);
        $this->routes[] = $this->default;
    }
    public function run() {
        $this->__init(new login_controller(
            new users_repository(),
            new roles_repository()
        ));
        /** @var route $route */
        foreach ($this->routes as $route) {
            if($route->action == $this->request->action){
                $response = call_user_func([$route->controller, $route->callback], $this->request);
                $this->registerRoutes($response);
                $response->content->manager = $this->request->user->get_role() == "manager";
                echo $response->render();
                return;
                break;
            }
        }
        /** @var response $response */
        $response = call_user_func([$this->default->controller, $this->default->callback], $this->request);
        $this->registerRoutes($response);
        $response->content->manager = $this->request->user->get_role() == "manager";
        echo $response->render();
    }

    private function registerRoutes(response $response){
        /** @var route $route */
        foreach ($this->routes as $route){
            $response->registerRoute($route, $this->request);
        }
    }

}