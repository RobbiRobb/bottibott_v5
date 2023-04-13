<?php
/**
* A class representing API-requests for all pages in a namespace
*
* The class AllpagesRequest allows the creation of API-requests for all pages in a given namespace of a wiki
*
* @method void setNamespace(int $namespace)
* @method void setLimit(string $limit)
* @method void setFilter(string $filter)
* @method void setContinue(string $continue)
* @method SimpleXMLElement execute()
*/
class AllpagesRequest extends Request {
	private int $namespace;
	private string $limit;
	private string $filter;
	private string $continue;
	
	/**
	* constructor for class AllpagesRequest
	*
	* @param string $url        the url to the wiki
	* @param int $namespace     the namespace to query from
	* @param string $limit      limit for the maximum amount of pages requested
	* @param string $filter     filter for the type of pages. Allowed values are "all", "redirects" and "nonredirects"
	* @param string $continue   continue for additional queries
	* @access public
	*/
	public function __construct(
		string $url,
		int $namespace,
		string $limit = "max",
		string $filter = "all",
		string $continue = ""
	) {
		$this->url = $url;
		$this->namespace = $namespace;
		$this->limit = $limit;
		$this->filter = $filter;
		$this->continue = $continue;
	}
	
	/**
	* setter for the namespace
	*
	* @param int $namespace  the namespace that should be set
	* @access public
	*/
	public function setNamespace(int $namespace) : void {
		$this->namespace = $namespace;
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
	
	/*
	* setter for the filter
	*
	* @param string $filter  the filter that should be used. Allowed values are "all", "redirects" and "nonredirects"
	* @access public
	*/
	public function setFilter(string $filter) : void {
		$this->filter = $filter;
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
		$request->addToGetFields("list", "allpages");
		$request->addToGetFields("format", "xml");
		$request->addToGetFields("apnamespace", $this->namespace);
		$request->addToGetFields("apcontinue", $this->continue);
		$request->addToGetFields("aplimit", $this->limit);
		$request->addToGetFields("apfilterredir", $this->filter);
		return $request->execute();
	}
	
}