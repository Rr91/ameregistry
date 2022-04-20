<?php


class shopRegistrypagePluginFrontendRegistryviewAction  extends shopFrontendAction
{
	public function execute()
	{
		$model = new shopRegistrypagePluginModel();
		$registrant_model = new shopRegistrypagePluginRegistrantModel();
		$celebraty_model = new shopRegistrypagePluginCelebratyModel();
		$registerid = waRequest::param('registerid');
		$registerid = (int)$registerid;
		if($registerid){
			wa()->getResponse()->setTitle('View Registry');
			wa()->getResponse()->addCss('css/registrypage.css', 'shop/plugins/registrypage');
			wa()->getResponse()->addJs('js/registrypage.js', 'shop/plugins/registrypage');
			$isregister = $model->getById($registerid);
			$name_register = $isregister["name_register"];
			$registrant_names = $registrant_model->getGetRegistrantNames($registerid);
			$registrant_names .= " - ".$celebraty_model->getNameCelebratyByTcId($isregister["type_celebraty"]);
			$reg_datetime = $isregister["datetime"]; 
			// $registrant_names .= $model->getTypeCelebraty($registerid);
			// $registrant_names .= $model->getTypeDatetimeByRpId($registerid);
			if(empty($isregister)){
				throw new waException("Product not found", 404);
			}
			$prd_register = $model->getProductsByRegisterid($registerid);
			$isempty = false;
			if(empty($prd_register)){
				$isempty = true;
			}
			$myregistry = false;
			$user_id = wa()->getUser()->getId();
			if($isregister["customer_id"] == $user_id){
				$myregistry = true;
			}
			$this->view->assign('name_register', $name_register);
			$this->view->assign('reg_datetime', $reg_datetime);
			$this->view->assign('registrant_names', $registrant_names);
			$this->view->assign('myregistry', $myregistry);
			$this->view->assign('isempty', $isempty);
			$this->view->assign('products', $prd_register);
			$this->view->assign('registerid', $registerid);

		}
		else{
			throw new waException("Product not found", 404);
		}
	}
}