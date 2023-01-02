<?php
spl_autoload_register(function($class) {require(strtolower($class).".php");});

/**
* A class for representing API-requests for all members of a category
*
* The class Categorymembers allows the creation of API-requests for all members of a given category
*
* @method void setCategory(String $category)
* @method void setLimit(String $limit)
* @method void setTypes(Array $types)
* @method void setContinue(String $continue)
* @method SimpleXMLElement execute()
*/
class Categorymembers extends Request {
	private String $category;
	private String $limit;
	private Array $types;
	private String $continue;
	
	/**
	* constructor for class Categorymembers
	*
	* @param String $url       the url to the wiki
	* @param String $category  the category to query from
	* @param String $limit     limit for the maximum amount of pages requested
	* @param Array $types      types that should be queried. May contain any but at least one of "page", "subcat" or "file"
	* @param String $continue  continue for additional queries
	* @access public
	*/
	public function __construct(String $url, String $category, String $limit = "max", Array $types = array("page", "subcat", "file"), String $continue = "") {
		$this->url = $url;
		$this->category = $category;
		$this->limit = $limit;
		$this->types = $types;
		$this->continue = $continue;
		$this->cookiefile = "cookies.txt";
	}
	
	/**
	* setter for the category
	*
	* @param String $category  the category that should be set
	* @access public
	*/
	public function setCategory(String $category) {
		$this->category = $category;
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
	* setter for the types
	*
	* @param Array $types  the types that should be set. May contain any but at least one of "page", "subcat" or "file"
	* @access public
	*/
	public function setTypes(Array $types) {
		$this->types = $types;
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
		$categorymembers = new APIRequest($this->url);
		$categorymembers->setCookieFile($this->cookiefile);
		$categorymembers->setLogger($this->logger);
		$categorymembers->addToGetFields("action", "query");
		$categorymembers->addToGetFields("list", "categorymembers");
		$categorymembers->addToGetFields("format", "xml");
		$categorymembers->addToGetFields("cmtitle", "Category:".$this->category);
		$categorymembers->addToGetFields("cmcontinue", $this->continue);
		$categorymembers->addToGetFields("cmlimit", $this->limit);
		$categorymembers->addToGetFields("cmtype", implode("|", $this->types));
		return $categorymembers->execute();
	}
}
?>