<?php
require_once "Connection.php";

class  UserModel{
    static public function createUser($data){
        $cantMail = self::getMail($data["use_mail"]);
        $data['use_dateCreate'] = date('d/m/Y', time());
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
        $result = self::executeQuery($query)->rowCount();
        return $result;
    }
    static private function idExist($id){
        $query = 'SELECT user_id FROM users WHERE user_id = '.$id;
        return ((self::executeQuery($query)->rowCount())>0)?1:0;
    }
    static public function getUsers($parametro){
        $param = is_numeric($parametro) ? $parametro : 0;
        $query ="SELECT user_id, use_mail, use_dateCreate FROM users ";
        $query .= ($param > 0) ? "WHERE users.user_id = $param AND": "WHERE ";
        $query .=  "us_status='1';";
        return self::executeQuery($query);
    }

    static public function updateUser($user_id,$data){
        if(!self::idExist($user_id)){
            return 209;
        }
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
        self::executeQuery($query,$data);
        return 203;
    }

    static public function deleteUser($user_id){
        if(!self::idExist($user_id)){
            return 209;
        }
        $query = "UPDATE users SET us_status = '0' WHERE user_id = ".$user_id;
        self::executeQuery($query);
        return 204;
    }
    
    static public function activeUser($user_id){
        if(!self::idExist($user_id)){
            return 209;
        }
        $query = "UPDATE users SET us_status = '1' WHERE user_id = ".$user_id;
        self::executeQuery($query);
        return 205;
    }

    static public function login($data){
        $data['use_pss'] = md5($data['use_pss']);

        if(!empty($data['use_mail']) && !empty($data['use_pss'])){
            $query = "SELECT us_identifier, us_key FROM users WHERE use_mail=:use_mail and use_pss=:use_pss and us_status='1';";
            return self::executeQuery($query,$data,true);
        }else{
            ResponseController::response(504);
        }

    }
    static public function getUserAuth(){
        $query = "SELECT us_identifier, us_key FROM users WHERE us_status = '1';";
        $result = self::executeQuery($query,null,true);
        return $result;
    }

    static public function  executeQuery($query,$data=null,$fetch=false){
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
            if($fetch) return $statement->fetchAll(PDO::FETCH_ASSOC);
            return $statement;
        }
        else{
            $error = $statement->execute() ? false : Connection::doConnection()->errorInfo();
            if($error != false) return $error;
            $statement-> closeCursor();
            $statement = null;
            return $statement;
        }
    }
}
?>