<?php
/**
* A class representing API-requests for editcounts
*
* The class Systemeditcount allows the creation of API-requests for a users editcount that is saved in the database and can be viewed in the settings
* Oftentimes MediaWiki doesn't count correctly, resulting in the data queried by this class being off
*
* @method void setUsers(String $users)
* @method SimpleXMLElement execute()
*/
class Systemeditcount extends Request {
	private String $users;
	
	/**
	* constructor for class Systemeditcount
	*
	* @param String $url    the url to the wiki
	* @param String $users  the users for which the editcount should be queried
	* @access public
	*/
	public function __construct(String $url, String $users) {
		$this->url = $url;
		$this->users = $users;
		$this->cookiefile = "cookiefile.txt";
	}
	
	/**
	* setter for the users
	*
	* @param String $users  the users that should be set
	* @access public
	*/
	public function setUsers(String $users) {
		$this->users = $users;
	}
	
	/**
	* executor for the API-request
	*
	* @return SimpleXMLElement  the SimpleXMLElement-representation of the query result
	* @access public
	*/
	public function execute() {
		$editcount = new APIRequest($this->url);
		$editcount->setCookieFile($this->cookiefile);
		$editcount->setLogger($this->logger);
		$editcount->addToGetFields("action", "query");
		$editcount->addToGetFields("list", "users");
		$editcount->addToGetFields("usprop", "editcount");
		$editcount->addToGetFields("format", "xml");
		$editcount->addToPostFields("ususers", $this->users);
		return $editcount->execute();
	}
}
?>