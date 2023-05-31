<?php
/**
* A class for representing API-requests for resolving redirects
*
* The class RedirectRequest allows the representation of API-requests for resolving redirects
* It loads all pages requested and resolves redirects, returning the name of the page they redirect to
*
* @method void setTitles(string $titles)
* @method SimpleXMLElement execute()
* @method CurlHandle getRequest()
*/
class RedirectRequest extends Request {
	private string $titles;
	
	/**
	* constructor for class RedirectRequest
	*
	* @param string $url  the url to the wiki
	* @access public
	*/
	public function __construct(string $url) {
		$this->url = $url;
	}
	
	/**
	* setter for the titles
	*
	* @param string $titles  the titles to set
	* @access public
	*/
	public function setTitles(string $titles) : void {
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
		$request->addToGetFields("redirects", "1");
		$request->addToGetFields("titles", $this->titles);
		return $request->execute();
	}
	
	/**
	* getter for an API-request of revids
	*
	* @return CurlHandle  a reference to the request handle
	* @access public
	*/
	public function &getRequest() : CurlHandle {
		$request = new APIRequest($this->url);
		$request->setCookieFile($this->cookiefile);
		$request->setLogger($this->logger);
		$request->addToGetFields("action", "query");
		$request->addToGetFields("format", "xml");
		$request->addToGetFields("redirects", "1");
		$request->addToGetFields("titles", $this->titles);
		return $request->getRequest();
	}
}