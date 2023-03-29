<?php
/**
* A class representing the basic data required for a request
*
* @abstract
* @method void setUrl(String $url)
* @method void setCookieFile(String $cookiefile)
* @method void setLogger(Logger $logger)
* @method Logger getLogger()
*/
abstract class Request {
	protected ?String $url = null;
	protected ?String $cookiefile = null;
	protected ?Logger $logger = null;
	
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
	
	/**
	* setter for the logger
	*
	* @param Logger $logger  a reference to the logger object
	* @access protected
	*/
	protected function setLogger(Logger &$logger) {
		$this->logger = $logger;
	}
	
	/**
	* getter for the logger
	*
	* @access protected
	*/
	protected function &getLogger() {
		return $this->logger;
	}
}
?>