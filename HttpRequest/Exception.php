<?php
/**
** PHP HttpBasicException
** @author: Joshua Grierson
** @company: EncredDesign
** @version: 0.0.1
** @date-modified: 18/08/2015
**/
class BasicHttpException extends Exception {
	
	public function __construct($message = null, $code = 0, Exception $previous = null){
		parent::__construct($message, $code, $previous);
	}

	public function __toString(){
		return __CLASS__.":[{$this->code}]: {$this->message}/n";
	}

	public function getJson(){
		return json_encode(array('message' => $this->message, 'code' => $this->code, 'line' => $this->line));
	}

	public function getTraceJson(){
		return json_encode($this->getTrace());
	}
}
?>