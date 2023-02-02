<?php
/**
* A class for representing API-requests for langlinks
*
* The class Langlinks represents API-requests for langlinks of given pages. Filtering only one particular language is possible
*
* @method void setTitles(String $titles)
* @method void setLang(String $lang)
* @method void setLimit(String $limit)
*/
class Langlinks extends Request {
	private String $titles;
	private String $lang;
	private String $limit;
	
	/**
	* constructor for class Langlinks
	*
	* @param String $url     the url to the wiki
	* @param String $titles  the titles for which the langlinks should be queried
	* @param String $lang    a filter for a language. No filter means all languages
	* @param String $limit   the maximum amount of langlinks that should be queried
	* @access public
	*/
	public function __construct(String $url, String $titles, String $lang = "", String $limit = "max") {
		$this->url = $url;
		$this->titles = $titles;
		$this->lang = $lang;
		$this->limit = $limit;
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
	* setter for the language filter
	*
	* @param String $lang  the language that should be set as the filter
	* @access public
	*/
	public function setLang(String $lang) {
		$this->lang = $lang;
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
	* executor for the API-request
	*
	* @return SimpleXMLElement  the SimpleXMLElement-representation of the query result
	* @access public
	*/
	public function execute() {
		$langlinks = new APIRequest($this->url);
		$langlinks->setCookieFile($this->cookiefile);
		$langlinks->setLogger($this->logger);
		$langlinks->addToGetFields("action", "query");
		$langlinks->addToGetFields("prop", "langlinks");
		$langlinks->addToGetFields("format", "xml");
		$langlinks->addToGetFields("lllimit", $this->limit);
		if(!empty($this->lang)) { $langlinks->addToGetFields("lllang", $this->lang); }
		$langlinks->addToPostFields("titles", $this->titles);
		return $langlinks->execute();
	}
}
?>