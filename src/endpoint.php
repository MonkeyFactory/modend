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

$exHelper = new ExceptionHelper();

header("Content-type: application/json");

try{
	$response = $core->ProcessRequest($module, $query);	
	$oldOutput = ob_get_clean();
		
	echo $response;
}
catch(NoSuchEndpointException $ex){
	$exHelper->LogException($core->db, $ex);
	$oldOutput = ob_get_clean();
	
	echo $exHelper->FormatOutputException($ex);
	
	http_response_code(501);
}
catch(InvalidInputDataException $ex2){
	$exHelper->LogException($core->db, $ex2);
	$oldOutput = ob_get_clean();

	echo $exHelper->FormatOutputException($ex2);
	
	http_response_code(400);
}
catch(NoSuchResourceException $ex3) {
	http_response_code(404);
}
catch(NonInternalException $ex5){
	$exHelper->LogException($core->db, $ex5);
	$oldOutput = ob_get_clean();
	
	echo $exHelper->FormatOutputException($ex5);
		
	http_response_code(500);
}
catch(Exception $ex4)
{
	$exHelper->LogException($core->db, $ex4);
	$oldOutput = ob_get_clean();
	
	echo $exHelper->FormatOutputException($ex4, "Internal server error occurred");
		
	http_response_code(500);
}finally{
	if(isset($_REQUEST["debug"])){
		echo "\n" . $oldOutput;
	}
}
