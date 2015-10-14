<?php
/**
** PHP Shopify Api
** @author: Joshua Grierson
** @company: EncredDesign
** @version: 0.0.1
** @date-modified: 14/08/2015
**/
require_once dirname(dirname(__FILE__)).'/ShopifyApi/HttpRequest/Cookie.php';
require_once dirname(dirname(__FILE__)).'/ShopifyApi/shopify_api_exception.php';
class ShopifyApi {

	//public properties
	public $shop = '';
	public $api_key = '';
	public $client_secret = '';
	public $scopes = '';
	public $redirect_uri = '';
	public $nonce = '';
	public $params = array();

	//inner helper properties
	private $no_cookies = TRUE;
	private $code = '';
	private $hmac = '';
	private $timestamp = '';
	private $http = null;
	private $auth_legal_len = 0;

	//init correct params, for most purpose of installing app
	public function __construct($shop, $params){
		$this->api_key = isset($params['client_id']) ? $params['client_id'] : $this->commit_error('Api Key missing', 400, 'Bad Request');
		$this->scopes = isset($params['scope']) ? $params['scope'] : $this->commit_error('Scopes missing', 400, 'Bad Request');
		$this->client_secret = isset($params['client_secret']) ? $params['client_secret'] : $this->commit_error('Client Secret missing', 400, 'Bad Request');
		$this->shop = isset($shop) ? $shop : $this->commit_error('Shop name missing', 400, 'Bad Request');
		$this->redirect_uri = isset($params['redirect_uri']) ? $params['redirect_uri'] : $this->commit_error('Redirect Uri missing', 400, 'Bad Request');
		$this->params = $params;
		$this->params['state'] = hash('sha256', $this->api_key.$this->shop);
		$this->nonce = $this->params['state'];
	}

	//install app onto shopify store
	public function install($endpoint){
		//set location to install endpoint on shopify, for authentication process
		header('Location: https://'.$this->shop.$endpoint.$this->build_query_no_secret($this->params));
	}

	//once logged into shop, collect access token
	public function get_token($key){
		$token = null;
		//double check user not already auth, dont want to use more data than needed
		if(!$this->is_auth($key) && isset($_GET['code'])){
			//get params returned, store if later use
			$this->code = isset($_GET['code']) ? $_GET['code'] : null;
			$this->hmac = isset($_GET['hmac']) ? $_GET['hmac'] : null;
			$this->timestamp = isset($_GET['timestamp']) ? $_GET['timestamp'] : null;
			//make http request
			$post_body = array('Content-type' => 'application/json', 'client_id' => $this->api_key, 'client_secret' => $this->client_secret, 'code' => $this->code);
			$http = new BasicHttpRequest('/admin/oauth/access_token', 'POST', $post_body);
			if(!is_null($http)){
				$http->execute($post_body);
			}
		}
	}

	//build query string from array
	private function build_query($params=array()){
		$_query = '?';
		if(sizeof($params) > 0){
			foreach ($params as $key => $val) {
				$_query .= $key.'='.$val.'&';
			}
		}
		return (substr($_query, 0, strlen($_query)-1));
	}

	//build query string without client secret
	private function build_query_no_secret($params=array()){
		$_query = '?';
		if(sizeof($params) > 0){
			unset($params['client_secret']);
			foreach ($params as $key => $value) {
				$_query .= $key.'='.$value.'&';
			}
		}
		return (substr($_query, 0, strlen($_query)-1));
	}

	//check cookies for token and what not
	public function is_auth($key){
		$key_stripped = preg_replace('/\.+/', '_', $key);
		if(!is_null(Cookie::get_cookie($key_stripped))){
			if(strlen($key_stripped) >= $this->auth_legal_len){
				return false;
				throw new ShopifyApiException($this->shop, 'Auth Token Exception, not legal token', 0);
			}
			return true;
		}else{
			return false;
		}
	}

	//return error when occured
	private function commit_error($message, $error_code, $code_message){
		header('Content-Type: application/json');
		header('HTTP/1.0 '.$error_code.' '.$code_message);
		die(json_encode(array(
			'error_code' => $error_code,
			'error' => $message
		), JSON_PRETTY_PRINT));
	}
}
?>