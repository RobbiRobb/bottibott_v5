<?php
spl_autoload_register(function($class) {require(strtolower($class).".php");});

/**
* A class representing API-requests for checking if a page is a redirect
*
* The class Redirect represents API-requests for checking if a page is a redirect or not
*
* @method void setTitles(String $titles)
* @method SimpleXMLElement execute()
*/
class Redirect extends Request {
	private String $titles;
	
	/**
	* constructor for class Redirect
	*
	* @param String $url     the url to the wiki
	* @param String $titles  the titles to be checked
	* @access public
	*/
	public function __construct(String $url, String $titles) {
		$this->url = $url;
		$this->titles = $titles;
		$this->cookiefile = "cookies.txt";
	}
	
	/**
	* setter for the titles
	*
	* @param String $titles  the titles that should be set
	* @access public
	*/
	public function setTitles(String $titles) {
		$this->titles = $titles;
	}
	
	/**
	* executor for the API-request
	*
	* @return SimpleXMLElement  the SimpleXMLElement-representation of the query result
	* @access public
	*/
	public function execute() {
		$redirect = new APIRequest($this->url);
		$redirect->setCookieFile($this->cookiefile);
		$redirect->setLogger($this->logger);
		$redirect->addToPostFields("action", "query");
		$redirect->addToPostFields("redirects", "1");
		$redirect->addToPostFields("format", "xml");
		$redirect->addToPostFields("titles", $this->titles);
		return $redirect->execute();
	}
}
?>