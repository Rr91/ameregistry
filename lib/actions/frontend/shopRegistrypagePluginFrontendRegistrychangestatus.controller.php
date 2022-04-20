<?php 

class shopRegistrypagePluginFrontendRegistrychangestatusController  extends waController
{
    public function execute(){
    	$answer = array();
    	$answer[1] = 1;
    	$user_id = wa()->getUser()->getId();
    	$register_id = waRequest::post("register_id");
    	$status = waRequest::post("status");
    	if(!$user_id || !$register_id || !$status){
    		$answer[1] = "Unknow Error";
    	}
    	else{
    		$model = new shopRegistrypagePluginModel();
    		if($model->isChangeUser($user_id, $register_id)){
    			$model->changeStatusRp($register_id, $status);
    		}
    		else{
    			$answer[1] = "This is not your registry";
    		}
    	}
  		echo json_encode($answer);
  		exit;
    }
}
