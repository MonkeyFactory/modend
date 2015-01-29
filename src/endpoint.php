<?php 
ob_start();

include "core.php";

header("Access-Control-Allow-Origin: " . CORS_HEADER);

if($_SERVER["REQUEST_METHOD"] == "OPTIONS"){
	http_response_code(200);
	exit
}

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
		
	header("Content-type: application/json");
	echo $response;
	
	if(isset($debug))
		echo "\n" . $debug;
}
catch(NoSuchEndpointException $ex){
	if(isset($_REQUEST["debug"])){
		ob_end_flush();
		echo "<br />" . get_class($ex) . ": " . $ex->getMessage() . "<br />"; 
	}

	http_response_code(501);
	exit;
}
catch(InvalidInputDataException $ex2){
	if(isset($_REQUEST["debug"])){
		ob_end_flush();
		echo "<br />" . get_class($ex2) . ": " . $ex2->getMessage() . "<br />"; 
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