<?php 
ob_start();

include "core.php";

header("Access-Control-Allow-Origin: " . CORS_HEADER);
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

if($_SERVER["REQUEST_METHOD"] == "OPTIONS"){
	http_response_code(200);
	exit;
}

if(!isset($_GET["m"])){
	http_response_code(400);
	exit;
}

$module = $_GET["m"];
$query = isset($_GET["q"]) ? $_GET["q"] : "";

$core = new Core();
$core->BuildRoutes();

header("Content-type: application/json");

try{
	$response = $core->ProcessRequest($module, $query);	
	$oldOutput = ob_get_clean();
		
	echo $response;
}
catch(NoSuchEndpointException $ex){
	LogException($core->db, $ex);
	$oldOutput = ob_get_clean();
	
	echo FormatOutputException($ex);
	
	http_response_code(501);
}
catch(InvalidInputDataException $ex2){
	LogException($core->db, $ex2);
	$oldOutput = ob_get_clean();

	echo FormatOutputException($ex2);
	
	http_response_code(400);
}
catch(NoSuchResourceException $ex3) {
	http_response_code(404);
}
catch(Exception $ex4)
{
	LogException($core->db, $ex);
	$oldOutput = ob_get_clean();
	
	echo FormatOutputException($ex4, "Internal server error occurred");
		
	http_response_code(500);
}finally{
	if(isset($_REQUEST["debug"])){
		echo "\n" . $oldOutput;
	}
}