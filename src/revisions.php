<?php
spl_autoload_register(function($class) {require(strtolower($class).".php");});

/**
* A class for representing API-requests for revisions
*
* The class Revisions allows the creation of API-requests for querying information about revisions independently from page names
*
* @method void setRevids(String $revids)
* @method SimpleXMLElement execute()
* @method CurlHandle getRequest()
*/
class Revisions extends Request {
	private String $revids;
	
	/**
	* constructor for class Revisions
	*
	* @param String $url     the url to the wiki
	* @param String $revids  the revids that should be queried
	* @access public
	*/
	public function __construct(String $url, String $revids) {
		$this->url = $url;
		$this->revids = $revids;
		$this->cookiefile = "cookies.txt";
	}
	
	/**
	* setter for the revids
	*
	* @param String $revids  the revids that should be set
	* @access public
	*/
	public function setRevids(String $revids) {
		$this->revids = $revids;
	}
	
	/**
	* executor for the API-request
	*
	* @return SimpleXMLElement  the SimpleXMLElement-representation of the query result
	* @access public
	*/
	public function execute() {
		$revisions = new APIRequest($this->url);
		$revisions->setCookieFile($this->cookiefile);
		$revisions->addToGetFields("action", "query");
		$revisions->addToGetFields("prop", "revisions");
		$revisions->addToGetFields("rvprop", "user|ids");
		$revisions->addToGetFields("format", "xml");
		$revisions->addToPostFields("revids", $this->revids);
		return $revisions->execute();
	}
	
	/**
	* getter for an API-request of revids
	*
	* @return CurlHandle  a reference to the request handle
	* @access public
	*/
	public function &getRequest() {
		$revisions = new APIRequest($this->url);
		$revisions->setCookieFile($this->cookiefile);
		$revisions->addToGetFields("action", "query");
		$revisions->addToGetFields("prop", "revisions");
		$revisions->addToGetFields("rvprop", "user|ids");
		$revisions->addToGetFields("format", "xml");
		$revisions->addToPostFields("revids", $this->revids);
		return $revisions->getRequest();
	}
}
?>