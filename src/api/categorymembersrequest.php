<?php
/**
* A class for representing API-requests for all members of a category
*
* The class CategorymembersRequest allows the creation of API-requests for all members of a given category
*
* @method void setCategory(string $category)
* @method void setLimit(string $limit)
* @method void setTypes(array $types)
* @method void setContinue(string $continue)
* @method SimpleXMLElement execute()
*/
class CategorymembersRequest extends Request {
	private string $category;
	private string $limit;
	private array $types;
	private string $continue;
	
	/**
	* constructor for class CategorymembersRequest
	*
	* @param string $url       the url to the wiki
	* @param string $category  the category to query from
	* @param string $limit     limit for the maximum amount of pages requested
	* @param array $types      types that should be queried. May contain any but at least one of "page", "subcat" or "file"
	* @param string $continue  continue for additional queries
	* @access public
	*/
	public function __construct(
		string $url,
		string $category,
		string $limit = "max",
		array $types = array("page", "subcat", "file"),
		string $continue = ""
	) {
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
	* @param string $category  the category that should be set
	* @access public
	*/
	public function setCategory(string $category) : void {
		$this->category = $category;
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
	* setter for the types
	*
	* @param array $types  the types that should be set. May contain any but at least one of "page", "subcat" or "file"
	* @access public
	*/
	public function setTypes(array $types) : void {
		$this->types = $types;
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
		$request->addToGetFields("list", "categorymembers");
		$request->addToGetFields("format", "xml");
		$request->addToGetFields("cmtitle", "Category:".$this->category);
		$request->addToGetFields("cmcontinue", $this->continue);
		$request->addToGetFields("cmlimit", $this->limit);
		$request->addToGetFields("cmtype", implode("|", $this->types));
		return $request->execute();
	}
}