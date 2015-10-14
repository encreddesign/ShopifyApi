<?php

//include required scripts
require_once dirname(dirname(__FILE__)).'/ShopifyApi/HttpRequest/BasicHttpRequest.php';
require_once dirname(dirname(__FILE__)).'/ShopifyApi/shopify_api.php';

//init ShopifyApi
$shopify = new ShopifyApi('brainchildapparel.myshopify.com', array(
	'client_id' => 'CLIENT_ID',
	'client_secret' => 'CLIENT_SECRET',
	'scope' => 'read_products,write_products',
	'redirect_uri' => 'http://localhost/ShopifyApi/index.php'
));

//if user already authenticated, it will skip over
if(!$shopify->is_auth($shopify->shop) && !isset($_GET['code'])){
	$shopify->install('/admin/oauth/authorize');
}

//get the access token
$shopify->get_token($shopify->shop);

?>