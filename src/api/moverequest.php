<?php
/**
* A class for representing API-requests for moving a page
*
* The class MoveRequest represents API-requests for changing the name of a page on a wiki
* Moving with and without redirect is possible as well as moving the talk page at the same time
*
* @method void setReason(string $reason)
* @method void setNoredirect(bool $noredirect)
* @method void setMovetalk(bool $movetalk)
* @method SimpleXMLElement execute(string $token)
* @method CurlHandle getRequest(string $token)
*/
class MoveRequest extends Request {
	private string $from;
	private string $to;
	private string $token;
	private string $reason;
	private bool $noredirect;
	private bool $movetalk;
	
	/**
	* constructor for class MoveRequest
	*
	* @param string $url         the url to the wiki
	* @param string $from        the old name of the page
	* @param string $to          the new name of the page
	* @param string $reason      the reason displayed why the page was moved
	* @param bool $noredirect    whether the page should be moved without redirect or not
	* @param bool $movetalk      whether the talk page of the page should be move as well or not
	* @access public
	*/
	public function __construct(
		string $url,
		string $from,
		string $to,
		string $token,
		string $reason= "",
		bool $noredirect = true,
		bool $movetalk = true
	) {
		$this->url = $url;
		$this->from = $from;
		$this->to = $to;
		$this->token = $token;
		$this->reason = $reason;
		$this->noredirect = $noredirect;
		$this->movetalk = $movetalk;
	}
	
	/**
	* setter for the reason of the move
	*
	* @param string $reason  the reason why the page was moved
	* @access public
	*/
	public function setReason(string $reason) : void {
		$this->reason = $reason;
	}
	
	/**
	* setter for whether the page should be moved without redirect or not
	*
	* @param bool $noredirect  the new value
	* @access public
	*/
	public function setNoredirect(bool $noredirect) : void {
		$this->noredirect = $noredirect;
	}
	
	/**
	* setter for whether the talk page of the page should be moved as well
	*
	* @param bool $movetalk  the new value
	* @access public
	*/
	public function setMovetalk(bool $movetalk) : void {
		$this->movetalk = $movetalk;
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
		$request->addToGetFields("action", "move");
		$request->addToGetFields("format", "xml");
		$request->addToGetFields("from", $this->from);
		$request->addToGetFields("to", $this->to);
		$request->addToGetFields("reason", $this->reason);
		$request->addToGetFields("noredirect", ($this->noredirect ? "1" : ""));
		$request->addToGetFields("movetalk", ($this->movetalk ? "1" : ""));
		$request->addToPostFields("token", $this->token);
		return $request->execute();
	}
	
	/**
	* executor for the API-request
	*
	* @return CurlHandle  a reference to the request handle
	* @access public
	*/
	public function &getRequest() : CurlHandle {
		$request = new APIRequest($this->url);
		$request->setCookieFile($this->cookiefile);
		$request->addToGetFields("action", "move");
		$request->addToGetFields("format", "xml");
		$request->addToGetFields("from", $this->from);
		$request->addToGetFields("to", $this->to);
		$request->addToGetFields("reason", $this->reason);
		$request->addToGetFields("noredirect", ($this->noredirect ? "1" : ""));
		$request->addToGetFields("movetalk", ($this->movetalk ? "1" : ""));
		$request->addToPostFields("token", $this->token);
		return $request->getRequest();
	}
}