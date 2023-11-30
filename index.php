<?php
date_default_timezone_set('America/Bogota');

require_once "controller/userController.php";
require_once "controller/routesControllers.php";
require_once "controller/loginController.php";
require_once "controller/responseController.php";
require_once "model/userModel.php";

header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
header('Access-Control-Allow-Headers: Authorization');

$rutasArray = explode('/',$_SERVER['REQUEST_URI']);
$endPoint = (array_filter($rutasArray)[2]);


if($endPoint != 'login'){
    if(isset($_SERVER['PHP_AUTH_USER']) && ($_SERVER['PHP_AUTH_PW'])){
        $ok=false;
        $identifier = $_SERVER['PHP_AUTH_USER'];
        $key =  $_SERVER['PHP_AUTH_PW'];
        $users = UserModel::getUserAuth();
        foreach ($users as $u){
            if($identifier.":".$key == $u["us_identifier"].":". $u["us_key"]){
                $ok = true;
            }
        }
        if($ok){
            $routes = new RoutesController();
            $routes -> index();
        }
        else{
            ResponseController::response(505);
            return;
        }
    }
    else{
        ResponseController::response(503);
        return;
    }
}
else{
    $routes = new RoutesController();
    $routes->index();
}
?>