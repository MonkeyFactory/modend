<?php

include "base/module.php";
include "pagemodel.php";

class page extends Module {
	function __construct($db){
		parent::__construct($db);
		
		$this->version = 1.1;
		$this->author = "nojan";
	}

	function RegisterRoutes($route){
		$route->register($this, "|^\/(\d*)$|", array($this, "getPage"));
	}
	
	function getPage($pageId){
		$model = new PageModel();
		$page = $model->GetPageById($pageId);
		
		return $page;
	}
} 