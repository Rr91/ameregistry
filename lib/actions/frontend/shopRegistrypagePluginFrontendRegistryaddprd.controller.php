<?php

class shopRegistrypagePluginFrontendRegistryaddprdController  extends waController
{
    public function execute()
    {
        $product_id = (int)waRequest::post('product');
        $registry_id = (int)waRequest::post('registry');
        $sku_id = (int)waRequest::post('sku');
        $count = (int)waRequest::post('count');

        $model = new shopRegistrypagePluginModel();
        $product_model = new shopProductModel();
        $prd_model = new shopRegistrypagePluginProductModel();
        $skus_model = new shopProductSkusModel();
        
        $answer = array();
        if($product_id == "" || $registry_id == ""){
        	$answer[1] = "Unknown error";
        }
        else{
        	$issetregistry = $model->getById($registry_id);
        	if(empty($issetregistry)){
        		$answer[1] = "The registry does not exist";
        	}
        	else{
        		$issetproduct = $product_model->getById($product_id);
        		if(empty($issetproduct)){
        			$answer[1] = "The product does not exist";
        		}
        		else{
                    $count_prd_in_reg = $prd_model->countByRegistry($registry_id);
                    if($count_prd_in_reg >= 20){
                        $answer[1] = 'You have reached the limit of 20 items in this registry. <a href="/registryview/'.$registry_id.'/">Click here to edit the items in this registry</a> or <a href="/registrycreate/">click here to create a new registry</a>';
                    }
                    else{
        				$data = array(
        					"sku_id" => $sku_id,
        					"register_id" => $registry_id,
        				);
            			
            			$arrprd = $prd_model->getByField($data);
            			$count_old = $arrprd["count"];
            			$count_buy = $arrprd["count_buy"];
            			if(!isset($count_buy)) $count_buy = 0;
                        
                        $count_new = $count + $count_old;
                        if(isset($arrprd["status_product"]) && $arrprd["status_product"] == 2){
            			    $count_new = $count;   
                            $count_buy = 0;
                        }
            			
                        $skus_data = $skus_model->getById($sku_id);
                        $name_prd_skus = $issetproduct['name']."(".$skus_data["name"].") quantity - $count ";


                        $data = array(
            				"sku_id" => $sku_id,
            				"product_id" => $product_id,
            				"register_id" => $registry_id,
            				"count" => $count_new,
            				"count_buy" => $count_buy,
            			);

            			if(isset($arrprd["register_prd_id"])){
            				$data["register_prd_id"] = $arrprd["register_prd_id"];
                            $data["status_product"] = 1;
            			}
            			$prd_model->insert($data, 1);
            			$answer[1] = $name_prd_skus.' successfully added to your registry <br/><a href="/registryview/'.$registry_id.'/">Click here to go to your registry</a>';
                    }
        		}
        	}
        }
        echo json_encode($answer);
		exit;

    }
    
}