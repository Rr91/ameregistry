<?php


class shopRegistrypagePluginFrontendRegistrycreateregistryAction  extends shopFrontendAction
{
	public function execute()
	{
		$user_id = wa()->getUser()->getId();
		if($user_id){
			wa()->getResponse()->setTitle('Create Registry');
			wa()->getResponse()->addJs('js/registrypage.js', 'shop/plugins/registrypage');
			wa()->getResponse()->addCss('css/registrypage.css', 'shop/plugins/registrypage');
			$model = new shopRegistrypagePluginModel();
			$count_nact = 4;
			if(waRequest::param('registerid')) $count_nact = 5;
			if($model->checkCountRP($user_id, $count_nact)){	
				$change = waRequest::param('registerid');
				$change_user = null;
				$reg_data = null;
				$curr_day = 0;
				$curr_month = 0;
				$curr_year = 0;
				if($change){
					$change_user = $model->isChangeUser($user_id, $change);
					if($change_user){
						wa()->getResponse()->setTitle('Change Registry');
						$reg_data = $model->getChangeInfo($change_user);
						$curr_year = (int)substr($reg_data[0]["datetime"], 0, 4);
						$curr_month = (int)substr($reg_data[0]["datetime"], 5, 2);
						$curr_day = (int)substr($reg_data[0]["datetime"], 8, 2);
					}
				}

				$model_event = new shopRegistrypagePluginCelebratyModel();
				$events = $model_event->getAll();
				$region_model = new waRegionModel();
				$data = array(
					"country_iso3" => "usa",
				);
				$states = $region_model->getByField($data, true);
				$days = array();
				for ($i=1; $i < 32; $i++) { 
					$days[] = $i;
				}
				$months = array("Month","January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
				$years = array();
				for ($i=2017; $i < 2028; $i++) { 
					$years[$i] = $i;
				}

				$this->view->assign('regdata', $reg_data);		
				$this->view->assign('curr_month', $curr_month);		
				$this->view->assign('curr_year', $curr_year);		
				$this->view->assign('curr_day', $curr_day);		
				$this->view->assign('days', $days);		
				$this->view->assign('months', $months);		
				$this->view->assign('years', $years);		
				$this->view->assign('user_id', $user_id);		
				$this->view->assign('states', $states);		
				$this->view->assign('events', $events);		
				$this->view->assign('change', $change_user);		
			}
			else{
				$this->view->assign('big_count', true);	
			}
		}
		else{
			header("Location: /registrycreate/");
			exit;
		}
	}
}