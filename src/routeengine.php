<?php

include "exceptions.php";

class RouteEngine {
	function __construct(){
		$this->routes = array();
	}

	function register($parent, $path, $function, $method = "GET"){
		$this->routes[] = array($parent, $path, $function, $method);
	}
	
	function Invoke($module, $query, $input){
		$method = $_SERVER["REQUEST_METHOD"];
	
		foreach($this->routes as $route){
			if(get_class($route[0]) == $module && $route[3] == $method && preg_match($route[1], $query, $matches)){
				$arguments = array_merge(array($input), array_slice($matches, 1));
				return call_user_func_array($route[2], $arguments);
			}
		}
		
		throw new NoSuchEndpointException("No registered route matched module '$module', method '$method' and query '$query'");
	}
}