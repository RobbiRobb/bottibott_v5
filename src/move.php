<?php
/**
* A class for representing API-requests for moving a page
*
* The class Move represents API-requests for changing the name of a page on a wiki
* Moving with and without redirect is possible as well as moving the talk page at the same time
*
* @method void setFrom(String $from)
* @method void setTo(String $to)
* @method void setReason(String $reason)
* @method void setNoredirect(String $noredirect)
* @method void setMovetalk(String $movetalk)
* @method SimpleXMLElement execute(String $token)
* @method CurlHandle getRequest(String $token)
*/
class Move extends Request {
	private String $from;
	private String $to;
	private String $reason;
	private String $noredirect;
	private String $movetalk;
	
	/**
	* constructor for class Move
	*
	* @param String $url         the url to the wiki
	* @param String $from        the old name of the page
	* @param String $to          the new name of the page
	* @param String $reason      the reason displayed why the page was moved
	* @param String $noredirect  whether the page should be moved without redirect or not
	* @param String $movetalk    whether the talk page of the page should be move as well or not
	* @access public
	*/
	public function __construct(String $url, String $from, String $to, String $reason= "", String $noredirect = "1", String $movetalk = "1") {
		$this->url = $url;
		$this->from = $from;
		$this->to = $to;
		$this->reason = $reason;
		$this->noredirect = $noredirect;
		$this->movetalk = $movetalk;
		$this->cookiefile = "cookiefile.txt";
	}
	
	/**
	* setter for the old name of the page
	*
	* @param String $from  the old name of the page
	* @access public
	*/
	public function setFrom(String $from) {
		$this->from = $from;
	}
	
	/**
	* setter for the new name of the page
	*
	* @param String $to  the new name of the page
	* @access public
	*/
	public function setTo(String $to) {
		$this->to = $to;
	}
	
	/**
	* setter for the reason of the move
	*
	* @param String $reason  the reason why the page was moved
	* @access public
	*/
	public function setReason(String $reason) {
		$this->reason = $reason;
	}
	
	/**
	* setter for whether the page should be moved without redirect or not
	*
	* @param String $noredirect  the new value
	* @access public
	*/
	public function setNoredirect(String $noredirect) {
		$this->noredirect = $noredirect;
	}
	
	/**
	* setter for whether the talk page of the page should be moved as well
	*
	* @param String $movetalk  the new value
	* @access public
	*/
	public function setMovetalk(String $movetalk) {
		$this->movetalk = $movetalk;
	}
	
	/**
	* executor for the API-request
	*
	* @param String $token      the token required for moving a page
	* @return SimpleXMLElement  the SimpleXMLElement-representation of the query result
	* @access public
	*/
	public function execute(String $token) {
		$move = new APIRequest($this->url);
		$move->setCookieFile($this->cookiefile);
		$move->setLogger($this->logger);
		$move->addToGetFields("action", "move");
		$move->addToGetFields("format", "xml");
		$move->addToGetFields("from", $this->from);
		$move->addToGetFields("to", $this->to);
		$move->addToGetFields("reason", $this->reason);
		$move->addToGetFields("noredirect", $this->noredirect);
		$move->addToGetFields("movetalk", $this->movetalk);
		$move->addToPostFields("token", $token);
		return $move->execute();
	}
	
	/**
	* executor for the API-request
	*
	* @param String $token  the token required for editing a page
	* @return CurlHandle    a reference to the request handle
	* @access public
	*/
	public function &getRequest(String $token) {
		$move = new APIRequest($this->url);
		$move->setCookieFile($this->cookiefile);
		$move->addToGetFields("action", "move");
		$move->addToGetFields("format", "xml");
		$move->addToGetFields("from", $this->from);
		$move->addToGetFields("to", $this->to);
		$move->addToGetFields("reason", $this->reason);
		$move->addToGetFields("noredirect", $this->noredirect);
		$move->addToGetFields("movetalk", $this->movetalk);
		$move->addToPostFields("token", $token);
		return $move->getRequest();
	}
}
?>