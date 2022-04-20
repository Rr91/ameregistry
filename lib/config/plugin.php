<?php
return array (
  'name' => 'GIFT REGISTRY',
  'icon' => 'img/registry_page.gif',
  'version' => '1.0.0',
  'vendor' => '666666',
  'frontend' => true,
  'handlers' =>
  array (
    'frontend_my_nav' => 'registryPageNav',
  	'frontend_nav' => 'registryNav',
    'frontend_product' => 'registryButton',
    'cart_add' => 'registryCartadd',
    'cart_delete' => 'registryCartdelete',
    'order_action.create' => 'registryOrdercreate',
    'backend_menu' => 'registryBackendMenu',
    'backend_order' => 'registryBackendOrder',
  ),
);
