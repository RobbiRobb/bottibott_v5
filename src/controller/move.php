<?php
/**
* A class for moving a page
*
* The class Move represents the move a page from one name ot another
*
* @method void setReason(string $reason)
* @method void setNoredirect(bool $noredirect)
* @method void setMovetalk(bool $movetalk)
* @method MoveResult execute()
* @method CurlHandle getRequest()
* @method MoveResult parseResult(SimpleXMLElement $queryResult)
*/
class Move {
	private Bot $bot;
	private string $from;
	private string $to;
	private string $reason;
	private bool $noredirect;
	private bool $movetalk;
	
	/**
	* constructor for class Move
	*
	* @param Bot $bot          a reference to the bot object
	* @param string $from      from where the page should be moved
	* @param string $to        where the page should be moved to
	* @param string $reason    the reason for the page moved, displayed in the log
	* @param bool $noredirect  true if the page should be moved without redirect, false otherwise
	* @param bool $movetalk    true if the talk page should be moved as well, false otherwise
	* @access public
	*/
	public function __construct(
		Bot &$bot,
		string $from,
		string $to,
		string $reason = "",
		bool $noredirect = true,
		bool $movetalk = true
	) {
		$this->bot = $bot;
		$this->from = $from;
		$this->to = $to;
		$this->reason = $reason;
		$this->noredirect = $noredirect;
		$this->movetalk = $movetalk;
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
	* setter for if the page should be moved with or without redirect
	*
	* @param bool $noredirect  true if the page should be moved without redirect, false otherwise
	* @access public
	*/
	public function setNoredirect(bool $noredirect) : void {
		$this->noredirect = $noredirect;
	}
	
	/**
	* setter for the if the talk page should be moved as well
	*
	* @param bool $movetalk  true if the talk page should be moved as well, false otherwise
	* @access public
	*/
	public function setMovetalk(bool $movetalk) : void {
		$this->movetalk = $movetalk;
	}
	
	/**
	* executor for moving a page
	*
	* @return MoveResult  a MoveResult representing the move
	* @access public
	*/
	public function execute() : MoveResult {
		$request = new MoveRequest(
			$this->bot->getUrl(),
			$this->from,
			$this->to,
			$this->bot->getToken("csrf"),
			$this->reason,
			$this->noredirect,
			$this->movetalk
		);
		$request->setCookieFile($this->bot->getCookieFile());
		$request->setLogger($this->bot->getLogger());
		return self::parseResult($request->execute());
	}
	
	/**
	* getter for the request of a move
	*
	* @return CurlHandle  a reference to the request handle
	* @access public
	*/
	public function &getRequest() : CurlHandle {
		$request = new MoveRequest(
			$this->bot->getUrl(),
			$this->from,
			$this->to,
			$this->bot->getToken("csrf"),
			$this->reason,
			$this->noredirect,
			$this->movetalk
		);
		$request->setCookieFile($this->bot->getCookieFile());
		$request->setLogger($this->bot->getLogger());
		return $request->getRequest();
	}
	
	/**
	* parse result into MoveResult object
	* called automatically on execution of request
	* can be called manually when using multirequest for execution
	*
	* @param SimpleXMLElement $queryResult  the result of the move returned by the api
	* @return MoveResult                    a moveresult object representing the result
	* @access public
	*/
	public static function parseResult(SimpleXMLElement $queryResult) : MoveResult {
		if(!isset($queryResult->move["from"])) { throw new Exception("Error on move"); }
		
		return new MoveResult(
			(string)$queryResult->move["from"],
			(string)$queryResult->move["to"],
			(string)$queryResult->move["reason"]
		);
	}
}