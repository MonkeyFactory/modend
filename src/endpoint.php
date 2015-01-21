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
	
	if(isset($_REQUEST["debug"])){
		$debug = ob_get_clean();
	}else{
		ob_end_clean();
	}
		
	header("Access-Control-Allow-Origin: " . CORS_HEADER);
	header("Content-type: application/json");
	echo $response;
	
	if(isset($debug))
		echo "\n" . $debug;
}
catch(NoSuchEndpointException $ex){
	http_response_code(501);
	echo '{"error":"' . $ex->getMessage() . '"}';
	exit;
}
catch(InvalidInputDataException $ex2){
	if(isset($_REQUEST["debug"])){
		echo "<br />" . get_class($ex4) . ": " . $ex4->getMessage() . "<br />"; 
	}

	http_response_code(400);
}
catch(NoSuchResourceException $ex3) {
	http_response_code(404);
}
catch(Exception $ex4)
{
	if(isset($_REQUEST["debug"])){
		ob_end_flush();
		echo "<br />" . get_class($ex4) . ": " . $ex4->getMessage() . "<br />"; 
	}
		
	http_response_code(500);
	exit;
}