<?php
/**
* A class for representing API-requests of getting the url of files
*
* The class FileurlRequest represents API-requests for getting the url of one or multiple files
*
* @method void setFiles(string $files)
* @method SimpleXMLElement execute()
* @method CurlHandle getRequest()
*/
class FileurlRequest extends Request {
	private string $files;
	
	/**
	* constructor for class FileurlRequest
	*
	* @param string $url    the url to the wiki
	* @param string $files  files for which the urls should be queried
	* @access public
	*/
	public function __construct(string $url, string $files) {
		$this->url = $url;
		$this->files = $files;
	}
	
	/**
	* setter for the files
	*
	* @param string $files  the files that should be set
	* @access public
	*/
	public function setFiles(string $files) : void {
		$this->files = $files;
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
		$request->addToGetFields("prop", "imageinfo");
		$request->addToGetFields("iiprop", "url");
		$request->addToPostFields("titles", $this->files);
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
		$request->addToGetFields("format", "xml");
		$request->addToGetFields("prop", "imageinfo");
		$request->addToGetFields("iiprop", "url");
		$request->addToPostFields("titles", $this->files);
		return $request->getRequest();
	}
}