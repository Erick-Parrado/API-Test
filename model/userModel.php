<?php
require_once "Connection.php";

class  UserModel{
    static public function createUser($data){
        $cantMail = self::getMail($data["use_mail"]);
        if($cantMail<1){
            $query = "INSERT INTO users(use_mail, use_pss, use_dateCreate, us_identifier, us_key, us_status) VALUES (:use_mail, :use_pss, :use_dateCreate, :us_identifier, :us_key, :us_status);";
            $data['us_status']=1;
            self::executeQuery($query,$data);
        }else{
            $message = "El usuario ya existe";
            return $message;
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
        $query ="SELECT user_id, use_mail, use_dateCreate FROM users ";
        $query .= ($param > 0) ? "WHERE users.user_id = '$param' AND ": "";
        $query .= ($param > 0) ? "us_status='1';" : "WHERE us_status='1';";
        return self::executeQuery($query);
    }

    static public function updateUser($user_id,$data){
        $query = "UPDATE users SET ";
        $dataAO = new ArrayObject($data);
        $iter = $dataAO->getIterator();
        while($iter->valid()){
            $query .= $iter->key()."=:".$iter->key();
            $iter->next();
            if($iter->valid()){
                $query .= ",";
            }
            else{
                $query .= " WHERE user_id = '".$user_id."'";
            }
        }
        return self::executeQuery($query,$data);
    }

    static public function deleteUser($user_id){
        $query = "UPDATE users SET us_status = '0' WHERE user_id = ".$user_id;
        $statement= Connection::doConnection()->prepare($query);
        return self::executeQuery($query);
    }

    static public function login($data){
        $user = $data['use_mail'];
        $pass = md5($data['use_pss']);
        $data['use_pss'] = $pass;

        if(!empty($user) && !empty($pass)){
            $query = "SELECT us_identifier, us_key FROM users WHERE use_mail=:use_mail and use_pss=:use_pss and us_status='1';";
            return self::executeQuery($query,$data);
        }else{
            ResponseController::response(504);
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

    static public function  executeQuery($query,$data=null){
        $fields = array("user_id","use_mail","use_pss","use_dateCreate","us_identifier","us_key","us_status");
        $statement= Connection::doConnection()->prepare($query);
        if(isset($data)){
            foreach(array_keys($fields) as $index){
                $pattern = '/^.*:'.$fields[$index].'.*$/';
                $result = (preg_match($pattern,$query));
    
                if(!$result) continue;
    
                switch($index){
                    case 0:
                        $statement->bindParam(":user_id", $data["user_id"],PDO::PARAM_STR);
                        break;
                    case 1:
                        $statement->bindParam(":use_mail", $data["use_mail"],PDO::PARAM_STR);
                        break;
                    case 2:
                        $statement->bindParam(":use_pss", $data["use_pss"],PDO::PARAM_STR);
                        break;
                    case 3:
                        $statement->bindParam(":use_dateCreate", $data["use_dateCreate"],PDO::PARAM_STR);
                        break;
                    case 4:
                        $statement->bindParam(":us_identifier", $data["us_identifier"],PDO::PARAM_STR);
                        break;
                    case 5:
                        $statement->bindParam(":us_key", $data["us_key"],PDO::PARAM_STR);
                        break;
                    case 6:
                        $statement->bindParam(":us_status", $data["us_status"],PDO::PARAM_STR);
                        break;
                }
            }
        }

        if(preg_match('/^SELECT.*$/',$query)){
            $statement -> execute();
            return $statement;
        }
        else{
            $message = $statement->execute() ? "Ok" : Connection::doConnection()->errorInfo();
            $statement-> closeCursor();
            $statement = null;
            return $message;
        }
        
    }
}

?>