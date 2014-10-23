<?php

$name = "page";
$version = 1.0;
$author = "nojan";

include "base/module.php";
include "pagemodel.php";

class page extends Module {
	function getPage($pageId){
		$model = new PageModel();
		$page = $model->GetPageById($pageId);
		
		return $page;
	}

	function RegisterRoutes($route){
		$route->register($this, "/\/(\d*)/", array($this, "getPage"));
	}
} 