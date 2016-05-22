<?php

class DbHandler {

    private $conn;

    function __construct() {
        require_once dirname(__FILE__) . '/db_connect.php';
        $db = new DbConnect();
        $this->conn = $db->connect();
    }

	// All Places
	public function getAllUser() {
		$response = array();
		$stmt = $this->conn->prepare("SELECT user_id, nombres, apellidos, email, gcm_id FROM user");
		$stmt->execute();
		$result = $stmt->get_result();
		$stmt->close();
		$data = array();
		if($result->num_rows >0){
			while ($dataQuery = $result->fetch_assoc()) {
				$tmp = array();
				$tmp["user_id"] = $dataQuery['user_id'];
				$tmp["nombres"] = $dataQuery['nombres'];
				$tmp["apellidos"] = $dataQuery['apellidos'];
				$tmp["email"] = $dataQuery['email'];
				$tmp["gcm_id"] = $dataQuery['gcm_id'];
				array_push($data, $tmp);
			}
			$meta = array();
			$meta["status"] = "success";
			$meta["code"] = "200";
			$response["_meta"] = $meta;
			$response["data"] = $data;
		}else{
			$meta = array();
			$meta["status"] = "error";
			$meta["code"] = "100";
			$meta["message"] = "No existe información";
			$response["_meta"] = $meta;
		}
		return $response;
	}

    public function addUser($name, $lastname, $email, $gcm_id){

    	$response = array();

    	$statement = $this->conn->prepare("insert into user(nombres, apellidos, email, gcm_id) values (?,?,?,?)");
    	$statement->bind_param("ssss", $name, $lastname, $email, $gcm_id);
    	$result = $statement->execute();

    	if($result){
    		$meta = array();
    		$meta["status"] = "success";
    		$meta["code"] = "200";
    		$response["_meta"] = $meta;
    		$response["data"] = $this->getUserByGcm($gcm_id);
    	}else{
    		$meta = array();
    		$meta["status"] = "error";
    		$meta["code"] = "100";
    		$meta["message"] = "Error en el Servidor";
    		$response["_meta"] = $meta;
    	}

    	return $response;
    }

    public function getUserByGcm($gcm_id){
    	$response = array();
    	$statement = $this->conn->prepare(
    		"select user_id, nombres, apellidos, email, 
    		gcm_id from user where gcm_id = ?");
    	$statement->bind_param("s", $gcm_id);
    	$statement->execute();
    	$result = $statement->get_result();
    	$statement->close();

    	if($result->num_rows > 0){
    		$data = $result->fetch_assoc();
    		$response = $data;
    	}else{
    		$meta = array();
    		$meta["status"] = "error";
    		$meta["code"]= "100";
    		$meta["message"] = "No existe usuario";
    		$response["_meta"] = $meta;
    	}

    	return $response;

    }

}

?>
