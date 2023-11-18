<?php
require_once "general/APIResponse.php";
require_once "controller/loginController.php";

$rutasArray = explode("/",$_SERVER['REQUEST_URI']);
$inputs = array();
//Raw input for request
$inputs['raw_input'] = @file_get_contents('php://input');
$_POST = json_decode($inputs['raw_input'], true);
//var_dump($rutasArray);
if (count(array_filter($rutasArray))<2){
    APIResponse("Route not found");
}else{
    /**
     * EndPoint Correctos
     */
    $endPoint = (array_filter($rutasArray)[2]);
    $complement =  (array_key_exists(3,$rutasArray))?($rutasArray)[3]:0;
    $add  = (array_key_exists(4,$rutasArray))?($rutasArray)[4]:"";
    if($add !="") $complement .= "/".$add;
    $method = $_SERVER['REQUEST_METHOD'];
    switch($endPoint){
        case 'users':
            if(isset($_POST))
                $user = new  UserController($method, $complement, $_POST);
            else
                $user =  new UserController($method, $complement, 0);
            $user -> index();
            break;
        case 'login':
            if(isset($_POST) && $method=='POST'){
                $user = new LoginController($method, $_POST);
                $user -> index();
            }else{
                APIResponse("Route not found");
                return;
            }
            break;
        default:
        APIResponse("Route not found");
        return;
    }
}
?>