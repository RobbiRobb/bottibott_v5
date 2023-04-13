<?php
/**
* A class for representing API-requests of edits
*
* The class EditRequest represents API-requests for editing a wiki page
*
* @method void setContent(string $content)
* @method void setRevision(int $revision)
* @method void setSummary(string $summary)
* @method void setIsBot(bool $isBot)
* @method void setIsMinor(bool $isMinor)
* @method SimpleXMLElement execute()
* @method CurlHandle getRequest()
*/
class EditRequest extends Request {
	private string $page;
	private string $content;
	private int $revision;
	private string $token;
	private string $summary;
	private bool $isMinor;
	private bool $isBot;
	
	/**
	* constructor for class EditRequest
	*
	* @param string $url      the url to the wiki
	* @param string $page     the page that will be edited
	* @param string $token    a csrf token for editing
	* @param string $summary  the summary of the edit
	* @param bool $isMinor    if the edit should be marked as minor or not
	* @param bool $isBot      if the account editing is a bot or not
	* @access public
	*/
	public function __construct(
		string $url,
		string $page,
		string $token,
		string $summary = "",
		bool $isMinor = true,
		bool $isBot = true
	) {
		$this->url = $url;
		$this->page = $page;
		$this->token = $token;
		$this->summary = $summary;
		$this->isMinor = $isMinor;
		$this->isBot = $isBot;
	}
	
	/**
	* setter for the content
	*
	* @param string $content  the content that should be set
	* @access public
	*/
	public function setContent(string $content) : void {
		$this->content = $content;
	}
	
	/**
	* setter for the revision
	*
	* @param int $revision  the revision to set
	* @access public
	*/
	public function setRevision(int $revision) : void {
		$this->revision = $revision;
	}
	
	/**
	* setter for the summary
	*
	* @param string $summary  the summary that should be set
	* @access public
	*/
	public function setSummary(string $summary) : void {
		$this->summary = $summary;
	}
	
	/**
	* setter for whether the account editing is a bot or not
	*
	* @param bool $isBot  the new value
	* @access public
	*/
	public function setIsBot(bool $isBot) : void {
		$this->isBot = $isBot;
	}
	
	/**
	* setter for whether the edit should be marked as minor or not
	*
	* @param bool $isMinor  the new value
	* @access public
	*/
	public function setIsMinor(bool $isMinor) : void {
		$this->isMinor = $isMinor;
	}
	
	/**
	* executor for the API-request
	*
	* @return SimpleXMLElement  the SimpleXMLElement-representation of the query result
	* @access public
	*/
	public function execute() : SimpleXMLElement {
		if(isset($this->content) && isset($this->revision)) { throw new Error("Can not set both content and revision"); }
		if(!isset($this->content) && !isset($this->revision)) { throw new Error("Either content or revision must be set"); }
		$request = new APIRequest($this->url);
		$request->setCookieFile($this->cookiefile);
		$request->setLogger($this->logger);
		$request->addToGetFields("action", "edit");
		$request->addToGetFields("format", "xml");
		$request->addToGetFields("title", $this->page);
		// only add text if content is set
		if(isset($this->content)) { $request->addToPostFields("text", $this->content); }
		// only add revision if is set
		if(isset($this->revision)) { $request->addToPostFields("undo", $this->revision); }
		$request->addToPostFields("summary", $this->summary);
		$request->addToPostFields("minor", ($this->isMinor ? "1" : "0"));
		$request->addToPostFields("bot", ($this->isBot ? "1" : "0"));
		$request->addToPostFields("token", $this->token);
		return $request->execute();
	}
	
	/**
	* getter for an API-request of an edit
	*
	* @return CurlHandle  a reference to the request handle
	* @access public
	*/
	public function &getRequest() : CurlHandle {
		if(isset($this->content) && isset($this->revision)) { throw new Error("Can not set both content and revision"); }
		if(!isset($this->content) && !isset($this->revision)) { throw new Error("Either content or revision must be set"); }
		$request = new APIRequest($this->url);
		$request->setCookieFile($this->cookiefile);
		$request->addToGetFields("action", "edit");
		$request->addToGetFields("format", "xml");
		$request->addToGetFields("title", $this->page);
		// only add text if content is set
		if(isset($this->content)) { $request->addToPostFields("text", $this->content); }
		// only add revision if set
		if(isset($this->revision)) { $request->addToPostFields("undo", $this->revision); }
		$request->addToPostFields("summary", $this->summary);
		$request->addToPostFields("minor", $this->isMinor);
		$request->addToPostFields("bot", $this->isBot);
		$request->addToPostFields("token", $this->token);
		return $request->getRequest();
	}
}