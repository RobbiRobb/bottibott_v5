<?php
/**
* A class for representing API-requests for all registered users on a wiki
*
* The class AllusersRequest allows the representation of API-requests for all registered users on a wiki
*
* @method void setLimit(string $limit)
* @method void setContinue(string $continue)
* @method void setActive (bool $active)
* @method SimpleXMLElement execute()
*/
class AllusersRequest extends Request {
	private string $limit;
	private string $continue;
	private bool $active;
	
	/**
	* constructor for class AllusersRequest
	*
	* @param string $url       the url to the wiki
	* @param string $continue  continue for additional queries
	* @param string $limit     the maximum amount of users requested
	* @access public
	*/
	public function __construct(string $url, string $limit = "max", string $continue = "") {
		$this->url = $url;
		$this->continue = $continue;
		$this->limit = $limit;
		$this->active = false;
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
	* setter for active value
	* setting this to true will only return users who have actions in the last 90 days
	*
	* @param bool $active  new value for active
	* @access public
	*/
	public function setActive(bool $active) : void {
		$this->active = $active;
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
		$request->addToGetFields("list", "allusers");
		$request->addToGetFields("format", "xml");
		$request->addToGetFields("auprop", "editcount|registration");
		$request->addToGetFields("aufrom", $this->continue);
		$request->addToGetFields("aulimit", $this->limit);
		// only set active users if requested
		if($this->active) { $request->addToGetFields("auactiveusers", $this->active); }
		return $request->execute();
	}
}