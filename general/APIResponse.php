<?php 
    function APIResponse($response){
        $json = array(
            "response:"=>$response
        );
        /*foreach($extras as $ek => $ev){
            $json[$ek] = $ev;
        }*/
        echo json_encode($json, true);
    }
?>