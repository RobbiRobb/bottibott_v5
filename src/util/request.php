<?php
/**
* A class representing the basic data required for a request
*
* @abstract
* @method void setUrl(string $url)
* @method string getUrl()
* @method void setCookieFile(string $cookiefile)
* @method string getCookieFile()
* @method void setLogger(Logger $logger)
* @method Logger getLogger()
*/
abstract class Request {
	protected string $url;
	protected string $cookiefile;
	protected Logger $logger;
	
	/**
	* setter for the url
	*
	* @param string $url  the url that should be set
	* @access protected
	*/
	public function setUrl(string $url) : void {
		$this->url = $url;
	}
	
	/**
	* getter for the url
	*
	* @return string  the url
	* @access public
	*/
	public function getUrl() : string {
		return $this->url;
	}
	
	/**
	* setter for the cookiefile
	*
	* @param string $cookiefile  the name of the cookiefile that should be used
	* @access protected
	*/
	public function setCookieFile(string $cookiefile) : void {
		$this->cookiefile = $cookiefile;
	}
	
	/**
	* getter for the cookiefile
	*
	* @return string  the name of the cookie file
	* @access public
	*/
	public function getCookieFile() : string {
		return $this->cookiefile;
	}
	
	/**
	* setter for the logger
	*
	* @param Logger $logger  a reference to the logger object
	* @access protected
	*/
	public function setLogger(Logger &$logger) : void {
		$this->logger = $logger;
	}
	
	/**
	* getter for the logger
	*
	* @return Logger  a reference to the logger object
	* @access public
	*/
	public function &getLogger() : Logger {
		return $this->logger;
	}
}