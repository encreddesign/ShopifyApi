<?php
/**
** PHP ShopifyApiException
** @author: Joshua Grierson
** @company: EncredDesign
** @version: 0.0.1
** @date-modified: 14/08/2015
**/
class ShopifyApiException extends Exception {

	private $shop_name = null;
	
	public function __construct($shop_name = null, $message = null, $code = 0, Exception $previous = null){
		$this->shop_name = $shop_name;
		parent::__construct($message, $code, $previous);
	}

	public function __toString(){
		return __CLASS__."{$this->shop_name}:[{$this->code}]: {$this->message}/n";
	}

	public function getJson(){
		return json_encode(array('message' => $this->message, 'code' => $this->code, 'line' => $this->line));
	}

	public function getTraceJson(){
		return json_encode($this->getTrace());
	}
}
?>