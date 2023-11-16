<?php
/**
* A class representing API-requests for info about the wiki
*
* The class UserinfoRequest allows the creation of API-requests for information on the wiki itself
*
* @method SimpleXMLElement execute()
* @method CurlHandle getRequest()
*/
class SiteinfoRequest extends Request {
	private array $properties;
	
	/**
	* constructor for class SiteinfoRequest
	*
	* @param string $url        the url to the wiki
	* @param array $properties  the properties to load
	* @access public
	*/
	public function __construct(string $url, array $properties) {
		$this->url = $url;
		$this->properties = $properties;
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
		$request->addToGetFields("meta", "siteinfo");
		$request->addToGetFields("siprop", implode("|", $this->properties));
		$request->addToGetFields("format", "xml");
		return $request->execute();
	}
}