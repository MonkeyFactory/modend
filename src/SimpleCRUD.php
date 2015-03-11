<?php

class SimpleCRUD {
	function __construct($db, $auth, $tableName) {
		$this->db = $db;
		$this->auth = $auth;
		
		$this->tableName = $tableName;
		$this->validationFunction = null;
		
		$this->authLevels = array("create" => MODERATOR,
								  "update" => MODERATOR,
								  "delete" => MODERATOR,
								  "retrieve" => NOT_LOGGED_IN);
								  
		$this->columns = $this->BuildColumns();
	}
	
	function BuildColumns(){
		$columns = array();
		
		$rs = $this->db->query('SELECT * FROM ' . $this->tableName . ' LIMIT 0', PDO::FETCH_ASSOC);
		for ($i = 0; $i < $rs->columnCount(); $i++) {
			$col = $rs->getColumnMeta($i);
			
			$isKey = false;
			foreach($col["flags"] as $flag){
				if($flag == "primary_key"){
					$isKey = true;
					break;
				}
			}
			
			$columns[$col["name"]] = array("type" => $col["native_type"], "primary_key" => $isKey);
		}
		
		return $columns;
	}
	
	function GetColumnNamesWhereIsKey($isKey){
		$names = array();
		foreach($this->columns as $name => $info){
			if($info["primary_key"] == $isKey){
				$names[] = $name;
			}
		}
		
		return $names;
	}
	
	function BuildInputDataArray($columns, $input){
		$inputData = array();

		$inputMembers = get_object_vars($input);
		foreach($inputMembers as $member => $value){
			foreach($columns as $columnName){
				if(strtolower($columnName) == strtolower($member)){
					$inputData[$columnName] = $value;
					break;
				}
			}
		}
		
		return $inputData;
	}
	
	function ParameterizeKeys($dict){
		$retval = array();
		
		foreach(array_keys($dict) as $key){
			$retval[":" . $key] = $dict[$key];
		}
		
		return $retval;
	}
	
	function SetAuthLevel($task, $level){
		$this->authLevels[$task] = $level;
	}
	
	function RegisterValidationFunction($func){
		$this->validationFunction = $func;
	}
	
	function RegisterRoutes($parent, $route, $endpointName = ""){
		if($endpointName != "" && substr($endpointName,0, 1) != "/")
			$endpointName = "/" . $endpointName;
	
		//Register all routes
		$route->register($parent, "|^$endpointName\/$|", array($this, "OnCreate"), "POST");
		$route->register($parent, "|^$endpointName\/$|", array($this, "OnList"));
		$route->register($parent, "|^$endpointName\/(\d*)$|", array($this, "OnUpdate"), "POST");
		$route->register($parent, "|^$endpointName\/(\d*)$|", array($this, "OnDelete"), "DELETE");
		$route->register($parent, "|^$endpointName\/(\d*)$|", array($this, "OnRetrieive"));
	}
	
	function ValidateInput($input){
		if($this->validationFunction != null){
			call_user_func($this->validationFunction, $input);
		}
	}
	
	function OnList($input){
		AuthLevelOr403($this, $this->authLevels["retrieve"]);
	
		return $this->db->query("select * from " . $this->tableName, PDO::FETCH_ASSOC)->fetchAll();
	}
	
	function OnRetrieive($input, $id){
		AuthLevelOr403($this, $this->authLevels["retrieve"]);
	
		if(!isset($id) || $id == "")
			throw new InvalidInputDataException("ID argument is required");
	
		$keyColumn = $this->GetColumnNamesWhereIsKey(true)[0];
		$sth = $this->db->prepare("SELECT * FROM " . $this->tableName . " WHERE " . $keyColumn . " = ? LIMIT 1;");
		$sth->execute(array($id));

		if(!$data = $sth->fetch(PDO::FETCH_ASSOC))
			throw new NoSuchResourceException();
		else
			return $data;
	}
	
	function OnCreate($input){
		AuthLevelOr403($this, $this->authLevels["create"]);
	
		$this->ValidateInput($input);
	
		$normalColumns = $this->GetColumnNamesWhereIsKey(false);
		$inputData = $this->BuildInputDataArray($normalColumns, $input);
		
		$sql = "INSERT INTO " . $this->tableName . " (" . implode(",", array_keys($inputData)) . ") VALUES(" . implode(",", array_fill(0, count($inputData), "?")) . ");";

		$sth = $this->db->prepare($sql);
		if($sth->execute(array_values($inputData)) == 0)
			throw new Exception("Database update failed when adding! " . implode(",", $sth->errorInfo()));
			
		return $this->OnRetrieive(null, $this->db->lastInsertId());
	}
	
	function OnUpdate($input, $id){
		AuthLevelOr403($this, $this->authLevels["update"]);
	
		if(!isset($id) || $id == "")
			throw new InvalidInputDataException("ID argument is required");
			
		$this->ValidateInput($input);
	
		$normalColumns = $this->GetColumnNamesWhereIsKey(false);
		$inputData = $this->BuildInputDataArray($normalColumns, $input);
		
		
		$keyColumn = $this->GetColumnNamesWhereIsKey(true)[0];
		
		$sql = "UPDATE " . $this->tableName . " SET ";
		
		$keyValuePairs = array();
		foreach(array_keys($inputData) as $columnName){
			$keyValuePairs[] = "$columnName = :$columnName";
		}

		$inputData["id"] = $id;
		
		$sql .= implode(",", $keyValuePairs) . " WHERE " . $keyColumn . " = :id LIMIT 1;";

		$sth = $this->db->prepare($sql);
		if($sth->execute($this->ParameterizeKeys($inputData)) == 0){
			throw new Exception("Database update failed when updating! " . implode(",", $sth->errorInfo()));
		}
			
		return $this->OnRetrieive(null, $id);
	}
	
	function OnDelete($input, $id){
		AuthLevelOr403($this, $this->authLevels["delete"]);
	
		if(!isset($id) || $id == "")
			throw new InvalidInputDataException("ID argument is required");
			
		$keyColumn = $this->GetColumnNamesWhereIsKey(true)[0];
		$sth = $this->db->prepare("DELETE FROM " . $this->tableName . " WHERE " . $keyColumn . " = ? LIMIT 1;");
		if($sth->execute(array($id)) == 0)
			throw new Exception("Database update failed when deleting");

		return true;
	}
}