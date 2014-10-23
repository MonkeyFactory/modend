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
catch(InvalidInputData $ex2){
	http_response_code(400);
}
catch(Exception $ex3)
{
	http_response_code(500);
	exit;
}