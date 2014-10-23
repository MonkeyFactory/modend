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

class InvalidInputData extends Exception {}