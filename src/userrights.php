<?php
spl_autoload_register(function($class) {require(strtolower($class).".php");});

/**
* A class for representing API-requests for userrights
*
* The class Userrights allows the creation of API-requests for querying the rights of the currently logged in user
* If the bot isn't logged in, the default rights will be queried
*
* @method SimpleXMLElement execute()
*/
class Userrights extends Request {
	/**
	* constructor for class Userrights
	*
	* @param String $url  the url to the wiki
	* @access public
	*/
	public function __construct(String $url) {
		$this->url = $url;
		$this->cookiefile = "cookies.txt";
	}
	
	/**
	* executor for the API-request
	*
	* @return SimpleXMLElement  the SimpleXMLElement-representation of the query result
	* @access public
	*/
	public function execute() {
		$userrights = new APIRequest($this->url);
		$userrights->setCookieFile($this->cookiefile);
		$userrights->addToGetFields("action", "query");
		$userrights->addToGetFields("meta", "userinfo");
		$userrights->addToGetFields("uiprop", "rights");
		$userrights->addToGetFields("format", "xml");
		return $userrights->execute();
	}
}
?>