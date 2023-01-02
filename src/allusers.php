<?php
spl_autoload_register(function($class) {require(strtolower($class).".php");});

/**
* A class for representing API-requests for all registered users on a wiki
*
* The class Allusers allows the representation of API-requests for all registered users on a wiki
*
* @method void setLimit(String $limit)
* @method void setContinue(String $continue)
* @method SimpleXMLElement execute()
*/
class Allusers extends Request {
	private String $limit;
	private String $continue;
	
	/**
	* constructor for class Allusers
	*
	* @param String $url       the url to the wiki
	* @param String $continue  continue for additional queries
	* @param String $limit     the maximum amount of users requested
	* @access public
	*/
	public function __construct(String $url, String $limit = "max", String $continue = "") {
		$this->url = $url;
		$this->continue = $continue;
		$this->limit = $limit;
		$this->cookiefile = "cookies.txt";
	}
	
	/**
	* setter for the limit
	*
	* @param String $limit  the limit that should be set
	* @access public
	*/
	public function setLimit(String $limit) {
		$this->limit = $limit;
	}
	
	/**
	* setter for the continue value
	*
	* @param String $continue  the value that should be set for continuation
	* @access public
	*/
	public function setContinue(String $continue) {
		$this->continue = $continue;
	}
	
	/**
	* executor for the API-request
	*
	* @return SimpleXMLElement  the SimpleXMLElement-representation of the query result
	* @access public
	*/
	public function execute() {
		$allusers = new APIRequest($this->url);
		$allusers->setCookieFile($this->cookiefile);
		$allusers->setLogger($this->logger);
		$allusers->addToGetFields("action", "query");
		$allusers->addToGetFields("list", "allusers");
		$allusers->addToGetFields("format", "xml");
		$allusers->addToGetFields("aufrom", $this->continue);
		$allusers->addToGetFields("aulimit", $this->limit);
		return $allusers->execute();
	}
}
?>