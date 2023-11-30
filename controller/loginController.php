<?php
class LoginController{
    private $_method;
    private $_data;

    function __construct($method, $data){
        $this -> _method = $method;
        $this -> _data = ($data !=0 )? $data : "";
    }

    public function index(){
        switch($this -> _method){
            case 'POST':
                $credentials = UserModel::login($this->_data);
                if(!empty($credentials)){
                    ResponseController::response(501,$credentials);
                }else{
                    ResponseController::response(503);
                }
                break;
            default:
                ResponseController::response(404);
        }
    }
}
?>