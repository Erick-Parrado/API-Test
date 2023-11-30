<?php

class UserController{
    private $_method; //get,post, put
    private $_complement; //get user 1 o 2
    private $_data; //Datos a insertar o actualizar

    function __construct($method, $complement, $data){
        $this->_method = $method;
        $this -> _complement = $complement == null ? 0: $complement;
        $this -> _data = $data !=0 ? $data:"";
    }

    public function index(){
        if($this->validateData()){
            switch ($this->_method){
                case "GET":
                    switch ($this->_complement){
                        case 0: 
                            ResponseController::response(201,UserModel::getUsers(0));
                            return;
                        default:
                            ResponseController::response(201,UserModel::getUsers($this->_complement));
                            return;
                    }
                case "POST":
                    UserModel::createUser($this->generateSalting());
                    ResponseController::response(201);
                    return;
                case "PUT":
                    UserModel::updateUser($this->_complement,$this->generateSalting());
                    ResponseController::response(203);
                    return;
                case "DELETE":
                    UserModel::deleteUser($this->_complement);
                    ResponseController::response(204);
                    return;
                default:
                ResponseController::response(404);
            }
        }
    }

    private function validateData(){
        $patterns = array("use_mail"=>"/^[a-zA-Z0-9_.]{8,}@gmail.com$/","use_pss"=>"/^(?=.*[a-z]+)(?=.*[A-Z]+)(?=.*[0-9]+)(?=.*[!@#$%^&*(){}\\[\\]]+)[a-zA-Z0-9!@#$%^&*(){}\\[\\]]{8,}$/");
        $dataAO = new ArrayObject($this->_data);
        $iter = $dataAO -> getIterator();
        while($iter->valid()){
            $pattern = (isset($patterns[$iter->key()]))?$patterns[$iter->key()]:null;
            if(isset($pattern)){
                $result = preg_match($pattern,$iter->current());
                if(!$result) {
                    ResponseController::response(101);
                    return false;
                };
            }
            $iter->next();
        }
        return true;
    }


    private function generateSalting(){
        $trimmed_data="";
        if(($this->_data !="") || (!empty($this->_data))){
            $trimmed_data = array_map('trim', $this->_data);
            if(isset($this->_data['use_pss'])){
                $trimmed_data['use_pss'] = md5($trimmed_data['use_pss']);
                $key = str_replace("$","ERT",crypt($trimmed_data['use_pss'],'uniempresa$'));
                $trimmed_data['us_key']=$key;
            }
            if(isset($this->_data['use_mail'])){
                $identifier = str_replace("$","y78",crypt($trimmed_data['use_mail'],'$1$aserwtop$'));
                $trimmed_data['us_identifier']=$identifier;
            }
            return $trimmed_data;
        }
        return $this->_data;
    }
}

?>