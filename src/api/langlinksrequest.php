<?php
/**
* A class for representing API-requests for langlinks
*
* The class LanglinksRequest represents API-requests for langlinks of given pages
* Filtering only one particular language is possible
*
* @method void setTitles(string $titles)
* @method void setLang(string $lang)
* @method void setLimit(string $limit)
* @method void setContinue(string $continue)
*/
class LanglinksRequest extends Request {
	private string $titles;
	private string $lang;
	private string $limit;
	private string $continue;
	
	/**
	* constructor for class LanglinksRequest
	*
	* @param string $url       the url to the wiki
	* @param string $titles    the titles for which the langlinks should be queried
	* @param string $lang      a filter for a language. No filter means all languages
	* @param string $limit     the maximum amount of langlinks that should be queried
	* @param string $continue  continue value for additional queries
	* @access public
	*/
	public function __construct(
		string $url,
		string $titles,
		string $lang = "",
		string $limit = "max",
		string $continue = ""
	) {
		$this->url = $url;
		$this->titles = $titles;
		$this->lang = $lang;
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
	* setter for the language filter
	*
	* @param string $lang  the language that should be set as the filter
	* @access public
	*/
	public function setLang(string $lang) : void {
		$this->lang = $lang;
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
	* setter for continue value
	*
	* @param string $continue  the continue that should be set
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
		$request->addToGetFields("prop", "langlinks");
		$request->addToGetFields("format", "xml");
		$request->addToGetFields("lllimit", $this->limit);
		// only set filter lang if set
		if(!empty($this->lang)) { $request->addToGetFields("lllang", $this->lang); }
		// MediaWiki doesn't like empty but set llcontinue
		if(!empty($this->continue)) { $request->addToGetFields("llcontinue", $this->continue); }
		$request->addToPostFields("titles", $this->titles);
		return $request->execute();
	}
}