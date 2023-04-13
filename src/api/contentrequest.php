<?php
/**
* A class representing API-requests for the content of pages
*
* The class ContentRequest allows the creation of API-requests for the content of given pages
*
* @method SimpleXMLElement execute()
* @method CurlHandle getRequest()
*/
class ContentRequest extends Request {
	private string $titles;
	
	/**
	* constructor for class ContentRequest
	*
	* @param string $url  the url to the wiki
	* @access public
	*/
	public function __construct(string $url, string $titles) {
		$this->url = $url;
		$this->titles = $titles;
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
		$request->addToGetFields("format", "xml");
		$request->addToGetFields("prop", "revisions");
		$request->addToGetFields("rvprop", "content");
		$request->addToGetFields("rvslots", "*");
		$request->addToPostFields("titles", $this->titles);
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
		$request->addToGetFields("action", "query");
		$request->addToGetFields("format", "xml");
		$request->addToGetFields("prop", "revisions");
		$request->addToGetFields("rvprop", "content");
		$request->addToGetFields("rvslots", "*");
		$request->addToPostFields("titles", $this->titles);
		return $request->getRequest();
	}
}