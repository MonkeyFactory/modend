<?php

include "config.php";

function GetDatabaseConnection(){
	try{
		$dsn = DBPROVIDER . ":dbname=" . DBNAME . ";host=" . DBHOST;
		return new PDO($dsn, DBUSER, DBPASS);
	}catch(PDOException $ex){
		http_response_code(500);
		die("Check database connection: " . $ex->getMessage());
	}
}