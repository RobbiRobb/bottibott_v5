<?php
/**
* A class for representing API-requests for all categories of one or multiple pages
*
* The class CategoriesRequest allows the creation of API-requests for all categories of one or multiple given pages
*
* @method void setPages(array $categories)
* @method void setLimit(string $limit)
* @method void setFilter(array $filter)
* @method void setContinue(string $continue)
* @method SimpleXMLElement execute()
*/
class CategoriesRequest extends Request {
	private array $pages;
	private string $limit;
	private string $continue;
	private array $filter;
	
	/**
	* constructor for class CategoriesRequest
	*
	* @param string $url       the url to the wiki
	* @param array $pages      the pages for which to query the categories
	* @param string $limit     limit for the maximum amount of categories requested
	* @param array $filter     an array of categories for which to filter for. Will only return these pages
	* @param string $continue  continue for additional queries
	* @access public
	*/
	public function __construct(
		string $url,
		array $pages,
		string $limit = "max",
		array $filter = array(),
		string $continue = ""
	) {
		$this->url = $url;
		$this->pages = $pages;
		$this->limit = $limit;
		$this->filter = $filter;
		$this->continue = $continue;
	}
	
	/**
	* setter for the pages
	*
	* @param array $pages  the pages that should be set
	* @access public
	*/
	public function setPages(array $pages) : void {
		$this->pages = $pages;
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
	* setter for the filter
	*
	* @param array $filter  the filter that should be set
	* @access public
	*/
	public function setFilter(array $filter) : void {
		$this->filter = $filter;
	}
	
	/**
	* setter for the continue
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
		$request->addToGetFields("prop", "categories");
		$request->addToGetFields("format", "xml");
		$request->addToPostFields("titles", implode("|", $this->pages));
		// MediaWiki doesn't like set but empty uccontinue
		if(!empty($this->continue)) { $request->addToGetFields("clcontinue", $this->continue); }
		$request->addToGetFields("cllimit", $this->limit);
		$request->addToPostFields("clcategories", implode("|", $this->filter));
		return $request->execute();
	}
}