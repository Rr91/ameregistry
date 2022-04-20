<?php 

class shopRegistrypagePluginFrontendRegistrychangeController  extends waController
{
    public function execute(){

    	$reg_id = waRequest::post("reg_id");
		$user_id = wa()->getUser()->getId();
		$model = new shopRegistrypagePluginModel();
		$arr = array();
		$data = array(
			"register_id" => $reg_id,
			"customer_id" => $user_id,
		);
		$register = $model->getByField($data);
		if(empty($register)){
			$arr[1] = "You can't remove someone else's registry!";
			echo json_encode($arr);
			exit;
		}
		else{
			wa_dump($register);
			echo json_encode($arr);
			exit;
		}
    }
}
