<?php
/**
* A class for representing API-requests for all pages a page links to
*
* The class Links allows the representation of API-requests for all pages a given page links to
*
* @method void setTitles(String $titles)
* @method void setTargets(String $targets)
* @method void setNamespace(String $namespace)
* @method void setLimit(String $limit)
* @method void setContinue(String $continue)
* @method SimpleXMLElement execute()
*/
class Links extends Request {
	private String $titles;
	private String $targets;
	private String $namespace;
	private String $limit;
	private String $continue;
	
	/**
	* constructor for class Links
	*
	* @param String $url        the url to the wiki
	* @param String $titles     the pages for which the links should be queried
	* @param String $targets    filter for which link targets the links should be queried
	* @param String $namespace  filter for which namespace the links are pointing to
	* @param String $limit      the maximum amount of links queried
	* @param String $continue   continue for additional queries
	* @access public
	*/
	public function __construct(String $url, String $titles, String $targets = "", String $namespace = "*", String $limit = "max", String $continue = "") {
		$this->url = $url;
		$this->titles = $titles;
		$this->targets = $targets;
		$this->namespace = $namespace;
		$this->limit = $limit;
		$this->continue = $continue;
		$this->cookiefile = "cookies.txt";
	}
	
	/**
	* setter for the titles
	*
	* @param String $titles  the titles that should be set
	* @access public
	*/
	public function setTitles(String $titles) {
		$this->titles = $titles;
	}
	
	/**
	* setter for the targets
	*
	* @param String $targets  the targets that should be set
	* @access public
	*/
	public function setTargets(String $targets) {
		$this->targets = $targets;
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
		$links = new APIRequest($this->url);
		$links->setCookieFile($this->cookiefile);
		$links->setLogger($this->logger);
		$links->addToGetFields("action", "query");
		$links->addToGetFields("prop", "links");
		$links->addToGetFields("format", "xml");
		$links->addToGetFields("pltitles", $this->targets);
		$links->addToGetFields("plnamespace", $this->namespace);
		$links->addToGetFields("pllimit", $this->limit);
		if(!empty($this->continue)) { $links->addToGetFields("plcontinue", $this->continue); } // MediaWiki doesn't like empty plcontinue
		$links->addToPostFields("titles", $this->titles);
		return $links->execute();
	}
}
?>