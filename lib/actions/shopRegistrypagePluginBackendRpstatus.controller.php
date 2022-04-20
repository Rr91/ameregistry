<?php 

class shopRegistrypagePluginBackendRpstatusController extends waController{
	public function execute()
	{
		$register_id = waRequest::post("register_id");
		$status = waRequest::post("status");
		$model = new shopRegistrypagePluginModel();
		$model->changeStatusRp($register_id, $status);
		echo json_encode(array("1"));
		exit;
	}
}