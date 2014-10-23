<?php

abstract class Module {
		function __construct($db) {
			$this->db = $db;
			
			$this->version = 0;
			$this->author = "";
		}
		
		function RegisterRoutes($route){
				
		}
}