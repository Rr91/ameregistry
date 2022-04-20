<?php 

class shopRegistrypagePluginBackendRpdeleteController extends waController{
	public function execute()
	{
		$register_id = waRequest::post("register_id");
		$model = new shopRegistrypagePluginModel();
		$model->deleteForseRP($register_id);
		echo json_encode(array("html"= hh));
		exit;
	}
}

