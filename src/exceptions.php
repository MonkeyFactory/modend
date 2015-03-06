<?php

class NoSuchEndpointException extends Exception {}

class ModuleVersionMismatchException extends Exception {
	function __construct($msg, $moduleVersion, $expectedVersion){
		parent::__construct($msg);
		
		$this->moduleVersion = $moduleVersion;
		$this->expectedVersion = $expectedVersion;
	}
}

class ModuleNotFoundException extends Exception {}

class ModuleHasNoSetupException extends Exception {}

class InvalidInputDataException extends Exception {}

class NoSuchResourceException extends Exception {}

class NonInternalException extends Exception {}

function LogException($db, $ex){
	$rawInput = file_get_contents("php://input");
	
	$sth = $db->prepare("insert into exceptions (exceptionType, exceptionMessage, inputData) values(?,?,?)");
	$sth->execute(array(get_class($ex), $ex->getMessage(), $rawInput));
}

function OutputFormatException($ex, $messageOverride = ""){
	return json_encode(array("type" => get_class($ex),
							 "message" =>  (($messageOverride == "") ? $ex->getMessage() : $messageOverride)));
}