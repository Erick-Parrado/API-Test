<?php
    //require vs require_once vs include
    require_once('config.php');

    class Connection{
        static public function doConnection(){
            $con = false;
            //Error: falla fisica 
            //Execpcion: fallas del codigo en tiempo de ejecucion
            try{//BlockTryCatch
                $data = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8';
                $con = new PDO($data,DB_USER,DB_PASSWORD);
                return $con;
            }
            catch(PDOException $e){
                $message = array(
                    'COD'=>'000',
                    'MSN'=>($e)
                );

                echo($e->getMessage());
            }
        }
    }
?>