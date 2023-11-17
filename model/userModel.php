<?php
require_once "Connection.php";
class  UserModel{
    static public function createUser($data){
        $cantMail = self::getMail($data["use_mail"]);
        if($cantMail==0){
            $query = "INSERT INTO users(user_id, use_mail, use_pss, use_dateCreate, us_identifier, us_key, us_status) VALUES (:user_id, :use_mail, :use_pss, :use_dateCreate, :us_identifier, :us_key, :us_status);";
            $status = "0";
            $statement = Connection::doConnection()->prepare($query);
            $statement -> bindParam(":user_id", $data["user_id"],PDO::PARAM_STR);
            $statement -> bindParam(":use_mail", $data["use_mail"],PDO::PARAM_STR);
            $statement -> bindParam(":use_pss", $data["use_pss"],PDO::PARAM_STR);
            $statement -> bindParam(":use_dateCreate", $data["use_dateCreate"],PDO::PARAM_STR);
            $statement -> bindParam(":us_identifier", $data["us_identifier"],PDO::PARAM_STR);
            $statement -> bindParam(":us_key", $data["us_key"],PDO::PARAM_STR);
            $statement -> bindParam(":us_status", $data["us_status"],PDO::PARAM_STR);
            $message = $statement->execute()?"Ok":Connection::doConnection()->errorInfo();
            $statement -> closeCursor();
            $statement = null;
            $query = "";
            return;
    }else{
        $message = "El usuario ya existe";
    }
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
        $query ="SELECT user_id, use_mail,use_dateCreate FROM users ";
        $query .= ($param > 0) ? "WHERE users.user_id = '$param' AND ": "";
        $query .= ($param > 0) ? "us_status='1';" : "WHERE us_status='1';";
        echo $query;
        $statement = Connection::doConnection()->prepare($query);
        $statement -> execute();
        $result = $statement -> fetchAll(PDO::FETCH_ASSOC);
        return $result;

    }
    static public function login($data){
        $user = $data['use_mail'];
        $pass = md5($data['use_pss']);

        if(!empty($user) && !empty($pass)){
            $query = "SELECT us_identifier, us_key, user_id FROM users WHERE
            use_mail='$user' and use_pss='$pass' and us_status='1'";
            $statement = Connection::doConnection()->prepare($query);
            $statement -> execute();
            $result = $statement -> fetchAll(PDO::FETCH_ASSOC);
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