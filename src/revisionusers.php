<?php
spl_autoload_register(function($class) {require(strtolower($class).".php");});

/**
* A class for representing API-requests for users of revisions
*
* The class RevisionUsers allows the representation of API-requests for all users who edited a given page
*
* @method void setPage(String $page)
* @method void setLimit(String $limit)
* @method void setContinue(String $continue)
* @method SimpleXMLElement execute()
*/
class RevisionUsers extends Request {
	private String $page;
	private String $limit;
	private String $continue;
	
	/**
	* constructor for class RevisionUsers
	*
	* @param String $url       the url to the wiki
	* @param String $page      the page which revisions should be loaded
	* @param String $limit     the maximum amount of revisions to query
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
	* setter for the page
	*
	* @param String $page  the page that should be set
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
		$revisionusers = new APIRequest($this->url);
		$revisionusers->setCookieFile($this->cookiefile);
		$revisionusers->addToGetFields("action", "query");
		$revisionusers->addToGetFields("prop", "revisions");
		$revisionusers->addToGetFields("rvprop", "user");
		$revisionusers->addToGetFields("format", "xml");
		$revisionusers->addToGetFields("rvlimit", $this->limit);
		if(!empty($this->continue)) { $revisionusers->addToGetFields("rvcontinue", $this->continue); }
		$revisionusers->addToPostFields("titles", $this->page);
		return $revisionusers->execute();
	}
}
?>