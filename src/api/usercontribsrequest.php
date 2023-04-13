<?php
/**
* A class for representing API-requests for all contributions a user has done
*
* The class UsercontribsRequest represents API-requests for all contributions a user has done on a wiki
* The returned data includes the timestamp, the difference in size and the title of each contribution
*
* @method void setUser(string $user)
* @method void setLimit(string $limit)
* @method void setContinue(string $continue)
* @method SimpleXMLElement execute()
*/
class UsercontribsRequest extends Request {
	private string $user;
	private string $limit;
	private string $continue;
	
	/**
	* constructor for class UsercontribsRequest
	*
	* @param string $url       the url to the wiki
	* @param string $user      the user for which to query the contributions for
	* @param string $limit     limit for the maximum amount of contributions requested
	* @param string $continue  continue for additional queries
	* @access public
	*/
	public function __construct(string $url, string $user, string $limit = "max", string $continue = "") {
		$this->url = $url;
		$this->user = $user;
		$this->limit = $limit;
		$this->continue = $continue;
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
		$request->addToGetFields("list", "usercontribs");
		$request->addToGetFields("ucprop", "ids|sizediff|timestamp|title");
		$request->addToGetFields("format", "xml");
		$request->addToGetFields("uclimit", $this->limit);
		// MediaWiki doesn't like empty uccontinue
		if(!empty($this->continue)) { $request->addToGetFields("uccontinue", $this->continue); }
		$request->addToPostFields("ucuser", $this->user);
		return $request->execute();
	}
}