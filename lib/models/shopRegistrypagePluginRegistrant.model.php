<?php 

class shopRegistrypagePluginRegistrantModel extends waModel
{
	protected $table = 'shop_registrant_data';

    public function addResitrantsByRp($register_id, $registrants){
    	foreach ($registrants as $key => $registrant) {
    		$check = $this->checkRegistrants($registrant, $key);
    		if($check === 1){
    			$this->insertRegistrant($register_id, $registrant, $key);
    		}
    		elseif($check === 3) return $check;
    	}
    	return 1;
    }
    public function updateRegistrantByRegisterId($register_id, $registrants){
        foreach ($registrants as $key => $registrant) {
            $check = $this->checkRegistrants($registrant, $key);
            if($check === 1){
                $this->updateRegistrant($register_id, $registrant, $key);
            }
            elseif($check === 3) return $check;
        }
        return 1;
    }
    private function updateRegistrant($register_id, $registrant, $key){
        $registrant["register_id"] = $register_id;
        $registrant["type_registrant"] = $key + 1;
        $this->insert($registrant, 1);
    }
    private function insertRegistrant($register_id, $registrant, $key){
    	$registrant["register_id"] = $register_id;
    	$registrant["type_registrant"] = $key+1;
    	$this->insert($registrant);
    }
    private function checkRegistrants($registrant, $key){ 
        foreach ($registrant as $field => $value) {
        	if (count($registrant) !== 7) {
        		return 2;
        	}
            if(!$this->fieldExists($field) || $value == ""){
            	return 3;
            }
            return 1;
        }
    }

    public function getByInfo($register_id, $enable_co, $shipping_registrant){
        $registrants = array();
        
        $registrant = array();
        $registrant_co = array();
        $shipping = array();
        
        $data = array(
            "register_id" => $register_id,
            "type_registrant" => 1,
        );
        $registrant = $this->getByField($data);

        if($enable_co){
             $data = array(
                "register_id" => $register_id,
                "type_registrant" => 2,
            );
            $registrant_co = $this->getByField($data);
        }

        $data = array(
            "register_id" => $register_id,
            "type_registrant" => $shipping_registrant,
        );
        $shipping = $this->getByField($data);
        
        if(empty($shipping)) $shipping = $registrant;
        
        $registrants["registrant"] = $registrant;
        $registrants["registrant_co"] = $registrant_co;
        $registrants["shipping"] = $shipping;

        return $registrants;
    }

    public function getGetRegistrantNames($register_id){
        $rs = $this->getFieldFromTableByRegistryId($register_id, array("firstname_regsitrant", "lastname_regsitrant"), "`type_registrant` != 3");
        $view_title = "";
        $delimeiter = "";
        foreach ($rs as $key => $value) {
            $view_title .= $delimeiter.$value["firstname_regsitrant"];
            $view_title .= " ".$value["lastname_regsitrant"];
            $delimeiter = " & ";
        }
        return $view_title;
    }

    private function getFieldFromTableByRegistryId($register_id = 0, $fields = array(), $and = ""){
        $selected_fields_str = $this->prepareRegistrantFields($fields);
        if($register_id == 0){
            if($and != "") $and = " WHERE $and";
            $query = "SELECT ".$selected_fields_str." FROM `".$this->table."` ".$and;
        }
        else{
            if($and != "") $and = " AND $and";
            $query = "SELECT ".$selected_fields_str." FROM `".$this->table."` WHERE `register_id` = '".$register_id."'".$and;   
        }
        $rs = $this->query($query)->fetchAll();
        return $rs;
    }
    private function prepareRegistrantFields($fields){
        if(empty($fields)){
            return " * ";
        }
        $query = " ";
        foreach ($fields as $value) {
            $query .= "`".$value."`,";
        }
        return  substr($query,0,-1);
    }




    public function deleteByRP($register_id){
        $data = array(
            "register_id" => $register_id,
        );
        $this->deleteByField($data);
    }
}