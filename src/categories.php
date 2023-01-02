<?php
spl_autoload_register(function($class) {require(strtolower($class).".php");});

/**
* A class for representing API-requests for all categories of one or multiple oages
*
* The class Categories allows the creation of API-requests for all categories of one or multiple given pages
*
* @method void setPages(Array $categories)
* @method void setLimit(String $limit)
* @method void setContinue(String $continue)
* @method void setFilter(Array $filter)
* @method SimpleXMLElement execute()
*/
class Categories extends Request {
	private Array $pages;
	private String $limit;
	private ?String $continue;
	private Array $filter;
	
	/**
	* constructor for class Categories
	*
	* @param String $url       the url to the wiki
	* @param Array $pages      the pages for which to query the categories
	* @param String $limit     limit for the maximum amount of categories requested
	* @param String $continue  continue for additional queries
	* @param Array $filter     an array of categories for which to filter for. Will only return these pages. Can be used to check if a page is in a specific category
	* @access public
	*/
	public function __construct(String $url, Array $pages, String $limit = "max", ?String $continue = "", Array $filter = array()) {
		$this->url = $url;
		$this->pages = $pages;
		$this->limit = $limit;
		$this->continue = $continue;
		$this->filter = $filter;
		$this->cookiefile = "cookies.txt";
	}
	
	/**
	* setter for the pages
	*
	* @param Array $pages  the pages that should be set
	* @access public
	*/
	public function setPages(Array $pages) {
		$this->pages = $pages;
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
	* setter for the continue
	*
	* @param String $continue  the value that should be set for continuation
	* @access public
	*/
	public function setContinue(String $continue) {
		$this->continue = $continue;
	}
	
	/**
	* setter for the filter
	*
	* @param Array $filter  the filter that should be set
	* @access public
	*/
	public function setFilter(Array $filter) {
		$this->filter = $ilter;
	}
	
	/**
	* executor for the API-request
	*
	* @return SimpleXMLElement  the SimpleXMLElement-representation of the query result
	* @access public
	*/
	public function execute() {
		$categories = new APIRequest($this->url);
		$categories->setCookieFile($this->cookiefile);
		$categories->setLogger($this->logger);
		$categories->addToGetFields("action", "query");
		$categories->addToGetFields("prop", "categories");
		$categories->addToGetFields("format", "xml");
		$categories->addToPostFields("titles", implode("|", $this->pages));
		if(!empty($this->continue)) { $categories->addToGetFields("clcontinue", $this->continue); } // MediaWiki doesn't like empty uccontinue
		$categories->addToGetFields("cllimit", $this->limit);
		$categories->addToPostFields("clcategories", implode("|", $this->filter));
		return $categories->execute();
	}
}