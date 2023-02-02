<?php
/**
* A class for representing API-requests for all pages transcluding a specific page
*
* The class Transclusions allows the creation of API-requests for all pages transcluding a given page
*
* @method void setPage(String $page)
* @method void setLimit(String $limit)
* @method void setContinue(String $continue)
* @method SimpleXMLElement execute()
*/
class Transclusions extends Request {
	private String $page;
	private String $limit;
	private String $continue;
	
	/**
	* constructor for class Transclusions
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
		$this->cookiefile = "cookies.txt";
	}
	
	/**
	* setter for the name of the page
	*
	* @param String $page
	* @access public
	*/
	public function setPage(String $page) {
		$this->page = $page;
	}
	
	/**
	* setter for the limit
	*
	* @param String $limit  the limit that should be set
	* @access public
	*/
	public function setLimit(String $limit) {
		$this->limit = $limit;
	}
	
	/**
	* setter for the continue value
	*
	* @param String $continue
	* @access public
	*/
	public function setContinue(String $continue) {
		$this->continue = $continue;
	}
	
	/**
	* executor for the API-request
	*
	* @return SimpleXMLElement  the SimpleXMLElement-representation of the query result
	* @access public
	*/
	public function execute() {
		$transclusions = new APIRequest($this->url);
		$transclusions->setCookieFile($this->cookiefile);
		$transclusions->setLogger($this->logger);
		$transclusions->addToGetFields("action", "query");
		$transclusions->addToGetFields("list", "embeddedin");
		$transclusions->addToGetFields("format", "xml");
		$transclusions->addToGetFields("eititle", $this->page);
		$transclusions->addToGetFields("eilimit", $this->limit);
		if(!empty($this->continue)) { $transclusions->addToGetFields("eicontinue", $this->continue); } // MediaWiki doesn't like set but empty eicontinue
		return $transclusions->execute();
	}
}
?>