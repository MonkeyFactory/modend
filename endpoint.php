<?php 
ob_start();

include "core.php";

if(!isset($_GET["m"])){
	http_response_code(400);
	exit;
}

$module = $_GET["m"];
$query = isset($_GET["q"]) ? $_GET["q"] : "";

$core = new Core();
$core->BuildRoutes();

try{
	$response = $core->ProcessRequest($module, $query);
	
	if(!isset($_REQUEST["debug"]))
		ob_end_clean();
		
	header("Response-code: 200");
	header("Content-type: application/json");
	echo $response;
}
catch(NoSuchEndpointException $ex){
	http_response_code(401);
	echo '{"error":"' . $ex->getMessage() . '"}';
	exit;
}
catch(Exception $ex2)
{
	http_response_code(500);
	exit;
}