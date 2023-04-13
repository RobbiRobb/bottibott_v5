<?php
/**
* A class representing API-requests for a token
*
* The class TokenRequest allows the creation of API-requests for a token of a given type
*
* @method SimpleXMLElement execute()
*/
class TokenRequest extends Request {
	private string $type;
	
	/**
	* constructor for class TokenRequest
	*
	* @param string $url   the url to the wiki
	* @param string $type  the type of the token
	* @access public
	*/
	public function __construct(string $url, string $type) {
		$this->url = $url;
		$this->type = $type;
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
		$request->addToGetFields("meta", "tokens");
		$request->addToGetFields("type", $this->type);
		$request->addToGetFields("format", "xml");
		return $request->execute();
	}
}