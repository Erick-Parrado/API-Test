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
        switch ($this->_method){
            case "GET":
                switch ($this->_complement){
                    case 0: 
                        $user = UserModel::getUsers(0);
                        $json = $user;
                        echo json_encode($json);
                        return;
                    default:
                    $user = UserModel::getUsers($this->_complement);
                    $json = $user;
                    echo json_encode($json);
                    return;
                }
            case "POST":
                $createUser = UserModel::createUser($this->_data);
                $json = array(
                    "response:"=>$createUser
                );
                echo json_encode($json, true);
                return;
            default:
            $json = array(
                "ruta:"=>"not found"
            );
            echo json_encode($json, true);
            return;
        }
    }
    private function generateSalting(){
        $trimmed_data="";
        if(($this->data !="") || (!empty($this->_data))){
            $trimmed_data = array_map("'trim,' $this->_data");
            $trimmed_data['use_pss'] = md5($trimmed_data['use_pss']);
            //salting
            $identifier = str_replace("$","y78",crypt($trimmed_data['use_mail'],'ser3478'));
            $key = str_replace("$","ERT",crypt($trimmed_data['use_pss'],'$uniempresarial2024'));
            $trimmed_data['us_identify']=$identifier;
            $trimmed_data['us_key']=$key;
            return $trimmed_data;
        }

    }
}

?>