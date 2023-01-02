<?php
spl_autoload_register(function($class) {require(strtolower($class).".php");});

/**
* A class for representing API-requests for all events logged
*
* The class Logevents allows the representation of API-requests for all events logged by MediaWiki
* Filtering by user and namespace is possible
*
* @method void setAction(String $action)
* @method void setUser(String $user)
* @method void setNamespace(String $namespace)
* @method void setLimit(String $limit)
* @method void setContinue(String $continue)
* @method SimpleXMLElement execute()
*/
class Logevents extends Request {
	private String $action;
	private String $user;
	private String $namespace;
	private String $limit;
	private String $continue;
	
	/**
	* constructor for class Logevents
	*
	* @param String $url        the url to the wiki
	* @param String $action     the action that should be queried
	* @param String $user       a filter for the user responsible for the event
	* @param String $namespace  a filter for the effected namespace
	* @param String $limit      the maximum amount of logevents to be queried
	* @param String $continue   continue for additional queries
	* @access public
	*/
	public function __construct(String $url, String $action, String $user = "", String $namespace = "", String $limit = "max", String $continue = "") {
		$this->url = $url;
		$this->action = $action;
		$this->user = $user;
		$this->namespace = $namespace;
		$this->limit = $limit;
		$this->continue = $continue;
		$this->cookiefile = "cookies.txt";
	}
	
	/**
	* setter for the action
	*
	* @param String $action  the action that should be set
	* @access public
	*/
	public function setAction(String $action) {
		$this->action = $action;
	}
	
	/**
	* setter for the user
	*
	* @param String $user  the user that should be set
	* @access public
	*/
	public function setUser(String $user) {
		$this->user = $user;
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
	
	/** setter for the limit
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
		$logevents = new APIRequest($this->url);
		$logevents->setCookieFile($this->cookiefile);
		$logevents->setLogger($this->logger);
		$logevents->addToGetFields("action", "query");
		$logevents->addToGetFields("list", "logevents");
		$logevents->addToGetFields("leprop", "user|type|title");
		$logevents->addToGetFields("format", "xml");
		$logevents->addToGetFields("leaction", $this->action);
		$logevents->addToGetFields("lelimit", $this->limit);
		if(!empty($this->continue)) { $logevents->addToGetFields("lecontinue", $this->continue); } // MediaWiki doesn't like empty lecontinue
		if(!empty($this->user)) { $logevents->addToGetFields("leuser", $this->user); } // MediaWiki doesn't like empty leuser
		if(!empty($this->namespace) || $this->namespace === "0") { $logevents->addToGetFields("lenamespace", $this->namespace); } // MediaWiki doesn't like empty lenamespace
		return $logevents->execute();
	}
}
?>