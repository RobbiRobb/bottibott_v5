<?php
spl_autoload_register(function($class) {require(strtolower($class).".php");});

/**
* A class representing API-requests for the content of pages
*
* The class Content allows the creation of API-requests for the content of given pages
*
* @method SimpleXMLElement execute()
*/
class Content extends Request {
	private String $titles;
	
	/**
	* constructor for class Content
	*
	* @param String $url  the url to the wiki
	* @access public
	*/
	public function __construct(String $url, String $titles) {
		$this->url = $url;
		$this->titles = $titles;
		$this->cookiefile = "cookies.txt";
	}
	
	/**
	* executor for the API-request
	*
	* @return SimpleXMLElement  the SimpleXMLElement-representation of the query result
	* @access public
	*/
	public function execute() {
		$content = new APIRequest($this->url);
		$content->setCookieFile($this->cookiefile);
		$content->addToGetFields("action", "query");
		$content->addToGetFields("format", "xml");
		$content->addToGetFields("prop", "revisions");
		$content->addToGetFields("rvprop", "content");
		$content->addToGetFields("rvslots", "*");
		$content->addToPostFields("titles", $this->titles);
		return $content->execute();
	}
}
?>