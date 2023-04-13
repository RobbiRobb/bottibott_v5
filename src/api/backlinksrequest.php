<?php
/**
* A class representing API-requests for all pages linking a specific page
*
* The class BacklinksRequest allows the creation of API-requests for all pages linking to a given page
*
* @method void setPage(string $page)
* @method void setLimit(string $limit)
* @method void setContinue(string $continue)
* @method SimpleXMLElement execute()
*/
class BacklinksRequest extends Request {
	private string $page;
	private string $limit;
	private string $continue;
	
	/**
	* constructor for class BacklinksRequest
	*
	* @param string $url       the url to the wiki
	* @param string $page      the page that is linked
	* @param string $limit     the maximum amount of pages to be queried
	* @param string $continue  continue for additional queries
	* @access public
	*/
	public function __construct(string $url, string $page, string $limit = "max", string $continue = "") {
		$this->url = $url;
		$this->page = $page;
		$this->limit = $limit;
		$this->continue = $continue;
	}
	
	/**
	* setter for the name of the page
	*
	* @param string $page  the name of the page that should be set
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
		$request = new APIRequest($this->url);
		$request->setCookieFile($this->cookiefile);
		$request->setLogger($this->logger);
		$request->addToGetFields("action", "query");
		$request->addToGetFields("list", "backlinks");
		$request->addToGetFields("format", "xml");
		$request->addToGetFields("bltitle", $this->page);
		$request->addToGetFields("bllimit", $this->limit);
		// MediaWiki doesn't like set but empty blcontinue
		if(!empty($this->continue)) { $request->addToGetFields("blcontinue", $this->continue); }
		return $request->execute();
	}
}