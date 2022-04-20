<?php 

class shopRegistrypagePluginFrontendRegistryproductdeleteController  extends waController
{
    public function execute(){
    	$reg_id = waRequest::post("reg_id");
    	$sku_id = waRequest::post("sku_id");
		$user_id = wa()->getUser()->getId();
		$model = new shopRegistrypagePluginModel();
		$prd_model = new shopRegistrypagePluginProductModel();
		$arr = array();
		$prd_id = $model->getProductByRegistry($user_id, $reg_id, $sku_id);
		if(empty($prd_id)){
			$arr[1] = "You can't remove the item from someone else's registry!";
			echo json_encode($arr);
			exit;
		}
		else{
			$data = array(
				"status_product" => 2,
			);
			$prd_model->updateById($prd_id, $data);
			$arr[1] = 1;
			echo json_encode($arr);
			exit;
		}
    }
}
