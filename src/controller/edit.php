<?php
/**
* A class for edits
*
* The class Edit allows performing edits on a wiki
*
* @method void setContent(string $content)
* @method void setRevision(int $revision)
* @method void setComment(string $comment)
* @method void setIsMinor(bool $isMinor)
* @method void setIsBot(bool $isBot)
* @method Revision exeucte()
* @method CurlHandle getRequest()
* @method Revision parseResult(SimpleXMLElement $queryResult)
*/
class Edit {
	private Bot $bot;
	private string $page;
	private string $content;
	private int $revision;
	private string $comment;
	private bool $isMinor;
	private bool $isBot;
	
	/**
	* constructor for class Edit
	*
	* @param Bot $bot         a reference to the bot object
	* @param string $page     the name of the page
	* @param string $comment  the comment for the edit
	* @param bool $isMinor    true if the edit should be considered minor, false otherwise
	* @param bool $isBot      true if the edit should be considered a bot edit, false otherwise
	* @access public
	*/
	public function __construct(Bot &$bot, string $page, string $comment = "", bool $isMinor = true, bool $isBot = true) {
		$this->bot = $bot;
		$this->page = $page;
		$this->comment = $comment;
		$this->isMinor = $isMinor;
		$this->isBot = $isBot;
	}
	
	/**
	* setter for the content
	*
	* @param string $content  the content to set
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
	* setter for the comment
	*
	* @param string $comment  the comment to set
	* @access public
	*/
	public function setComment(string $comment) : void {
		$this->comment = $comment;
	}
	
	/**
	* setter for if the edit is minor
	*
	* @param bool $isMinor  true if the edit should be marked as minor, false otherwise
	* @access public
	*/
	public function setIsMinor(bool $isMinor) : void {
		$this->isMinor = $isMinor;
	}
	
	/**
	* setter for if the edit should be considered a bot edit
	*
	* @param bool $isBot  true if the edit should considered a bot edit, false otherwise
	* @access public
	*/
	public function setIsBot(bool $isBot) : void {
		$this->isBot = $isBot;
	}
	
	/**
	* executor for edit
	*
	* @return Revision  a revision representing the edit
	* @access public
	*/
	public function execute() : Revision {
		$request = new EditRequest(
			$this->bot->getUrl(),
			$this->page,
			$this->bot->getToken("csrf"),
			$this->comment,
			$this->isMinor,
			$this->isBot
		);
		$request->setCookieFile($this->bot->getCookieFile());
		$request->setLogger($this->bot->getLogger());
		if(isset($this->content)) { $request->setContent($this->content); }
		if(isset($this->revision)) { $request->setRevision($this->revision); }
		$queryResult = $request->execute();
		
		return $this->parseResult($queryResult);
	}
	
	/**
	* getter for the request of an edit
	*
	* @return CurlHandle  a reference to the request handle
	* @access public
	*/
	public function &getRequest() : CurlHandle {
		$request = new EditRequest(
			$this->bot->getUrl(),
			$this->page,
			$this->bot->getToken("csrf"),
			$this->comment,
			$this->isMinor,
			$this->isBot
		);
		$request->setCookieFile($this->bot->getCookieFile());
		$request->setLogger($this->bot->getLogger());
		if(isset($this->content)) { $request->setContent($this->content); }
		if(isset($this->revision)) { $request->setRevision($this->revision); }
		return $request->getRequest();
	}
	
	/**
	* parse result into revision object
	* called automatically on execution of request
	* can be called manually when using multirequest for execution
	*
	* @param SimpleXMLElement $queryResult  the result of the edit returned by the api
	* @return Revision                      the revision representing the edit result
	* @access public
	*/
	public function parseResult(SimpleXMLElement $queryResult) : Revision {
		if(!isset($queryResult->edit["result"]) || (string)$queryResult->edit["result"] !== "Success") {
			throw new Exception("Error on edit");
		}
		
		$revision = new Revision((int)$queryResult->edit["newrevid"]);
		$revision->setParentId((int)$queryResult->edit["oldrevid"]);
		$revision->setTimestamp(strtotime((string)$queryResult->edit["newtimestamp"]));
		$page = new Page((string)$queryResult->edit["title"]);
		$page->setId((int)$queryResult->edit["pageid"]);
		$revision->setPage($page);
		return $revision;
	}
}