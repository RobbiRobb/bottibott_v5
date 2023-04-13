<?php
/**
* A class for representing API-requests for deleting a page
*
* The class DeleteRequest allows the creation of API-requests for deleting a page on a wiki
*
* @method void setReason()
* @method SimpleXMLElement execute()
* @method CurlHandle getRequest()
*/
class DeleteRequest extends Request {
	private string $title;
	private string $token;
	private string $reason;
	
	/**
	* constructor for class DeleteRequest
	*
	* @param string $url     the url to the wiki
	* @param string $title   the title of the page that should be deleted
	* @param string $token   a csrf token needed to perform the action
	* @param string $reason  the reason why the page should be deleted
	* @access public
	*/
	public function __construct(string $url, string $title, string $token, string $reason = "") {
		$this->url = $url;
		$this->title = $title;
		$this->token = $token;
		$this->reason = $reason;
	}
	
	/**
	* setter for the reason
	*
	* @param string $reason  the reason that should be set
	* @access public
	*/
	public function setReason(string $reason) : void {
		$this->reason = $reason;
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
		$request->addToGetFields("action", "delete");
		$request->addToGetFields("format", "xml");
		$request->addToGetFields("title", $this->title);
		$request->addToGetFields("reason", $this->reason);
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
		$request = new APIRequest($this->url);
		$request->setCookieFile($this->cookiefile);
		$request->setLogger($this->logger);
		$request->addToGetFields("action", "delete");
		$request->addToGetFields("format", "xml");
		$request->addToGetFields("title", $this->title);
		$request->addToGetFields("reason", $this->reason);
		$request->addToPostFields("token", $this->token);
		return $request->getRequest();
	}
}