<?php

class shopRegistrypagePluginFrontendRegistrycreateregController  extends waController
{
    public function execute()
    {

        $array_table = array(
            "shop_register" => array(
                array(
                    "user_id" => "customer_id", 
                    "reg_event_name" => "name_register", 
                    "reg_event_type" => "type_celebraty", 
                    "date_event" => "datetime", 
                    "enable_registrant_co" => "enable_co", 
                    "before_event" => "shipping_registrant", 
                ),
            ),
            "shop_registrant_data" => array(
                array(
                    "reg_user_firstname" => "firstname_regsitrant", 
                    "reg_user_lastname" => "lastname_regsitrant" , 
                    "reg_user_address" => "address_registrant", 
                    "reg_user_city" => "city_registrant", 
                    "reg_user_state"  => "state" ,
                    "reg_user_zip" => "zip_registrant",  
                    "reg_user_phone" => "phone_registrant",
                ),
                array(
                    "reg_user_firstname_co" => "firstname_regsitrant",
                    "reg_user_lastname_co" => "lastname_regsitrant" , 
                    "reg_user_address_co" => "address_registrant", 
                    "reg_user_city_co" => "city_registrant", 
                    "reg_user_state_co" => "state" ,
                    "reg_user_zip_co" => "zip_registrant",  
                    "reg_user_phone_co" => "phone_registrant",
                ),
                array(
                    "reg_user_firstname_before" => "firstname_regsitrant",
                    "reg_user_lastname_before" => "lastname_regsitrant", 
                    "reg_user_address_before" => "address_registrant",
                    "reg_user_city_before" => "city_registrant",   
                    "reg_user_state_before"  => "state" , 
                    "reg_user_zip_before"  => "zip_registrant",
                    "reg_user_phone_before" => "phone_registrant",
                ),
            ),

        );
        $data = waRequest::post();
        $data["date_event"] = $this->datePrepare($data);
        if($data["change"] == 0){
            if(wa()->getUser()->getId() == $data["user_id"]){
                $wacontcat = new waContactCategoriesModel();
                $datawacontcat = array(
                    "contact_id" => $data["user_id"],
                    "category_id" => 2,
                );
                $wacontcat->insert($datawacontcat, 1);
                $insert_data = array();
                foreach ($data as $key => $value) {
                    foreach ($array_table as $tablename => $dbdata) {
                        foreach ($dbdata as $insertline => $fields) {
                            foreach ($fields as $postfield => $dbfield) {
                                if($value != ""){
                                    if($postfield == $key){
                                        if($dbfield == "enable_co"){
                                            $insert_data[$tablename][$insertline][$dbfield] = 1;
                                        }
                                        else{
                                            $insert_data[$tablename][$insertline][$dbfield] = $value; 
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $model = new shopRegistrypagePluginModel();
                $addinfo =  $model->addRegisterAndRegistrant($insert_data);
                $mail_reg_id = $addinfo[1];
                if($mail_reg_id){
                    $model->sendMailCreateRP($mail_reg_id, 6);
                    $model->sendMailCreateRPA($mail_reg_id, 7);
                }
                $answer[1] =$addinfo[0];
                echo json_encode($answer);
                exit;
            }
            else{
               $this->checkempty(false, "Error UserAccount False");
            }
        }
        elseif($data["change"]){
            $model = new shopRegistrypagePluginModel();
            $ischange_user = $model->isChangeUser($data["user_id"], $data["change"]); 
            if(wa()->getUser()->getId() == $data["user_id"] && $ischange_user){
                $insert_data = array();
                foreach ($data as $key => $value) {
                    foreach ($array_table as $tablename => $dbdata) {
                        foreach ($dbdata as $insertline => $fields) {
                            foreach ($fields as $postfield => $dbfield) {
                                if($value != ""){
                                    if($postfield == $key){
                                        if($dbfield == "enable_co"){
                                            $insert_data[$tablename][$insertline][$dbfield] = 1;
                                        }
                                        else{
                                            $insert_data[$tablename][$insertline][$dbfield] = $value; 
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                if(!$insert_data["shop_register"][0]["enable_co"]) $insert_data["shop_register"][0]["enable_co"] = 0;
                $answer[1] = $model->changeRegisterAndRegistrant($insert_data, $ischange_user);
                echo json_encode($answer);
                exit;
            }
            else{
               $this->checkempty(false, "Error UserAccount False");
            }
        }
        else{
            $this->checkempty(false, "Error Change Error");
        }
    }
    private function checkempty($field, $error = "Unknown Error"){
        if($field) return true;
        $answer[1] = $error;
        echo json_encode($answer);
        exit;
    }
    private function createDate($mounth, $day, $year){
        return date("Y-m-d", mktime(0, 0, 0, $mounth, $day, $year));
    }

    private function datePrepare($data){
        $year = $data["date_year"]?$data["date_year"]:0;
        $month = $data["date_mounth"]?$data["date_mounth"]:0;
        $day = $data["date_day"]?$data["date_day"]:0;
        $new_date = mktime(0, 0, 0, $month, $day, $year);
        return date('Y-m-d', $new_date); 
    }

   
    
}