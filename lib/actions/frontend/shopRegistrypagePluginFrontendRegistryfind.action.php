<?php


class shopRegistrypagePluginFrontendRegistryfindAction  extends shopFrontendAction
{
	public function execute()
	{
		wa()->getResponse()->setTitle('Find Registry');
		wa()->getResponse()->addCss('css/registrypage.css', 'shop/plugins/registrypage');
		wa()->getResponse()->addJs('js/registrypage.js', 'shop/plugins/registrypage');
		if(waRequest::post("ajax")){
			$model = new shopRegistrypagePluginModel();
			$firstname = waRequest::post("firstname");
			$lastname = waRequest::post("lastname");
			$place = waRequest::post("place");
			$mouth = waRequest::post("mouth");
			$year = waRequest::post("year");
			$model = new shopRegistrypagePluginModel();
			$registry = $model->getReestryFind($firstname, $lastname, $place, $mouth, $year);
			$arr = array();
			$html = "<div class=\"div_form\" style=\"width: 98%;min-height: 0px;\">";
			if(count($registry)){
				$html .= "<table border='1' class='table_view' style='margin-bottom: 0px;'>";
				foreach ($registry as $reg) {
					$html .= "<tr>
					<td><a href='/registryview/".$reg['register_id']."/' class='rigistry_link'>".$reg['title']." - ".$reg['name_celebraty']." - ".date("m-d-Y", strtotime($reg['datetime']))."</a></td>
					</tr>";
				}
				$html .= "</table>";
			}
			else{
				$html .= "<p>Nothing found</p>";
			}
			$html .= "</div>";
			$arr[1] = $html;
			echo json_encode($arr);
			exit;
		}
		$state_model = new waRegionModel();
		$data_state = array(
			"country_iso3" => "usa",
		);
		$states = $state_model->getByField($data_state, true);
		$this->view->assign('states', $states);	
	}
}