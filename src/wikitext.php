<?php
/**
* A class for representing API-requests for expanded wikitext
*
* The class Wikitext allows the representation of API-requests for the expanded wikitext of a given input
* The parser will attempt to expand all transcluded templates and magic words,
* so different wikis may result in different output on the same input
*
* @method void setText(String $text)
* @method void setTitle(String $title)
* @method String execute()
*/
class Wikitext extends Request {
	private String $text;
	private String $title;
	
	/**
	* constructor for class Wikitext
	*
	* @param String $url    the url to the wiki
	* @param String $text   the text to be expanded
	* @param String $title  a page title for page-sensitive expanding
	* @access public
	*/
	public function __construct(String $url, String $text, String $title = "") {
		$this->url = $url;
		$this->text = $text;
		$this->title = $title;
		$this->cookiefile = "cookie.txt";
	}
	
	/**
	* setter for the text
	*
	* @param String $text  the text to be set
	* @access public
	*/
	public function setText(String $text) {
		$this->text = $text;
	}
	
	/**
	* setter for the title
	*
	* @param String $title  the title to be set
	* @access public
	*/
	public function setTitle(String $title) {
		$this->title = $title;
	}
	
	/**
	* executor for the API-request
	*
	* @access public
	* @return String  the expanded wikitext for the input
	*/
	public function execute() {
		$wikitext = new APIRequest($this->url);
		$wikitext->setCookieFile($this->cookiefile);
		$wikitext->setLogger($this->logger);
		$wikitext->addToGetFields("action", "expandtemplates");
		$wikitext->addToGetFields("format", "xml");
		$wikitext->addToGetFields("prop", "wikitext");
		$wikitext->addToGetFields("includecomments", "1");
		if(!empty($this->title)) $wikitext->addToPOSTFields("title", $this->title);
		$wikitext->addToPOSTFields("text", $this->text);
		return (String)$wikitext->execute()->expandtemplates->wikitext;
	}
}
?>