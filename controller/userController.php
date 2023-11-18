<?php
require_once "general/APIResponse.php";

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
        if($this->validateData()!=true){
            switch ($this->_method){
                case "GET":
                    switch ($this->_complement){
                        case 0: 
                            APIResponse(UserModel::getUsers(0));
                            return;
                        default:
                            APIResponse(UserModel::getUsers($this->_complement));
                            return;
                    }
                case "POST":
                    APIResponse(UserModel::createUser($this->generateSalting()));
                    return;
                case "PUT":
                    APIResponse(UserModel::updateUser($this->_complement,$this->generateSalting()));
                    return;
                case "DELETE":
                    APIResponse(UserModel::deleteUser($this->_complement));
                    return;
                default:
                APIResponse("Route not found");
            }
        }
        else APIResponse($this->validateData());
    }

    private function validateData(){
        $patterns = array("use_mail"=>"/^[a-zA-Z0-9_.]{8,}@gmail.com$/","use_pss"=>"/^(?=.*[a-z]+)(?=.*[A-Z]+)(?=.*[0-9]+)(?=.*[!@#$%^&*(){}\\[\\]]+)[a-zA-Z0-9!@#$%^&*(){}\\[\\]]{8,}$/");
        $dataAO = new ArrayObject($this->_data);
        $iter = $dataAO -> getIterator();
        while($iter->valid()){
            $pattern = $patterns[$iter->key()];
            if(isset($pattern)){
                $result = preg_match($pattern,$iter->current());
                if(!$result) return "Error en ".$iter->key();
            }
            $iter->next();
        }
        return;
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