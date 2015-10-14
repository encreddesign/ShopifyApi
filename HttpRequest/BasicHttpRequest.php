<?php
/**
** PHP Http simple request library
** @author: Joshua Grierson
** @company: EncredDesign
** @version: 0.0.1
** @date-modified: 18/08/2015
**/
//require classes
require_once dirname(dirname(__FILE__)).'/HttpRequest/BasicHttpFramework.php';
require_once dirname(dirname(__FILE__)).'/HttpRequest/Exception.php';
//start class
class BasicHttpRequest implements BasicHttpFramework {
	
	private $method = 'GET';
	private $url = '';
	private $query_params = '';

	private $headers = array();
	private $properties = array();
	private $http = null;
	private $response = null;
	private $header_size = 0;

	public function __construct(...$params){
		$this->url = $params[0];
		if(isset($params[1]))$this->method = $params[1];
		if(isset($params[2]))$this->query_params = http_build_query($params[2]);
		if(sizeof($params) > 3){
			throw new Exception('Params are too long');
		}
	}

	public function execute($headers){
		$this->http = curl_init();
		try{
			curl_setopt($this->http, CURLOPT_URL, $this->url);
			curl_setopt($this->http, CURLOPT_HEADER, $headers);
			curl_setopt($this->http, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($this->http, CURLOPT_CUSTOMREQUEST, $this->method);
			if(sizeof($this->properties) > 0){
				curl_setopt_array($this->http, $this->properties);
			}
			if($this->isPost($this->method) or $this->isPut($this->method)){
				if(is_null($this->query_params)){ throw new BasicHttpException('Error: Query Params cannot be empty for type POST'); }
				curl_setopt($this->http, CURLOPT_POSTFIELDS, $this->query_params);
			}
			if(sizeof($this->headers) > 0){
				curl_setopt($this->http, CURLOPT_HEADER, $this->headers);
			}
			$this->response = curl_exec($this->http);
			$error_code = curl_errno($this->http);
			$error = curl_error($this->http);
			$this->header_size = curl_getinfo($this->http, CURLINFO_HEADER_SIZE);
			if($error){
				throw new BasicHttpException('Error code: '.$error_code.' Http Error: '.$error);
			}
			curl_close($this->http);
		}catch(Exception $e){
			curl_error($this->http);
			die($e->getMessage());
		}
	}

	public function setMethod($type){
		try{
			if(is_null($this->http)){
				$this->method = $type;
			}else{
				throw new Exception('Method set too late');
			}
		}catch(Exception $e){
			die($e.getMessage());
		}
	}

	public function setUrl($url){
		try{
			if(is_null($this->http)){
				$this->url = $url;
			}else{
				throw new Exception('Url set too late');
			}
		}catch(Exception $e){
			die($e.getMessage());
		}
	}

	public function setParams(...$query){
		try{
			if(is_null($this->http)){
				$this->query_params = http_build_query($query);
			}else{
				throw new Exception('Query params set too late');
			}
		}catch(Exception $e){
			die($e.getMessage());
		}
	}

	public function setHeaders(...$headers){
		try{
			if(is_null($this->http)){
				$this->headers = $type;
			}else{
				throw new Exception('Headers set too late');
			}
		}catch(Exception $e){
			die($e.getMessage());
		}
	}

	public function setProperties($props){
		try{
			if(is_null($this->http)){
				$this->properties = $props;
			}else{
				throw new Exception('Properties set too late');
			}
		}catch(Exception $e){
			die($e.getMessage());
		}
	}

	public function getMethod(){
		if($this->method){
			return $this->method;
		}
	}

	public function getUrl(){
		if($this->url){
			return $this->url;
		}
	}

	public function getParams(){
		if($this->query_params){
			return $this->query_params;
		}
	}

	public function getRequestHeaders(){
		if($this->headers){
			return $this->headers;
		}
	}

	public function getResponse(){
		if($this->response){
			return substr($this->response, $this->header_size);
		}
	}

	public function getResponseHeaders(){
		if(!is_null($this->response)){
			$header = substr($this->response, 0, $this->header_size);
			$headers = array();
			if($header){
				$headers_explode = explode("\r\n", $header);
				foreach($headers_explode as $i => $header){
					if($i === 0){
						$headers['HTTP-Code'] = $header;
					}else{
						if($header != ''){
							list($key, $name) = explode(": ", $header);
							$headers[$key] = $name;
							$this->http_status($headers);
						}
					}
				}
			}
			return $headers;
		}
	}

	private function http_status($headers){
		if(!is_null($headers['HTTP-Code'])){
			$matches = array();
			header($headers['HTTP-Code']);
			$status = preg_match("/\w*\/\d*\.\d*\s*(.*)\s*/", $headers['HTTP-Code'], $matches);
			if($status > 0){
				header("X-Server-Status: ".$matches[1]);
			}
		}
	}

	private function isPost($type){
		return (strcasecmp($type, 'POST') == 0);
	}

	private function isPut($type){
		return (strcasecmp($type, 'PUT') == 0);
	}
}
?>