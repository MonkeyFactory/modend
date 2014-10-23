<?php

include "config.php";

function GetDatabaseConnection(){
	global $dbhost, $dbname, $dbuser, $dbpass;

	try{
		$dsn = "mysql:dbname=$dbname;host=$dbhost";
		return new PDO($dsn, $dbuser, $dbpass);
	}catch(PDOException $ex){
		http_response_code(500);
		die("Check database connection: " . $ex->getMessage());
	}
}