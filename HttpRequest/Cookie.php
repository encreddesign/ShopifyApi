<?php
/**
** PHP Cookie lib helper
** @author: Joshua Grierson
** @company: EncredDesign
** @version: 0.0.1
** @date-modified: 18/08/2015
**/
class Cookie {

	//create new cookie
	public static function create_cookie($name, $val, $expires){
		if(!isset($_COOKIE[$name])){
			setcookie($name, $val, $expires);
		}
	}

	//create array of cookies
	public static function create_cookies($cookies, $expires){
		if(sizeof($cookies) > 0){
			$index = 0;
			foreach ($cookies as $key => $value) {
				if(!isset($_COOKIE[$key])){
					setcookie($key, $value, $expires);
				}
			}
		}
	}

	//get cookie
	public static function get_cookie($key){
		if(isset($_COOKIE[$key])){
			return $_COOKIE[$key];
		}
		return null;
	}

	//get all cookies
	public static function get_cookies(){
		$cks = array();
		if(sizeof($_COOKIE) > 0){
			foreach ($_COOKIE as $key => $value) {
				$cks[$key] = $value;
			}
		}
		return $cks;
	}

	//destory cookie
	public static function remove_cookie($key){
		if(isset($_COOKIE[$key])){
			unset($_COOKIE[$key]);
		}
	}

	//destory cookies
	public static function remove_cookies(){
		if(sizeof($_COOKIE) > 0){
			foreach ($_COOKIE as $key => $value) {
				unset($_COOKIE[$key]);
			}
		}
	}
}
?>