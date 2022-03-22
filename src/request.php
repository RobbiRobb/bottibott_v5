<?php
/**
* A class representing the basic data required for a request
*
* @abstract
* @method void setUrl(String $url)
* @method void setCookieFile(String $cookiefile)
*/
abstract class Request {
	protected $url;
	protected $cookiefile;
	
	/**
	* setter for the url
	*
	* @param String $url  the url that should be set
	* @access protected
	*/
	protected function setUrl(String $url) {
		$this->url = $url;
	}
	
	/**
	* setter for the cookiefile
	*
	* @param String $cookiefile  the name of the cookiefile that should be used
	* @access protected
	*/
	protected function setCookieFile(String $cookiefile) {
		$this->cookiefile = $cookiefile;
	}
}
?>