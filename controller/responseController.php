<?php

class ResponseController{   

    static public $response=null;

    static public function response($cod,PDOStatement $statement=null){
        switch($cod){
            case 101://Validacion de correo
                self::setError($cod,'Los campos no cumplen con condiciones mínimas');
                break;
            case 201://Get Users
            case 202://Create User
            case 203://Update User
            case 204://Delete User
            case 404:
                self::setError($cod,'Ruta no encontrada');
                break;
            case 501:
                self::setInfo($cod,'OK');
                self::$response['credentials'] = $statement->fetchAll(PDO::FETCH_ASSOC);
                break;
            case 503://Error de credenciales
                self::setError($cod,'ERROR EN CREDENCIALES');
                self::$response['credentials'] = null;
                self::$response['header'] = "HTTP/1.1 400 FAIL";
                break;
            case 504://No credentials
                self::setError($cod,'NO TIENE CREDENCIALES');
                break;
        }
        echo json_encode(self::$response,JSON_UNESCAPED_UNICODE);
    }

    static private function setInfo($status,$message){
        self::$response['info']['status']=$status;
        self::$response['info']['message']=$message;
    }

    static private function setError($status,$message){
        self::$response['error']['status']=$status;
        self::$response['error']['message']=$message;
    }
}


/*
Route not found
Validation
Response
Confirmation (Update,Create,Delete)
Credentials warning
Error de conexion
*/
?>