<?php
spl_autoload_register(function($class) {require(strtolower($class).".php");});

/**
* A class for representing API-requests for reverting edits
*
* The class Undo allows the creation of API-requests for reverting edits specified by a revision id
*
* @method void setPage(String $page)
* @method void setRevision(String $revision)
* @method void setSummary(String $summary)
* @method void setIsminor(String $isminor)
* @method void setIsbot(String $isbot)
*/
class Undo extends Request {
	private String $page;
	private String $revision;
	private String $summary;
	private String $isbot;
	private String $isminor;
	
	/**
	* constructor for class Undo
	*
	* @param String $url       the url to the wiki
	* @param String $page      the page on which the revision will be reverted
	* @param String $revision  the revision id for the edit that should be reverted
	* @param String $summary   the summary that will be added when reverting
	* @param String $isbot     whether the edit should be marked as done by a bot or not
	* @param String $isminor   whether the edit should be marked as minor or not
	* @access public
	*/
	public function __construct(String $url, String $page, String $revision, String $summary = "", String $isbot = "1", String $isminor = "1") {
		$this->url = $url;
		$this->page = $page;
		$this->revision = $revision;
		$this->summary = $summary;
		$this->isminor = $isminor;
		$this->isbot = $isbot;
		$this->cookiefile = "cookies.txt";
	}
	
	/**
	* setter for the page
	*
	* @param String $page  the page that should be set
	* @access public
	*/
	public function setPage(String $page) {
		$this->page = $page;
	}
	
	/**
	* setter for the revision
	*
	* @param String $revision  the revision that should be set
	* @access public
	*/
	public function setRevision(String $revision) {
		$this->revision = $revision;
	}
	
	/**
	* setter for the summary
	*
	* @param String $summary  the summary that should be set
	* @access public
	*/
	public function setSummary(String $summary) {
		$this->summary = $summary;
	}
	
	/**
	* setter for whether the account editing is a bot or not
	*
	* @param String $isbot  the new value
	* @access public
	*/
	public function setIsbot(String $isbot) {
		$this->isbot = $isbot;
	}
	
	/**
	* setter for whether the edit should be marked as minor or not
	*
	* @param String $isminor  the new value
	* @access public
	*/
	public function setIsinor(String $isminor) {
		$this->isminor = $isminor;
	}
	
	/**
	* executor for the API-request
	*
	* @param String $token      the token required for reverting a revision
	* @return SimpleXMLElement  the SimpleXMLElement-representation of the query result
	* @access public
	*/
	public function execute(String $token) {
		$undo = new APIRequest($this->url);
		$undo->setCookieFile($this->cookiefile);
		$undo->addToGetFields("action", "edit");
		$undo->addToGetFields("format", "xml");
		$undo->addToPostFields("title", $this->page);
		$undo->addToPostFields("undo", $this->revision);
		$undo->addToPostFields("summary", $this->summary);
		$undo->addToPostFields("minor", $this->isminor);
		$undo->addToPostFields("bot", $this->isbot);
		$undo->addToPostFields("token", $token);
		return $undo->execute();
	}
}
?>