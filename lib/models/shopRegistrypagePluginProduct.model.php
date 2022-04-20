<?php
class shopRegistrypagePluginProductModel extends waModel
{
    protected $table = 'shop_register_product';
    protected $id = 'register_prd_id';

    public function getByInfo($register_id){
    	$query = "SELECT srp.*, sps.`sku`, sps.`name` AS skuname, sp.`name` AS prdname, sp.`image_id` AS prd_imageid, sp.`ext` AS prd_ext, sp.`image_filename` AS prd_imagefilename, sps.`price` AS skuprice, sps.`count` AS skucount, sp.`currency` AS prdcurr FROM `shop_register_product` AS srp
					LEFT JOIN shop_product_skus AS sps
					ON srp.`sku_id` = sps.`id`
					LEFT JOIN shop_product AS sp
					ON srp.`product_id` = sp.id
					WHERE srp.register_id = '".$register_id."'";

		return $this->query($query)->fetchAll();
    }
    public function deleteByRP($register_id){
        $data = array(
            "register_id" => $register_id,
        );
        $this->deleteByField($data);
    }
    public function countByRegistry($register_id){
        $query = "SELECT COUNT(*) as `cnt` FROM `".$this->table."` WHERE `register_id` = ".$register_id." AND `status_product` = 1";
        $rs = $this->query($query)->fetchAll();
        return $rs[0]["cnt"];
    }
}