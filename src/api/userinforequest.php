<?php
/**
* A class representing API-requests for editcounts
*
* The class UserinfoRequest allows the creation of API-requests for information on the user
*
* @method void setUsers(string $users)
* @method SimpleXMLElement execute()
*/
class UserinfoRequest extends Request {
	private string $users;
	
	/**
	* constructor for class UserinfoRequest
	*
	* @param string $url    the url to the wiki
	* @param string $users  the users for which the information should be queried
	* @access public
	*/
	public function __construct(string $url, string $users) {
		$this->url = $url;
		$this->users = $users;
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
		$request->addToGetFields("list", "users");
		$request->addToGetFields("usprop", "editcount|groups|registration|rights");
		$request->addToGetFields("format", "xml");
		$request->addToPostFields("ususers", $this->users);
		return $request->execute();
	}
	
	/**
	* getter for the API-request for executing multiple requests at once
	*
	* @return CurlHandle  a reference to the request handle
	* @access public
	*/
	public function &getRequest() : CurlHandle {
		$request = new APIRequest($this->url);
		$request->setCookieFile($this->cookiefile);
		$request->setLogger($this->logger);
		$request->addToGetFields("action", "query");
		$request->addToGetFields("list", "users");
		$request->addToGetFields("usprop", "editcount|groups|registration|rights");
		$request->addToGetFields("format", "xml");
		$request->addToPostFields("ususers", $this->users);
		return $request->getRequest();
	}
}