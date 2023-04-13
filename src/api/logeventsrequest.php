<?php
/**
* A class for representing API-requests for all events logged
*
* The class LogeventsRequest allows the representation of API-requests for all events logged by MediaWiki
* Filtering by user and namespace is possible
*
* @method void setAction(string $action)
* @method void setUser(string $user)
* @method void setNamespace(string $namespace)
* @method void setLimit(string $limit)
* @method void setContinue(string $continue)
* @method SimpleXMLElement execute()
*/
class LogeventsRequest extends Request {
	private string $action;
	private string $user;
	private string $namespace;
	private string $limit;
	private string $continue;
	
	/**
	* constructor for class LogeventsRequest
	*
	* @param string $url        the url to the wiki
	* @param string $action     the action that should be queried
	* @param string $user       a filter for the user responsible for the event
	* @param string $namespace  a filter for the effected namespace
	* @param string $limit      the maximum amount of logevents to be queried
	* @param string $continue   continue for additional queries
	* @access public
	*/
	public function __construct(
		string $url,
		string $action,
		string $user = "",
		string $namespace = "",
		string $limit = "max",
		string $continue = ""
	) {
		$this->url = $url;
		$this->action = $action;
		$this->user = $user;
		$this->namespace = $namespace;
		$this->limit = $limit;
		$this->continue = $continue;
	}
	
	/**
	* setter for the action
	*
	* @param string $action  the action that should be set
	* @access public
	*/
	public function setAction(string $action) : void {
		$this->action = $action;
	}
	
	/**
	* setter for the user
	*
	* @param string $user  the user that should be set
	* @access public
	*/
	public function setUser(string $user) : void {
		$this->user = $user;
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
	
	/** setter for the limit
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
		$request->addToGetFields("list", "logevents");
		$request->addToGetFields("leprop", "timestamp|title|type|user|userid");
		$request->addToGetFields("format", "xml");
		$request->addToGetFields("leaction", $this->action);
		$request->addToGetFields("lelimit", $this->limit);
		// MediaWiki doesn't like empty but set lecontinue
		if(!empty($this->continue)) { $request->addToGetFields("lecontinue", $this->continue); }
		// MediaWiki doesn't like empty but set leuser
		if(!empty($this->user)) { $request->addToGetFields("leuser", $this->user); }
		// MediaWiki doesn't like empty but set lenamespace
		if(!empty($this->namespace) || $this->namespace === "0") { $request->addToGetFields("lenamespace", $this->namespace); }
		return $request->execute();
	}
}