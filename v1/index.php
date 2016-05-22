<?php

error_reporting(-1);
ini_set('display_errors', 'On');

require_once '../include/db_handler.php';
require '.././libs/Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

$app->get('/user/', function() use ($app) {
    
    $db = new DbHandler();
    $response = $db->getAllUser();

    printResponse(200, $response);
});

$app->post('/user/register', function() use ($app) {
    
    verifyParams(array('nombres','apellidos','email',
		'gcm_id'));

    $nombres = $app->request->post('nombres');
	$apellidos = $app->request->post('apellidos');
	$email = $app->request->post('email');
	$gcm_id = $app->request->post('gcm_id');

    $db = new DbHandler();
    $response = $db->addUser($nombres, 
		$apellidos, $email, $gcm_id);
    
    printResponse(200, $response);
});

function verifyParams($required_fields) {
    $error = false;
    $error_fields = "";
    $request_params = array();
    $request_params = $_REQUEST;

    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }

    if ($error) {
        $response = array();
        $app = \Slim\Slim::getInstance();

        $meta = array();
        $meta["status"] = "error";
        $meta["code"] = "9999";
        $meta["message"] = 'Campo requerido ' . substr($error_fields, 0, -2) . ', se encuentra vacio o nulo';
        $response["_meta"] = $meta;
        printResponse(400, $response);
        $app->stop();
    }
}

function printResponse($status_code, $response) {
    $app = \Slim\Slim::getInstance();

    $app->status($status_code);
    $app->contentType('application/json');

    echo json_encode($response);
}

$app->run();
?>