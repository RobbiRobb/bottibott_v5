<?php
/**
* A class representing API-requests for a logout
*
* The class Logout allows the creation of API-requests for logging out of a wiki after a successful login
*
* @method SimpleXMLElement execute(String $token)
*/
class Logout extends Request {
	/**
	* constructor for class Logout
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
	* @param String $token      the token required to logout
	* @return SimpleXMLElement  the SimpleXMLElement-representation of the query result
	* @access public
	*/
	public function execute(String $token) {
		$logout = new APIRequest($this->url);
		$logout->setCookieFile($this->cookiefile);
		$logout->setLogger($this->logger);
		$logout->addToGetFields("action", "logout");
		$logout->addToGetFields("format", "xml");
		$logout->addToPostFields("token", $token);
		return $logout->execute();
	}
}
?>