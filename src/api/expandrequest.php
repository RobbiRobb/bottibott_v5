<?php
/**
* A class for representing API-requests for expanded wikitext
*
* The class ExpandRequest allows the representation of API-requests for the expanded wikitext of a given input
* The parser will attempt to expand all transcluded templates and magic words,
* so different wikis may result in different output on the same input
*
* @method void setText(string $text)
* @method void setTitle(string $title)
* @method string execute()
*/
class ExpandRequest extends Request {
	private string $text;
	private string $title;
	
	/**
	* constructor for class ExpandRequest
	*
	* @param string $url    the url to the wiki
	* @param string $text   the text to be expanded
	* @param string $title  a page title for page-sensitive expanding
	* @access public
	*/
	public function __construct(string $url, string $text, string $title = "") {
		$this->url = $url;
		$this->text = $text;
		$this->title = $title;
	}
	
	/**
	* setter for the text
	*
	* @param string $text  the text to be set
	* @access public
	*/
	public function setText(string $text) : void {
		$this->text = $text;
	}
	
	/**
	* setter for the title
	*
	* @param string $title  the title to be set
	* @access public
	*/
	public function setTitle(string $title) : void {
		$this->title = $title;
	}
	
	/**
	* executor for the API-request
	*
	* @return SimpleXMLElement  the expanded wikitext for the input
	* @access public
	*/
	public function execute() : SimpleXMLElement {
		$request = new APIRequest($this->url);
		$request->setCookieFile($this->cookiefile);
		$request->setLogger($this->logger);
		$request->addToGetFields("action", "expandtemplates");
		$request->addToGetFields("format", "xml");
		$request->addToGetFields("prop", "wikitext");
		$request->addToGetFields("includecomments", "1");
		// MediaWiki doesn't like empty but set title
		if(!empty($this->title)) { $request->addToPOSTFields("title", $this->title); }
		$request->addToPOSTFields("text", $this->text);
		return $request->execute();
	}
}