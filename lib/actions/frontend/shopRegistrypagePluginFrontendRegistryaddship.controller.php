<?php

class shopRegistrypagePluginFrontendRegistryaddshipController  extends waController
{
	public function execute()
    {

    	$flag = waRequest::post("rp");
    	$shop_cart_cokie = waRequest::cookie("shop_cart");
        $shop_cart_model = new shopCartItemsModel();
        $cart_id_arr = $shop_cart_model->getByField("code", $shop_cart_cokie, true);        
        $cart_model = new shopRegistrypagePluginCartModel();
    	$data = array(
    		"ship_flag" => $flag,
    	);
        foreach ($cart_id_arr as $key => $value) {
    	   $cart_model->updateById($value['id'], $data);
        }
    	echo json_encode(array());
    	exit;

    }
}