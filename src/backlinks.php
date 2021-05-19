<?php
spl_autoload_register(function($class) {require(strtolower($class).".php");});

/**
* A class representing API-requests for all pages linking a specific page
*
* The class Backlinks allows the creation of API-requests for all pages linking to a given page
*
* @method void setPage(String $page)
* @method void setLimit(String $limit)
* @method void setContinue(String $continue)
* @method SimpleXMLElement execute()
*/
class Backlinks extends Request {
	private String $page;
	private String $limit;
	private String $continue;
	
	/**
	* constructor for class Backlinks
	*
	* @param String $url       the url to the wiki
	* @param String $page      the page that is linked
	* @param String $limit     the maximum amount of pages to be queried
	* @param String $continue  continue for additional queries
	* @access public
	*/
	public function __construct(String $url, String $page, String $limit = "max", String $continue = "") {
		$this->url = $url;
		$this->page = $page;
		$this->limit = $limit;
		$this->continue = $continue;
		$this->cookiefile = "cookiefile.txt";
	}
	
	/**
	* setter for the name of the page
	*
	* @param String $page  the name of the page that should be set
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
	* @param String $continue  the value that should be set for continuation
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
		$backlinks = new APIRequest($this->url);
		$backlinks->setCookieFile($this->cookiefile);
		$backlinks->addToGetFields("action", "query");
		$backlinks->addToGetFields("list", "backlinks");
		$backlinks->addToGetFields("format", "xml");
		$backlinks->addToGetFields("bltitle", $this->page);
		$backlinks->addToGetFields("bllimit", $this->limit);
		if(!empty($this->continue)) { $backlinks->addToGetFields("blcontinue", $this->continue); } // MediaWiki doesn't like set but empty blcontinue
		return $backlinks->execute();
	}
}
?>