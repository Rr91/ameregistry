<?php 

class shopRegistrypagePluginCartModel extends waModel
{
	protected $table = "shop_register_cart";
	protected $id = "cart_id";

	public function getInfo($order_id){
		$query = "SELECT src.*, sp.name AS prdname,sp.id AS prdid, sps.name AS skuname, sps.sku, sr.name_register AS name_register FROM shop_register_cart AS src
					LEFT JOIN shop_product_skus AS sps
					ON src.sku_id = sps.id
					LEFT JOIN shop_product AS sp
					ON sps.product_id = sp.id
					LEFT JOIN shop_register AS sr
					ON sr.register_id = src.register_id
					WHERE src.order_id = '".$order_id."'";

		return $this->query($query)->fetchAll();
	}
}