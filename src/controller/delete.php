<?php
/**
* A class for deleting pages
*
* The class Delete represents the deletion of a page on a wiki
*
* @method void setReason(string $reason)
* @method DeleteResult execute()
* @method CurlHandle getRequest()
* @method DeleteResult parseResult()
*/
class Delete {
	private Bot $bot;
	private string $title;
	private string $reason;
	
	/**
	* constructor for class Delete
	*
	* @param Bot $bot        a reference to the bot object
	* @param string $title   the title of the page that will be deleted
	* @param string $reason  the reason why the page will be deleted
	* @access public
	*/
	public function __construct(Bot &$bot, string $title, string $reason = "") {
		$this->bot = $bot;
		$this->title = $title;
		$this->reason = $reason;
	}
	
	/**
	* setter for the reason
	*
	* @param string $reason  the reason to set
	* @access public
	*/
	public function setReason(string $reason) : void {
		$this->reason = $reason;
	}
	
	/**
	* executor for deleting a page
	*
	* @return DeleteResult  a DeleteResult representing the deletion
	* @access public
	*/
	public function execute() : DeleteResult {
		$request = new DeleteRequest($this->bot->getUrl(), $this->title, $this->bot->getToken("csrf"), $this->reason);
		$request->setCookieFile($this->bot->getCookieFile());
		$request->setLogger($this->bot->getLogger());
		return $this->parseResult($request->execute());
	}
	
	/**
	* getter for the request of a deletion
	*
	* @return CurlHandle  a reference to the request handle
	* @access public
	*/
	public function &getRequest() : CurlHandle {
		$request = new DeleteRequest($this->bot->getUrl(), $this->title, $this->bot->getToken("csrf"), $this->reason);
		$request->setCookieFile($this->bot->getCookieFile());
		$request->setLogger($this->bot->getLogger());
		return $request->getRequest();
	}
	
	/**
	* parse result into DeleteResult object
	* called automatically on execution of request
	* can be called manually when using multirequest for execution
	*
	* @param SimpleXMLElement $queryResult  the result of the deletion returned by the api
	* @return DeleteResult                  a deleteresult object representing the result
	* @access public
	*/
	public function parseResult(SimpleXMLElement $queryResult) : DeleteResult {
		if(!isset($queryResult->delete["reason"])) { throw new Exception("Error on delete"); }
		
		return new DeleteResult(
			(string)$queryResult->delete["from"],
			(string)$queryResult->delete["to"],
			(string)$queryResult->delete["reason"]
		);
	}
}