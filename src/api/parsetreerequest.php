<?php
/**
* A class for representing API-requests for parsetrees
*
* The class ParsetreeRequest allows the representation of parsetrees
* It allows the bot to generate and expand those parsetrees
*
* @method void setPage(string $page)
* @method void setTitle(string $title)
* @method void setContent(string $content)
* @method SimpleXMLElement execute()
* @method CurlHandle getRequest()
*/
class ParsetreeRequest extends Request {
	private string $page;
	private string $title;
	private string $content;
	
	/**
	* constructor for class ParsetreeRequest
	*
	* @param string $url      the url to the wiki
	* @access public
	*/
	public function __construct(string $url) {
		$this->url = $url;
	}
	
	/**
	* setter for the page
	*
	* @param string $page  the page to set
	* @access public
	*/
	public function setPage(string $page) : void {
		$this->page = $page;
	}
	
	/**
	* setter for the title
	*
	* @param string $title  the title that should be set
	* @access public
	*/
	public function setTitle(string $title) : void {
		$this->title = $title;
	}
	
	/**
	* setter for the content
	*
	* @param string $content  the content that should be set
	* @access public
	*/
	public function setContent(string $content) : void {
		$this->content = $content;
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
		$request->addToGetFields("action", "parse");
		$request->addToGetFields("prop", "parsetree");
		$request->addToGetFields("format", "xml");
		// only set page if is set
		if(isset($this->page)) { $request->addToGetFields("page", $this->page); }
		// only set title if is set
		if(isset($this->title)) { $request->addToGetFields("title", $this->title); }
		// only set text if is set
		if(isset($this->content)) { $request->addToPostFields("text", $this->content); }
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
		$request->addToGetFields("action", "parse");
		$request->addToGetFields("prop", "parsetree");
		$request->addToGetFields("format", "xml");
		// only set page if is set
		if(isset($this->page)) { $request->addToGetFields("page", $this->page); }
		// only set title if is set
		if(isset($this->title)) { $request->addToGetFields("title", $this->title); }
		// only set text if is set
		if(isset($this->content)) { $request->addToPostFields("text", $this->content); }
		return $request->getRequest();
	}
}