<?php
/**
* A class representing API-requests for all pages in a namespace
*
* The class Allpages allows the creation of API-requests for all pages in a given namespace of a wiki
*
* @method void setNamespace(String $namespace)
* @method void setLimit(String $limit)
* @method void setFilter(String $filter)
* @method void setContinue(String $continue)
* @method SimpleXMLElement execute()
*/
class Allpages extends Request {
	private String $namespace;
	private String $limit;
	private String $filter;
	private String $continue;
	
	/**
	* constructor for class Allpages
	*
	* @param String $url        the url to the wiki
	* @param String $namespace  the namespace to query from
	* @param String $limit      limit for the maximum amount of pages requested
	* @param String $filter     filter for the type of pages. Allowed values are "all", "redirects" and "nonredirects"
	* @param String $continue   continue for additional queries
	* @access public
	*/
	public function __construct(String $url, String $namespace, String $limit = "max", String $filter = "all", String $continue = "") {
		$this->url = $url;
		$this->namespace = $namespace;
		$this->limit = $limit;
		$this->filter = $filter;
		$this->continue = $continue;
		$this->cookiefile = "cookies.txt";
	}
	
	/**
	* setter for the namespace
	*
	* @param String $namespace  the namespace that should be set
	* @access public
	*/
	public function setNamespace(String $namespace) {
		$this->namespace = $namespace;
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
	
	/*
	* setter for the filter
	*
	* @param String $filter  the filter that should be used. Allowed values are "all", "redirects" and "nonredirects"
	* @access public
	*/
	public function setFilter(String $filter) {
		$this->filter = $filter;
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
		$allpages = new APIRequest($this->url);
		$allpages->setCookieFile($this->cookiefile);
		$allpages->setLogger($this->logger);
		$allpages->addToGetFields("action", "query");
		$allpages->addToGetFields("list", "allpages");
		$allpages->addToGetFields("format", "xml");
		$allpages->addToGetFields("apnamespace", $this->namespace);
		$allpages->addToGetFields("apcontinue", $this->continue);
		$allpages->addToGetFields("aplimit", $this->limit);
		$allpages->addToGetFields("apfilterredir", $this->filter);
		return $allpages->execute();
	}
	
}
?>