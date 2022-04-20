<?php


class shopRegistrypagePluginFrontendRegistrymanageAction  extends shopFrontendAction
{
	public function execute()
	{

		$user = wa()->getUser();
		$user_id = $user->getId();
		$model = new shopRegistrypagePluginModel();
		
		// Если зареган
		if($user_id){
			wa()->getResponse()->setTitle('Manage Registry');
			wa()->getResponse()->addCss('css/registrypage.css', 'shop/plugins/registrypage');
			wa()->getResponse()->addJs('js/registrypage.js', 'shop/plugins/registrypage');
			$registrys = $model->getReestr($user_id, true);
			$this->view->assign('registrys', $registrys);
		}
		
	}
}