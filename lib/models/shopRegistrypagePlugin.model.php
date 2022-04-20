<?php
class shopRegistrypagePluginModel extends waModel
{
    protected $table = 'shop_register';
    protected $id = 'register_id';

    public function getReestr($user_id){
        $where = "WHERE sr.customer_id ='".$user_id."' AND srd.`type_registrant`= 1 AND sr.status != 2";
        $query = $this->getRegistryQuery($where);
        $rs = $this->query($query);
        return $rs->fetchAll();
    }
    public function isChangeUser($user_id, $register_id){
        $data = array(
            "customer_id" => $user_id,
            "register_id" => $register_id,
        );
        $register = $this->getByField($data);
        return $register["register_id"];
    }
    public function getChangeInfo($register_id){
        $query = "  SELECT * FROM `shop_register` AS `sr`
                    LEFT JOIN `shop_registrant_data` AS `srd`
                    ON `sr`.`register_id` = `srd`.`register_id`
                    WHERE `sr`.`register_id` = '".$register_id."' ORDER BY `srd`.`type_registrant`";

        $rs = $this->query($query)->fetchAll();
        return $rs;
    }
    private function getRegistryQuery($where, $group = ""){
        $query = 'SELECT * FROM shop_register AS sr
                    LEFT JOIN  `shop_register_status` AS srs
                    ON srs.id = sr.status
                    LEFT JOIN `shop_registry_celebraty` AS src
                    ON src.id = sr.type_celebraty
                    LEFT JOIN `shop_registrant_data` AS srd
                    ON srd.register_id = sr.register_id
                     '.$where.$group.' ORDER BY sr.register_date_created DESC';
                     // wa_dump($query);
        return $query;
    }
    public function addRegister($data, $array_table, $array_compare){
        $dataval = array();
        foreach ($data as $key => $value) {
            foreach ($array_table as $ke => $fields) {
                foreach ($fields as $k => $field) {
                    if(in_array($key, $field)){
                        $dataval[$ke][$k][$key] = $value;
                    }
                }
            }
        }
        $data_ins = array();
        foreach ($dataval as $key => $value) {
            foreach ($value as $ke => $valu) {
                foreach ($valu as $k => $val) {
                    $t = $this->arrayCompare($k, $array_compare);
                    $data_ins[$key][$ke][$t] = $val;
                }
            }
        }
        $this->insertRegistry($data_ins);
    }
    public function insertRegistry($data_ins){
        foreach ($data_ins as $key => $value) {
            if($key == "shop_register"){
                $data_reg = array();
                foreach ($value[0] as $k => $val) {
                    $data_reg[$k] = $val;
                }
            }
            else{
                $data_registrant = array();
                foreach ($value as $k => $val) {
                    foreach ($val as $ke => $v) {
                        $data_registrant[$k][$ke] = $v;
                    }
                }
            }
        }
        $this->insert($data_reg);
        $mod = new shopRegistrypagePluginRegistrantModel();
        foreach ($data_registrant as $key => $value) {
            $mod->insert($value);
        }
    } 
    public function addRegisterAndRegistrant($insert_data){
        $event = $insert_data["shop_register"][0];
        $register_id = $this->addEvent($event);
        $reg_model = new shopRegistrypagePluginRegistrantModel();
        $error = $reg_model->addResitrantsByRp($register_id, $insert_data["shop_registrant_data"]);
        if($error === 3){
            return array("Registrant data error", null);
        }
        return array($error, $register_id);
    }
    public function addEvent($event){
        if(!in_array("register_date_created", array_keys($event))){
            $date = date('Y-m-d H:i:s');
            $event["register_date_created"] = $date;
        }
        $this->insert($event);
        $result = $this->getInsertId("shop_register", "register_id");
        $this->changeStatusRp($result, 1);
        return $result;
    }
    private function getInsertId($table, $key){
        $query = "SELECT MAX(`".$key."`) as `maxid` FROM `".$table."`";
        $result = $this->query($query)->fetchAll();
        return $result[0]["maxid"];
    }
    public function changeRegisterAndRegistrant($insert_data, $register_id){
        unset($insert_data['shop_register'][0]["customer_id"]);
        $data_event = $insert_data['shop_register'][0];
        $this->updateById($register_id , $data_event);
        $registrant = $insert_data["shop_registrant_data"];
        $registrant_model = new shopRegistrypagePluginRegistrantModel();
        $error = $registrant_model->updateRegistrantByRegisterId($register_id, $registrant);
        if($error === 3){
            return "Registrant update error";
        }
        return $error;
    }
    public function getReestrPrd($user_id){
        $prd_model = new shopRegistrypagePluginProductModel();
        $product_model = new shopProductModel();
        $registers = $this->getReestr($user_id);
        foreach ($registers as $key => $register) {
            $data = array(
                "register_id" => $register["register_id"],
            );
            $prdregister = $prd_model->getByField($data, true);
            foreach ($prdregister as $k => $prd) {
                $product = new shopProduct($prd["product_id"]);
                $registers[$key]["product"][$k] = $product->data;
                $registers[$key]["product"][$k]["count_buy"] = $prd["count_buy"];
                $registers[$key]["product"][$k]["count_true"] = $prd["count"];
            }
        }
        return $registers;
    }
    public function getReestryFind($firstname, $lastname, $place, $mouth, $year){
        $reg_model = new shopRegistrypagePluginRegistrantModel();
        $firstname = trim($firstname);
        $lastname = trim($lastname);
        $where = 'WHERE srd.firstname_regsitrant LIKE "'.$firstname.'%" AND srd.lastname_regsitrant LIKE "'.$lastname.'%" AND sr.status = 1 ';
        if($place){
            $where .= ' AND srd.state = "'.$place.'"';
        }
        if($mouth){
            $where .= ' AND MONTH(sr.datetime) = "'.$mouth.'"';
        }
        if($year){
            $where .= ' AND YEAR(sr.datetime) = "'.$year.'"';
        }
        $group = " GROUP BY sr.register_id ";
        // $group = "";
        $query = $this->getRegistryQuery($where, $group);
        $rs = $this->query($query)->fetchAll();
        foreach ($rs as $key => $registry) {
            $rs[$key]["title"] = $reg_model->getGetRegistrantNames($registry["register_id"]);
        }
        return $rs;
    }
    private function getCO($register_id){
        $query = "SELECT `firstname_regsitrant` as `firstname`, `lastname_regsitrant` as `lastname` FROM `shop_registrant_data` WHERE `register_id` = '".$register_id."' AND `type_registrant` = 2";
        $rs = $this->query($query)->fetchAll();
        return $rs[0];
    }
  
    public function getProductsByRegisterid($register_id){
        $prd_model = new shopRegistrypagePluginProductModel();
        $skus_model = new shopProductSkusModel();
        $data = array(
            "register_id" => $register_id,
            "status_product" => "1",
        );
        $products = array();
        $prdregister = $prd_model->getByField($data, true);
        foreach ($prdregister as $k => $prd) {
            $product = new shopProduct($prd["product_id"]);
            $products[$k] = $product->data;
            $products[$k]["count_buy"] = $prd["count_buy"];
            $products[$k]["count_true"] = $prd["count"];
            $products[$k]["register_id"] = $register_id;
            $products[$k]["status"] = $this->getprdStatus($prd["sku_id"] ,$prd["count"]);
            if($prd["sku_id"]) {
                $products[$k]["sku_id"] = $prd["sku_id"];
                $arr_sku = $skus_model->getById($prd["sku_id"]);
                $products[$k]["sku_name"] = $arr_sku["name"]; 
                $products[$k]["sku_sku"] = $arr_sku["sku"]; 
            }
        }
        return $products;
    }
    private function getprdStatus($sku_id, $cnt_registry){
        if(!isset($sku_id)) return "<span style='color:red;'>The product under the order</span>";
        $sku_model = new shopProductSkusModel();
        $sku = $sku_model->getById($sku_id);
        $cnt_stock = $sku["count"];
        if(!isset($cnt_stock)) return "The product is available";
        if($cnt_stock >= $cnt_registry) return "The product is available";
        return "<span style='color:red;'>The product under the order</span>";
    }
    public function getProductByRegistry($user_id, $reg_id, $sku_id){
        $query = "SELECT `srp`.`register_prd_id` AS `rp_prd` FROM `shop_register_product` AS `srp`
                  LEFT JOIN `shop_register` AS `sr`
                  ON  `srp`.`register_id` = `sr`.`register_id`
                  WHERE `srp`.`sku_id` = '".$sku_id."' AND `sr`.`register_id` = '".$reg_id."' AND `sr`.`customer_id` = '".$user_id."' AND srp.status_product = 1";
        $prd_id = $this->query($query)->fetchAll();
        return $prd_id[0]["rp_prd"];
    }
    public function getSkuByOptionsJoinVariant($product_id, $options){
        $index = 1;
        $query = "SELECT spf".$index.".sku_id FROM shop_product_features AS spf".$index." ";
        foreach ($options as $key => $option) {
            if($index != 1){
                $query .= " LEFT JOIN shop_product_features AS spf".$index."
                           ON (spf1.sku_id = spf".$index.".sku_id) ";
            }
            $index++;
        }
        $index = 1;
        $query .= " WHERE spf1.product_id = ".$product_id." ";
        foreach ($options as $key => $value) {
            $query .= " AND spf".$index.".feature_id = ".$key." ";
            $query .= " AND spf".$index.".feature_value_id = ".$value." ";
            $index++;
        }
        $query .= " ORDER BY spf1.sku_id DESC";
        $rs = $this->query($query)->fetchAll();
        return $rs[0]["sku_id"];
    }
    public function getMailInfoByRegisterId($register_id){
        $query = $this->getRegistryQuery("WHERE sr.register_id = '".$register_id."' AND srd.type_registrant = 1");
        $rp = $this->query($query)->fetchAll();
        return $rp[0];
    }
    public function sendMailCreateRP($register_id, $indx, $items = null){
        $model = new shopRegistrypagePluginModel();
        $note_model = new shopNotificationModel();

        $plugin = wa('shop')->getPlugin('registrypage');
        $data = array(
            "id" => $indx,
            "status" => 1,
        );
        $note = $note_model->getByField($data);
        $note_id = $note["id"];
        if($note_id){
            $note_params_model = new shopNotificationParamsModel();
            $wacontact = new waContactModel();
            $waemail = new waContactEmailsModel();

            $note_params = $note_params_model->getParams($note_id);
            $subject = str_replace('{$rp.id}', "№".$register_id, $note_params["subject"]);
            $text = $note_params["body"];
            $rp = $this->getMailInfoByRegisterId($register_id);
            $customer = $wacontact->getById($rp["customer_id"]);
            $emails = $waemail->getByField("contact_id", $rp["customer_id"]);
            $email = $emails["email"];
            $params["rp"] = $rp;
            $params["customer"] = $customer;
            if(isset($items)) {
                $params["items"] = $items;
                $params["create_datetime"] = date("Y-m-d");
            }
            $content = $plugin->getTemplateMail($text, $params);
            $admin_data = wa('shop')->getConfig()->getGeneralSettings();
           
            $email_to = $email;
            $name_to  = $customer["name"];

            $mail_message = new waMailMessage($subject, $content);
            $mail_message->setTo($email_to, $name_to);
            $mail_message->setFrom($admin_data["email"], $admin_data["name"]);
            $mail_message->send();
        }
    }
    public function sendMailCreateRPA($register_id, $indx, $items = null){
        $model = new shopRegistrypagePluginModel();
        $note_model = new shopNotificationModel();

        $plugin = wa('shop')->getPlugin('registrypage');
        $data = array(
            "id" => $indx,
            "status" => 1,
        );
        $note = $note_model->getByField($data);
        $note_id = $note["id"];
        if($note_id){
            $note_params_model = new shopNotificationParamsModel();
            $wacontact = new waContactModel();
            $waemail = new waContactEmailsModel();

            $note_params = $note_params_model->getParams($note_id);
            $subject = str_replace('{$rp.id}', "#".$register_id, $note_params["subject"]);
            $text = $note_params["body"];
            $rp = $this->getMailInfoByRegisterId($register_id);
            $customer = $wacontact->getById($rp["customer_id"]);
            $emails = $waemail->getByField("contact_id", $rp["customer_id"]);
            $email = $emails["email"];
            $params["rp"] = $rp;
            $params["customer"] = $customer;
            if(isset($items)) {
                $params["items"] = $items;
                $params["create_datetime"] = date("Y-m-d");
            }
            $content = $plugin->getTemplateMailA($text, $params);
            $admin_data = wa('shop')->getConfig()->getGeneralSettings();

            $email_to = $admin_data["email"];
            $name_to  = $admin_data["name"];
           
            $mail_message = new waMailMessage($subject, $content);
            $mail_message->setTo($email_to, $name_to);
            $mail_message->setFrom($admin_data["email"], $admin_data["name"]);
            $mail_message->send();
        }
    }

    public function getAdminAllRP($where = ""){
        $wacontact = new waContactModel();
        $waemail = new waContactEmailsModel();
        $prd_model = new shopRegistrypagePluginProductModel();
        $registrant_model = new shopRegistrypagePluginRegistrantModel();
        $event_model = new shopRegistrypagePluginCelebratyModel();

        $query = "SELECT * FROM ".$this->table." ORDER BY register_id DESC";
        
        if(!empty($where)){
            $query = "SELECT `sr`.* FROM `shop_register` AS sr
                        LEFT JOIN `wa_contact` AS wc
                        ON sr.`customer_id` = wc.`id`
                        LEFT JOIN `wa_contact_emails` AS wce
                        ON wc.`id`= wce.`contact_id`
                        LEFT JOIN `shop_registrant_data` AS srd
                        ON srd.`register_id` = sr.`register_id` WHERE 1 = 1 ";
            
            if($where["login"]) $query .= " AND wc.login = '".$where["login"]."'";
            if($where["email"]) $query .= " AND wce.email = '".$where["email"]."'";
            if($where["name"]) $query .= " AND srd.firstname_regsitrant LIKE '%".$where["name"]."%' OR  srd.lastname_regsitrant LIKE '%".$where["name"]."%'";

            if($where["event"]) $query .= " AND sr.type_celebraty = '".$where["event"]."'";
            if($where["status"]) $query .= " AND sr.status = '".$where["status"]."'";

            $query .= " GROUP BY sr.`register_id` ORDER BY sr.`register_date_created` DESC";        
        }

        $results = $this->query($query)->fetchAll();
        foreach ($results as $key => $result) {
            $contact_id = $result["customer_id"];
            $contact = $wacontact->getById($contact_id);
            $products = $prd_model->getByInfo($result["register_id"]);

            $registrants = $registrant_model->getByInfo($result["register_id"], $result["enable_co"], $result["shipping_registrant"]);
            $events = $event_model->getById($result['type_celebraty']);

            $emails = $waemail->getById($contact_id);
            $email = $emails["email"];
            
            $results[$key]["contact"] = $contact;
            $results[$key]["products"] = $products;
            $results[$key]["registrants"] = $registrants;
            $results[$key]["events"] = $events;
            $results[$key]["email"] = $email;
        }
        return $results;
    }  

    public function deleteForseRP($register_id){
        $prd_model = new shopRegistrypagePluginProductModel();
        $registrant_model = new shopRegistrypagePluginRegistrantModel();
        
        $prd_model->deleteByRP($register_id);
        $registrant_model->deleteByRP($register_id);
        $this->deleteById($register_id);
    }

    public function changeStatusRp($register_id, $status){
        $get_data = $this->getById($register_id);
        if($status == 1){
            $customer_id = $get_data['customer_id'];
            $query = "SELECT * FROM ".$this->table." WHERE `customer_id` = '".$customer_id."' AND status != 2";
            $rp_data = $this->query($query)->fetchAll();
            foreach ($rp_data as $key => $rp) {
                $data = array(
                    "status" => 3,
                );
                $this->updateById($rp["register_id"], $data);
            }
        }
        $data = array(
            "status" => $status,
        );
        $this->updateById($register_id, $data);
    }

    public function checkCountRP($user_id, $count_nact){
        $data = array(
          "customer_id" => $user_id, 
          "status" => 1,  
        );
        $count_active = $this->countByField($data);
        $data = array(
          "customer_id" => $user_id, 
          "status" => 3,  
        );
        $count_noactive = $this->countByField($data);
        if($count_active + $count_noactive > $count_nact) return false;
        return true;
    }


    
}