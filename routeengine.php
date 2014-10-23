<?php

include "exceptions.php";

class RouteEngine {
	function __construct(){
		$this->routes = array();
	}

	function register($parent, $path, $function){
		$this->routes[] = array($parent, $path, $function);
	}
	
	function Invoke($module, $query){
		foreach($this->routes as $route){
			if(get_class($route[0]) == $module && preg_match($route[1], $query, $matches) !== false){
				//var_dump($route);
				return call_user_func_array($route[2], $matches);
			}
		}
		
		throw new NoSuchEndpointException("No registered route matched module " . $module . " and query " . $query);
	}
}