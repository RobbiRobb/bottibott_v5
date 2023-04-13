<?php
/**
* A class for representing API-requests for userrights
*
* The class UserrightsRequest allows the creation of API-requests for querying the rights of the currently logged in user
* If the bot isn't logged in, the default rights will be queried
*
* @method SimpleXMLElement execute()
*/
class UserrightsRequest extends Request {
	/**
	* constructor for class UserrightsRequest
	*
	* @param string $url  the url to the wiki
	* @access public
	*/
	public function __construct(string $url) {
		$this->url = $url;
	}
	
	/**
	* executor for the API-request
	*
	* @return SimpleXMLElement  the SimpleXMLElement-representation of the query result
	* @access public
	*/
	public function execute() : SimpleXMLElement {
		$request = new APIRequest($this->url);
		$request->setCookieFile($this->cookiefile);
		$request->setLogger($this->logger);
		$request->addToGetFields("action", "query");
		$request->addToGetFields("meta", "userinfo");
		$request->addToGetFields("uiprop", "rights");
		$request->addToGetFields("format", "xml");
		return $request->execute();
	}
}