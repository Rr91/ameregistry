<?php

class shopRegistrypagePluginFrontendRegistryloginAction  extends waLoginAction
{
    public function execute()
    {
        $answer = array(); 
        try {
           	$result = parent::execute();
        } catch (waException $e) {
           $result = null;
        }
        if($result){
        	$answer[1] = "Success";
        }else{
        	$answer[1] = "Error";
        }
        echo json_encode($answer);
		exit;
    }

    
}