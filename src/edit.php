<?php
spl_autoload_register(function($class) {require(strtolower($class).".php");});

/**
* A class for representing API-requests of edits
*
* The class Edit represents API-requests for editing a wiki page
*
* @method void setPage(String $page)
* @method void setContent(String $content)
* @method void setSummary(String $summary)
* @method void setIsBot(String $isbot)
* @method void setIsMinor(String $isminor)
* @method SimpleXMLElement execute()
* @method CurlHandle getRequest(String $token)
*/
class Edit extends Request {
	private String $page;
	private String $content;
	private String $summary;
	private String $isbot;
	private String $isminor;
	
	/**
	* constructor for class Edit
	*
	* @param String $url      the url to the wiki
	* @param String $page     the page that will be edited
	* @param String $content  the new content of the page
	* @param String $summary  the summary of the edit
	* @param String $isbot    if the account editing is a bot or not
	* @param String $isminor  if the edit should be marked as minor or not
	* @access public
	*/
	public function __construct(String $url, String $page, String $content, String $summary = "", String $isbot = "1", String $isminor = "1") {
		$this->url = $url;
		$this->page = $page;
		$this->content = $content;
		$this->summary = $summary;
		$this->isbot = $isbot;
		$this->isminor = $isminor;
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
	* setter for the content
	*
	* @param String $content  the content that should be set
	* @access public
	*/
	public function setContent(String $content) {
		$this->content = $content;
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
	public function setIsBot(String $isbot) {
		$this->isbot = $isbot;
	}
	
	/**
	* setter for whether the edit should be marked as minor or not
	*
	* @param String $isminor  the new value
	* @access public
	*/
	public function setIsMinor(String $isminor) {
		$this->isminor = $isminor;
	}
	
	/**
	* executor for the API-request
	*
	* @param String $token      the token required for editing a page
	* @return SimpleXMLElement  the SimpleXMLElement-representation of the query result
	* @access public
	*/
	public function execute(String $token) {
		$edit = new APIRequest($this->url);
		$edit->setCookieFile($this->cookiefile);
		$edit->addToGetFields("action", "edit");
		$edit->addToGetFields("format", "xml");
		$edit->addToGetFields("title", $this->page);
		$edit->addToPostFields("text", $this->content);
		$edit->addToPostFields("summary", $this->summary);
		$edit->addToPostFields("bot", $this->isbot);
		$edit->addToPostFields("minor", $this->isminor);
		$edit->addToPostFields("token", $token);
		return $edit->execute();
	}
	
	/**
	* getter for an API-request of an edit
	*
	* @param String $token  the token required for editing a page
	* @return CurlHandle    a reference to the request handle
	* @access public
	*/
	public function &getRequest(String $token) {
		$edit = new APIRequest($this->url);
		$edit->setCookieFile($this->cookiefile);
		$edit->addToGetFields("action", "edit");
		$edit->addToGetFields("format", "xml");
		$edit->addToGetFields("title", $this->page);
		$edit->addToPostFields("text", $this->content);
		$edit->addToPostFields("summary", $this->summary);
		$edit->addToPostFields("bot", $this->isbot);
		$edit->addToPostFields("minor", $this->isminor);
		$edit->addToPostFields("token", $token);
		return $edit->getRequest();
	}
}
?>