<?php
class shopRegistrypagePluginCelebratyModel extends waModel
{
    protected $table = 'shop_registry_celebraty';

    public function getNameCelebratyByTcId($tc_id){
    	$rs = $this->getById($tc_id);
    	return $rs["name_celebraty"];
    }
}