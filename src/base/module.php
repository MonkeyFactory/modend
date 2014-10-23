<?php

abstract class Module {
		function __construct__($db) {
			$this->db = $db;
		}
		
		function RegisterRoutes($route){
				
		}
}