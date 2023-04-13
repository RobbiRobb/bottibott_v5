<?php
/**
* A class for representing API-requests for revisions
*
* The class RevisionsRequest allows the creation of API-requests for querying information about revisions
* It is possible to either query revisions from one or multiple revids
* or use a page title to get all revisions of that page
*
* @method void setRevids(string $revids)
* @method void setPage(string $page)
* @method void setLimit(string $limit)
* @method void setContinue(string $continue)
* @method SimpleXMLElement execute()
* @method CurlHandle getRequest()
*/
class RevisionsRequest extends Request {
	private string $revids;
	private string $page;
	private string $limit;
	private string $continue;
	
	/**
	* constructor for class RevisionsRequest
	*
	* @param string $url     the url to the wiki
	* @access public
	*/
	public function __construct(string $url) {
		$this->url = $url;
	}
	
	/**
	* setter for the revids
	*
	* @param string $revids  the revids that should be set
	* @access public
	*/
	public function setRevids(string $revids) : void {
		$this->revids = $revids;
	}
	
	/**
	* setter for the page
	*
	* @param string $page  the page that should be set
	* @access public
	*/
	public function setPage(string $page) : void {
		$this->page = $page;
	}
	
	/**
	* setter for the limit
	*
	* @param string $limit  the limit that should be set
	* @access public
	*/
	public function setLimit(string $limit) : void {
		$this->limit = $limit;
	}
	
	/**
	* setter for the continue value
	*
	* @param string $continue  the value that should be set for continuation
	* @access public
	*/
	public function setContinue(string $continue) : void {
		$this->continue = $continue;
	}
	
	/**
	* executor for the API-request
	*
	* @return SimpleXMLElement  the SimpleXMLElement-representation of the query result
	* @access public
	*/
	public function execute() : SimpleXMLElement {
		if(isset($this->revids) && isset($this->page)) { throw new Error("Can not set both revids and page"); }
		if(!isset($this->revids) && !isset($this->page)) { throw new Error("Neither revids nor page are set"); }
		$request = new APIRequest($this->url);
		$request->setCookieFile($this->cookiefile);
		$request->setLogger($this->logger);
		$request->addToGetFields("action", "query");
		$request->addToGetFields("prop", "revisions");
		$request->addToGetFields("rvprop", "ids|timestamp|user|userid");
		$request->addToGetFields("format", "xml");
		if(isset($this->revids)) { $request->addToPostFields("revids", $this->revids); }
		if(isset($this->page)) { $request->addToPostFields("titles", $this->page); }
		if(isset($this->page)) { $request->addToGetFields("rvlimit", $this->limit); }
		if(isset($this->page) && !empty($this->continue)) { $request->addToGetFields("rvcontinue", $this->continue); }
		return $request->execute();
	}
	
	/**
	* getter for an API-request of revids
	*
	* @return CurlHandle  a reference to the request handle
	* @access public
	*/
	public function &getRequest() : CurlHandle {
		if(isset($this->revids) && isset($this->page)) { throw new Error("Can not set both revids and page"); }
		if(!isset($this->revids) && !isset($this->page)) { throw new Error("Neither revids nor page are set"); }
		$request = new APIRequest($this->url);
		$request->setCookieFile($this->cookiefile);
		$request->addToGetFields("action", "query");
		$request->addToGetFields("prop", "revisions");
		$request->addToGetFields("rvprop", "ids|timestamp|user|userid");
		$request->addToGetFields("format", "xml");
		if(isset($this->revids)) { $request->addToPostFields("revids", $this->revids); }
		if(isset($this->page)) { $request->addToPostFields("titles", $this->page); }
		if(isset($this->page)) { $request->addToGetFields("rvlimit", $this->limit); }
		if(isset($this->page) && !empty($this->continue)) { $request->addToGetFields("rvcontinue", $this->continue); }
		return $request->getRequest();
	}
}