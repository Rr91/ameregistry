<?php 

class shopRegistrypagePluginFrontendRegistryproductchangeController  extends waController
{
    public function execute(){
    	$reg_id = waRequest::post("reg_id");
    	$sku_id = waRequest::post("sku_id");
    	$new_count = waRequest::post("new_count");
		$user_id = wa()->getUser()->getId();
		$model = new shopRegistrypagePluginModel();
		$prd_model = new shopRegistrypagePluginProductModel();
		$arr = array();
		$prd_id = $model->getProductByRegistry($user_id, $reg_id, $sku_id);
		if(empty($prd_id)){
			$arr[1] = "You can't change the item from someone else's registry!";
			echo json_encode($arr);
			exit;
		}
		else{
			$sku_model = new shopProductSkusModel();
			$sku_result = $sku_model->getById($sku_id);
			if(!empty($sku_result)){
				$count = $sku_result['count'];
				if(!isset($count) || $count >= $new_count){
					$data = array(
						"count" => $new_count,
					);
					$prd_model->updateById($prd_id, $data);
					$arr[1] = 1;
					echo json_encode($arr);
					exit;
				}
			}
			$arr[1] = "Not in stock enough goods";
			echo json_encode($arr);
			exit;
		}
    }
}
