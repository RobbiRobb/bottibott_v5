<?php
/**
* A class for representing API-requests for all pages a page links to
*
* The class LinksRequest allows the representation of API-requests for all pages a given page links to
*
* @method void setTitles(string $titles)
* @method void setTargets(string $targets)
* @method void setNamespace(string $namespace)
* @method void setLimit(string $limit)
* @method void setContinue(string $continue)
* @method SimpleXMLElement execute()
*/
class LinksRequest extends Request {
	private string $titles;
	private string $targets;
	private string $namespace;
	private string $limit;
	private string $continue;
	
	/**
	* constructor for class LinksRequest
	*
	* @param string $url        the url to the wiki
	* @param string $titles     the pages for which the links should be queried
	* @param string $targets    filter for which link targets the links should be queried
	* @param string $namespace  filter for which namespace the links are pointing to
	* @param string $limit      the maximum amount of links queried
	* @param string $continue   continue for additional queries
	* @access public
	*/
	public function __construct(
		string $url,
		string $titles,
		string $targets = "",
		string $namespace = "*",
		string $limit = "max",
		string $continue = ""
	) {
		$this->url = $url;
		$this->titles = $titles;
		$this->targets = $targets;
		$this->namespace = $namespace;
		$this->limit = $limit;
		$this->continue = $continue;
	}
	
	/**
	* setter for the titles
	*
	* @param string $titles  the titles that should be set
	* @access public
	*/
	public function setTitles(string $titles) : void {
		$this->titles = $titles;
	}
	
	/**
	* setter for the targets
	*
	* @param string $targets  the targets that should be set
	* @access public
	*/
	public function setTargets(string $targets) : void {
		$this->targets = $targets;
	}
	
	/**
	* setter for the namespace
	*
	* @param string $namespace  the namespace that should be set
	* @access public
	*/
	public function setNamespace(string $namespace) : void {
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
		$request->addToGetFields("prop", "links");
		$request->addToGetFields("format", "xml");
		$request->addToGetFields("pltitles", $this->targets);
		$request->addToGetFields("plnamespace", $this->namespace);
		$request->addToGetFields("pllimit", $this->limit);
		// MediaWiki doesn't like empty plcontinue
		if(!empty($this->continue)) { $request->addToGetFields("plcontinue", $this->continue); }
		$request->addToPostFields("titles", $this->titles);
		return $request->execute();
	}
}