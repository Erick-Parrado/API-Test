<?php
require_once "Connection.php";
class  UserModel{
    static public function createUser($data){
        $cantMail = self::getMail($data["use_mail"]);
        if($cantMail<1){
            //var_export($data);
            $query = "INSERT INTO users(use_mail, use_pss, use_dateCreate, us_identifier, us_key, us_status) VALUES (:use_mail, :use_pss, :use_dateCreate, :us_identifier, :us_key, :us_status);";
            //echo $query;
            $status = "1";
            $statement= Connection::doConnection()->prepare($query);
            $statement->bindParam(":use_mail", $data["use_mail"],PDO::PARAM_STR);
            $statement->bindParam(":use_pss", $data["use_pss"],PDO::PARAM_STR);
            $statement->bindParam(":use_dateCreate", $data["use_dateCreate"],PDO::PARAM_STR);
            $statement->bindParam(":us_identifier", $data["us_identifier"],PDO::PARAM_STR);
            $statement->bindParam(":us_key", $data["us_key"],PDO::PARAM_STR);
            $statement->bindParam(":us_status", $status,PDO::PARAM_STR);
            $message = $statement->execute() ? "Ok" : Connection::doConnection()->errorInfo();
            $statement-> closeCursor();
            $statement = null;
            $query = "";
            }else{
                $message = "El usuario ya existe";
            }
            return $message;
    }
    static private function getMail($mail){
        $query = "SELECT use_mail FROM users WHERE use_mail='$mail'";
        $statement = Connection::doConnection()->prepare($query);
        $statement -> execute();
        $result = $statement -> rowCount();
        return $result;
    }
    static public function getUsers($parametro){
        $param = is_numeric($parametro) ? $parametro : 0;
        $query ="SELECT user_id, use_mail, use_dateCreate FROM users ";
        $query .= ($param > 0) ? "WHERE users.user_id = '$param' AND ": "";
        $query .= ($param > 0) ? "us_status='1';" : "WHERE us_status='1';";
       // echo $query;
        $statement = Connection::doConnection()->prepare($query);
        $statement -> execute();
        $result = $statement -> fetchAll(PDO::FETCH_ASSOC);
        return $result;

    }

    static public function updateUser($user_id,$data){
        $query = "UPDATE users SET ";
        $dataAO = new ArrayObject($data);
        $iter = $dataAO->getIterator();
        while($iter->valid()){
            $query .= $iter->key()."='".$iter->current()."'";
            $iter->next();
            if($iter->valid()){
                $query .= ",";
            }
            else{
                $query .= " WHERE user_id = '".$user_id."'";
            }
        }
        $statement= Connection::doConnection()->prepare($query);
        $message = $statement->execute() ? "Ok" : Connection::doConnection()->errorInfo();
        $statement-> closeCursor();
        $statement = null;
        $query = "";
        return $message;
    }

    static public function deleteUser($user_id){
        $query = "UPDATE users SET us_status = '0' WHERE user_id = ".$user_id;
        $statement= Connection::doConnection()->prepare($query);
        $message = $statement->execute() ? "Usuario ".$user_id." eliminado" : Connection::doConnection()->errorInfo();
        $statement-> closeCursor();
        $statement = null;
        $query = "";
        return $message;
    }

    static public function login($data){
        $user = $data['use_mail'];
        $pass = md5($data['use_pss']);

        if(!empty($user) && !empty($pass)){
            $query = "SELECT us_identifier, us_key FROM users WHERE
            use_mail='$user' and use_pss='$pass' and us_status='1'";
            $statement = Connection::doConnection()->prepare($query);
            $statement -> execute();
            $result = $statement -> fetchAll(PDO::FETCH_ASSOC);
            //var_dump("'$user'+'$pass'");
            return $result;
        }else{
            return "NO TIENE CREDENCIALES";
        }

    }
    static public function getUserAuth(){
        $query = "";
        $query = "SELECT us_identifier, us_key FROM users WHERE us_status = '1';";
        $statement = Connection::doConnection()->prepare($query);
        $statement -> execute();
        $result = $statement -> fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


}

?>