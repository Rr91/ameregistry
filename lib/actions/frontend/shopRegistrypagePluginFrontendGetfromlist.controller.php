<?php


class shopRegistrypagePluginFrontendGetfromlistController  extends waController
{
	public function execute()
	{
		$user_id = wa()->getUser()->getId();
		$option_ids = waRequest::post("option_ids");
		$option_values = waRequest::post("option_values");
		$product_id = waRequest::post("product_id");
		$count = waRequest::post("count");
		$sku_id = 0;
		$one_skus_in_prd = $this->oneSkusCheck($product_id);
		if($one_skus_in_prd){
			$sku_id = $one_skus_in_prd;
		}
		else{
			$option_true = array();
			foreach ($option_ids as $key => $value) {
				$option_true[$value] = $option_values[$key];
			}
			$sku_id = $this->getSkuByOptions($product_id, $option_true);
		}
		

		if($this->checkCnt($sku_id, $count)){	
			$html = "<span>You do not have an active registry.</span><br/><a href='/registrycreate' >Click here to create your registry.</a>";
			if($user_id){
				$model = new shopRegistrypagePluginModel();
				$registry = $model->getReestr($user_id);
				if(!empty($registry)){
					if(count($registry) <= 5){
						$html = "<h3>Please choose which registry to add this product to:</h3><table>";
						foreach ($registry as $key => $value) {
							$html .= "<tr><td>".$value["name_register"]."</td><td>".date("m-d-Y",strtotime($value["datetime"]))."</td><td><form class='add_product' action method='post'><input type='hidden' name='product' value='".$product_id."' /><input type='hidden' name='sku' value='".$sku_id."' /><input type='hidden' name='count' value='".$count."' /><input type='hidden' name='registry' value='".$value['register_id']."' /></form><button class='rigistry_link' onclick='add_uniq_product_fromlist(this);'>Select</button></td></tr>";
						}
						$html .= "</table>";
					}
					else{
						$html = "<p>Available limit - 5 registers are exhausted. You cannot create a new registry</p>";
					}
				}
			}
		}
		else{
			$html = "<p>In this number the product is not available</p>";
		}
		$arr[1] = $html;
		echo json_encode($arr);
		exit;
			
	}

	private function checkCnt($sku_id, $count){
		if(!isset($sku_id)) return false;
		$sku_model = new shopProductSkusModel();
		$sku = $sku_model->getById($sku_id);
		$true_count = $sku["count"];
		if(!isset($true_count)) return true;
		if($true_count < $count) return false;
		return true;
	}
	
	private function getSkuByOptions($product_id, $option_true){
		$model = new shopRegistrypagePluginModel();
		return $model->getSkuByOptionsJoinVariant($product_id, $option_true);
	}

	private function oneSkusCheck($product_id){
		$model = new shopProductSkusModel();
		$data = array("product_id" => $product_id);
		$rs = $model->getByField($data, true);
		if(count($rs) == 1) return $rs[0]["id"];
		return false;
	}
}