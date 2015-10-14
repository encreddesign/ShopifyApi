<?php
/**
** PHP Http simple request library
** @author: Joshua Grierson
** @company: EncredDesign
** @version: 0.0.1
** @date-modified: 18/08/2015
**/
interface BasicHttpFramework {

	/*
	* SET functions
	*/
	public function setMethod($type);

	public function setUrl($url);

	public function setParams(...$query);

	public function setHeaders(...$headers);

	/*
	* GET functions
	*/
	public function getMethod();

	public function getUrl();

	public function getParams();

	public function getRequestHeaders();

	public function getResponseHeaders();
}
?>