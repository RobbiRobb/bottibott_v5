<?php
/**
* A class for representing API-requests for all contributions a user has done
*
* The class Usercontribs represents API-requests for all contributions a user has done on a wiki
* The returned data includes the timestamp, the difference in size and the title of each contribution
*
* @method void setUser(String $user)
* @method void setLimit(String $limit)
* @method void setContinue(String $continue)
* @method SimpleXMLElement execute()
*/
class Usercontribs extends Request {
	private String $user;
	private String $limit;
	private String $continue;
	
	/**
	* constructor for class Usercontribs
	*
	* @param String $url       the url to the wiki
	* @param String $user      the user for which to query the contributions for
	* @param String $limit     limit for the maximum amount of contributions requested
	* @param String $continue  continue for additional queries
	* @access public
	*/
	public function __construct(String $url, String $user, String $limit = "max", String $continue = "") {
		$this->url = $url;
		$this->user = $user;
		$this->limit = $limit;
		$this->continue = $continue;
		$this->cookiefile = "cookies.txt";
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
		$usercontribs = new APIRequest($this->url);
		$usercontribs->setCookieFile($this->cookiefile);
		$usercontribs->setLogger($this->logger);
		$usercontribs->addToGetFields("action", "query");
		$usercontribs->addToGetFields("list", "usercontribs");
		$usercontribs->addToGetFields("ucprop", "timestamp|sizediff|title");
		$usercontribs->addToGetFields("format", "xml");
		$usercontribs->addToGetFields("uclimit", $this->limit);
		if(!empty($this->continue)) { $usercontribs->addToGetFields("uccontinue", $this->continue); } // MediaWiki doesn't like empty uccontinue
		$usercontribs->addToPostFields("ucuser", $this->user);
		return $usercontribs->execute();
	}
}
?>