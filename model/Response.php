<?php

class Response{   
    private $_context;
    private $_response;

    public function __construct($context = '') {
        $this->_context = $context;
    }
    public function response($cod){
        switch($cod){
            case 101:
            case 404:
                break;
        }
    }

    private function _error(){
        
    }
}


/*
Route not found
Validation
Response
Confirmation (Update,Create,Delete)
Credentials warning
*/
?>