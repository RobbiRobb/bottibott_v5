<?php
spl_autoload_register(function($class) {require(strtolower($class).".php");});

/**
* A class for representing API-requests for deleting a page
*
* The class Delete allows the creation of API-requests for deleting a page on a wiki
*
* @method void setTitle(String $title)
* @method void setReason(String $reason)
* @method SimpleXMLElement execute(String $token)
*/
class Delete extends Request {
	private String $title;
	private String $reason;
	
	/**
	* constructor for class Delete
	*
	* @param String $url     the url to the wiki
	* @param String $title   the title of the page that should be deleted
	* @param String $reason  the reason why the page should be deleted
	* @access public
	*/
	public function __construct(String $url, String $title, String $reason = "") {
		$this->url = $url;
		$this->title = $title;
		$this->reason = $reason;
		$this->cookiefile = "cookies.txt";
	}
	
	/**
	* setter for the title
	*
	* @param String $title  the title that should be set
	* @access public
	*/
	public function setTitle(String $title) {
		$this->title = $title;
	}
	
	/**
	* setter for the reason
	*
	* @param String $reason  the reason that should be set
	* @access public
	*/
	public function setReason(String $reason) {
		$this->reason = $reason;
	}
	
	/**
	* executor for the API-request
	*
	* @param String $token      the token required for deleting a page
	* @return SimpleXMLElement  the SimpleXMLElement-representation of the query result
	* @access public
	*/
	public function execute(String $token) {
		$delete = new APIRequest($this->url);
		$delete->setCookieFile($this->cookiefile);
		$delete->addToGetFields("action", "delete");
		$delete->addToGetFields("format", "xml");
		$delete->addToGetFields("title", $this->title);
		$delete->addToGetFields("reason", $this->reason);
		$delete->addToPostFields("token", $token);
		return $delete->execute();
	}
}
?>