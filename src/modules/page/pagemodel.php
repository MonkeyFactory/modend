<?php

class PageModel {
	function GetPageById($pageId) {
		return array("pagetitle" => "Page $pageId", "pagecontent" => "This is sample page content");
	}
}