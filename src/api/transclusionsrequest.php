<?php
/**
* A class for representing API-requests for all pages transcluding a specific page
*
* The class TransclusionsRequest allows the creation of API-requests for all pages transcluding a given page
*
* @method void setPage(String $page)
* @method void setLimit(String $limit)
* @method void setContinue(String $continue)
* @method SimpleXMLElement execute()
*/
class TransclusionsRequest extends Request {
	private String $page;
	private String $limit;
	private String $continue;
	
	/**
	* constructor for class TransclusionsRequest
	*
	* @param String $url       the url to the wiki
	* @param String $page      the page that is transcluded
	* @param String $limit     the maximum amount of pages to be queried
	* @param String $continue  continue for additional queries
	* @access public
	*/
	public function __construct(String $url, String $page, String $limit = "max", String $continue = "") {
		$this->url = $url;
		$this->page = $page;
		$this->limit = $limit;
		$this->continue = $continue;
	}
	
	/**
	* setter for the name of the page
	*
	* @param String $page
	* @access public
	*/
	public function setPage(String $page) : void {
		$this->page = $page;
	}
	
	/**
	* setter for the limit
	*
	* @param String $limit  the limit that should be set
	* @access public
	*/
	public function setLimit(String $limit) : void {
		$this->limit = $limit;
	}
	
	/**
	* setter for the continue value
	*
	* @param String $continue
	* @access public
	*/
	public function setContinue(String $continue) : void {
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
		$request->addToGetFields("list", "embeddedin");
		$request->addToGetFields("format", "xml");
		$request->addToGetFields("eititle", $this->page);
		$request->addToGetFields("eilimit", $this->limit);
		// MediaWiki doesn't like set but empty eicontinue
		if(!empty($this->continue)) { $request->addToGetFields("eicontinue", $this->continue); }
		return $request->execute();
	}
}