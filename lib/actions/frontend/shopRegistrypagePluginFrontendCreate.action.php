<?php


class shopRegistrypagePluginFrontendCreateAction  extends shopFrontendAction
{
	public function execute()
	{
		$user_id = wa()->getUser()->getId();
		$notreg = true;
		$product_id = waRequest::post('pid');
		$this->view->assign('product_id', $product_id);
		
		if($user_id){
			header("Location: /registrycreateregistry/");
			exit;
		}
		wa()->getResponse()->setTitle('Create Registry Account');
		wa()->getResponse()->addJs('js/registrypage.js', 'shop/plugins/registrypage');
		wa()->getResponse()->addCss('css/registrypage.css', 'shop/plugins/registrypage');
		$this->view->assign('notreg', $notreg);	
	}
}