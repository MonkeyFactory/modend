<?php

include "exceptions.php";

class RouteEngine {
	function __construct(){
		$this->routes = array();
	}

	function register($parent, $path, $function, $method = "GET"){
		$this->routes[] = array($parent, $path, $function, $method);
	}
	
	function Invoke($module, $query){
		$method = $_SERVER["REQUEST_METHOD"];
	
		foreach($this->routes as $route){
			if(get_class($route[0]) == $module && $route[3] == $method && preg_match($route[1], $query, $matches)){
				return call_user_func_array($route[2], array_slice($matches, 1));
			}
		}
		
		throw new NoSuchEndpointException("No registered route matched module '$module', method '$method' and query '$query'");
	}
}