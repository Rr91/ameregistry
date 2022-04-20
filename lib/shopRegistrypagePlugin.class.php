<?php

class shopRegistrypagePlugin extends shopPlugin
{
    public function registryPageNav(){
        $view_helper = new waViewHelper(wa()->getView());
        return '<a href="/registrymanage/">'._wp('My registry').'</a>';
    }
	public function registryNav(){
    	$user = wa()->getUser();
        $html = "<ul class=\"menu-v category-tree\">";
        if($user->getId()){
            $html .= '<li><a href="/registrymanage/">Manage My Registry</a></li>';
        }
    	$html .= '
    			<li><a href="/registryfind/">Find a Registry</a></li>
    			<li><a href="/registrycreate/">Create a Registry</a></li>
    			';
    	if(!$user->getId()){
    		$html .= '
    			<li><a href="/login/">Registry Sign In</a></li>';
    	}
        $html .= "</ul>";
    	return $html;
    }
    public function registryButton($product){
        wa()->getResponse()->addJs('js/jquery.arcticmodal-0.3.min.js', 'shop/plugins/registrypage');
        wa()->getResponse()->addCss('css/jquery.arcticmodal-0.3.css', 'shop/plugins/registrypage');
        wa()->getResponse()->addCss('css/simple.css', 'shop/plugins/registrypage');
        
        
        wa()->getResponse()->addJs('js/registrypage.js', 'shop/plugins/registrypage');
        wa()->getResponse()->addCss('css/registrypage.css', 'shop/plugins/registrypage');
        
        $product_id = $product->data["id"];
        return array(
            'cart'      => '<button id="addtoregistrysku" data_prd = '.$product["id"].'>Add To Registry</button><div style="display: none;"><div class="box-modal" id="exampleModal"><div class="box-modal_close arcticmodal-close">close</div><div id="exampleModalInfo"></div></div><div>',
        );
    }
    public function registryCartadd($params){
        $model = new shopRegistrypagePluginModel();
        $regcart_model = new shopRegistrypagePluginCartModel();
        $register_id = waRequest::post('registr');
        $issetreg = $model->getById($register_id);
      
        if($register_id && $issetreg["register_id"]){
            $data = array(
                "register_id" => $register_id,
                "cart_id" => $params["id"],
                "contact_id" => $params["contact_id"],
                "sku_id" => $params["sku_id"],
            );
            $regcart_model->insert($data);
        }
    }
    public function registryCartdelete($params){
        $regcart_model = new shopRegistrypagePluginCartModel();
        $regcart_model->deleteById($params["id"]);
    }
    public function registryOrdercreate($params){
        $shop_cart_cokie = waRequest::cookie("shop_cart");
        $shop_cart_model = new shopCartItemsModel();
        $cart_id_arr = $shop_cart_model->getByField("code", $shop_cart_cokie, true);
        $cart_ids =array();
        foreach ($cart_id_arr as $key => $value) {
            $cart_ids[] = $value['id'];
        }

        $items_model = new shopOrderItemsModel();
        $regcart_model = new shopRegistrypagePluginCartModel();
        $prd_model = new shopRegistrypagePluginProductModel();

        $order_id = $params["order_id"];
        $contact_id = wa()->getUser()->getId();
        $data = array(
            "order_id" => $order_id,
        );
        $items = $items_model->getByField($data, true);
        $arr_mail_prd = array();
        foreach ($items as $key => $item) {
            $fields = array(
                "sku_id" => $item["sku_id"],
                "order_id" => NULL,
                "cart_id" =>$cart_ids[$key],
            );
            $values = array(
                "order_id" => $item["order_id"],
                "item_id" => $item["id"],
                "contact_id" => $contact_id,
            );
            $regcart_model->updateByField($fields, $values);
            $data_prd = array(
                "sku_id" => $item["sku_id"],
                "contact_id" => $contact_id,
                "order_id" => $item["order_id"],
                "item_id" => $item["id"],
            );
            $register_cart = $regcart_model->getByField($data_prd);
            $register_id = $register_cart["register_id"];
            if($register_id){
            	$arr_mail_prd[$register_id][$key] = $item;
	            $data_p = array(
	                "register_id" => $register_id,
	                "sku_id" => $item["sku_id"],
	            );
	            $prd_data = $prd_model->getByField($data_p);
	            $prd_id = $prd_data["register_prd_id"];

	            $new_count = $prd_data["count"] - $item["quantity"];
	            $new_count_buy = $prd_data["count_buy"] + $item["quantity"];
	           
	            $data_cnt = array(
	                "count" => $new_count,
	                "count_buy" => $new_count_buy,
	            );
	            $prd_model->updateById($prd_id, $data_cnt);
            }
        }
        if(!empty($arr_mail_prd)){
        	$this->getMailBuyRp($arr_mail_prd, $order_id);
        }
    }
    public function getTemplateMail($content, $params){
        $view = wa()->getView();
        $abs_path = $_SERVER['DOCUMENT_ROOT'];       
        $rel_path = "/mailx.html";   
        $mail_path = $abs_path.$rel_path;
        file_put_contents($mail_path, $content);
        
        foreach ($params as $key => $value) {
            $view->assign("$key", $value);
        }
        
        $mail_text = $view->fetch($mail_path);
        unlink($mail_path);
        return $mail_text;
    }
    public function getTemplateMailA($content, $params){
        $view = wa()->getView();
        $abs_path = $_SERVER['DOCUMENT_ROOT'];   
        $rel_path = "/mailxa.html";
        $mail_path = $abs_path.$rel_path;
        file_put_contents($mail_path, $content);
        
        foreach ($params as $key => $value) {
            $view->assign("$key", $value);
        }
        
        $mail_text = $view->fetch($mail_path);
        unlink($mail_path);
        return $mail_text;
    }

    public function getMailBuyRp($data, $order_id){
    	$model = new shopRegistrypagePluginModel();
    	foreach ($data as $register_id => $items) {	
    		$model->sendMailCreateRP($register_id, 8, $items);
    		$model->sendMailCreateRPA($register_id, 9, $items);
    	}
    }
    public function registryBackendMenu(){
        wa()->getResponse()->addCss('css/registrypage.css', 'shop/plugins/registrypage');
        wa()->getResponse()->addJs('js/registrypage.js', 'shop/plugins/registrypage');
        $html = '<li ' . (waRequest::get('plugin') == $this->id ? 'class="selected"' : 'class="no-tab"') . '>
                        <a href="?plugin=registrypage">Модерация точек</a>
                    </li>';

        return array('core_li' => $html);
    }

    public function registryBackendOrder($params){
        $regcart_model = new shopRegistrypagePluginCartModel();
        $order_id = $params["id"];
        $items = $regcart_model->getInfo($order_id);
        $title_suffix = "";
        $info_section = "";
        if(!empty($items)){
            $flag = false;
            $title_suffix = " <span  style='color:#F60;'>RP</span>";
            $info_section .= "<h3>Goods from the register/s</h3>";
            $info_section .= "<table class='zebra'><thead><tr><td>Registry</td><td>Product</td></tr></thead>";
            foreach ($items as $key => $item) {
                if($item["ship_flag"] == 1) $flag = true;
                $info_section .= "<tr>";
                $info_section .= "<td><a href='?plugin=registrypage&register_id=".$item["register_id"]."'>#".$item["register_id"]." ".$item["name_register"]."</a></td>";
                $info_section .= "<td><a href='?action=products#/product/".$item["prdid"]."/'>".$item["prdname"]."(".$item["skuname"].")</a><br/><span class='hint'>".$item["sku"]."</span></td>";
                                        
                $info_section .= "</tr>";
            }
            if($flag){
                $info_section .= "<tr><td colspan = 2><i class='icon16 ss sent'></i><span style='color:red;'>Ship to the Registry Owner</span></td></tr>";
            }
            else{
                $info_section .= "<tr><td colspan = 2><i class='icon16 ss sent'></i><span>Shipping to myself</span></td></tr>";
            }
            $info_section .= "</table>";
        }

        return array(
            'title_suffix'  => $title_suffix,
            'info_section'  => '<p>'.$info_section.'</p>',
        );
    }

}
