<?php 

class shopRegistrypagePluginBackendAction extends waViewAction {
	public function execute()
    {
        $this->setLayout(new shopRegistrypagePluginBackendLayout());
    	$register_id = waRequest::post("register_id");
    	if(!isset($register_id)) $register_id = waRequest::get("register_id");
    	$model = new shopRegistrypagePluginModel();
    	$event_model = new shopRegistrypagePluginCelebratyModel();
    	$is_filter = waRequest::post("filtr");
    	$where = array();
    	if($is_filter){
    		$login  =  waRequest::post("rp_login");
    		$email  =  waRequest::post("rp_email");
    		$name   =  waRequest::post("rp_name");
    		$event  =  waRequest::post("rp_event");
    		$status =  waRequest::post("rp_status");
    		$where = array("login" => $login, "email" => $email, "name" => $name, "event" => $event, "status" => $status);
    	}
   		$data_rp = $model->getAdminAllRP($where);

    	if(!(int)$register_id) {
    		$register_id = $data_rp[0]["register_id"];
    	}
    	$events = $event_model->getAll();

    	$main_rp = $this->selectedMailRp($data_rp, $register_id);
    	$this->view->assign('data_rp', $data_rp);
    	$this->view->assign('main_rp', $main_rp);
    	$this->view->assign('events', $events);
    }

    private function selectedMailRp($data_rp, $register_id){
    	foreach ($data_rp as $key => $value) {
    		if($value["register_id"] == $register_id)
    			return $value;
    	}
    }

}