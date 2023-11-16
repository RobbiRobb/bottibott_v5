<?php
/**
* A class for representing API-requests for parsing text
*
* The class ParseRequest represents API-requests for parsing existing pages or set wikitext to HTML
*
* @method void setPage(string $page)
* @method void setTitle(string $title)
* @method void setText(string $text)
* @method void setSection(int $section)
* @method SimpleXMLElement execute()
* @method CurlHandle getRequest()
*/
class ParseRequest extends Request {
	private string $prop;
	private string $page;
	private string $title;
	private string $text;
	private int $section;
	
	private const PARSER_UNSUPPORTED_PROPERTY = "Only sections and text are supported properties";
	
	/**
	* constructor for class ParseRequest
	*
	* @param string $url   the url to the wiki
	* @param string $prop  the property to query for
	* @access public
	*/
	public function __construct(string $url, string $prop) {
		if(!in_array($prop, array("sections", "text"))) { throw new Error(self::PARSER_UNSUPPORTED_PROPERTY); }
		$this->url = $url;
		$this->prop = $prop;
	}
	
	/**
	* setter for the property
	*
	* @param string $prop  the property to set
	* @access public
	*/
	public function setProp(string $prop) : void {
		if(!in_array($prop, array("sections", "text"))) { throw new Error(self::PARSER_UNSUPPORTED_PROPERTY); }
		$this->prop = $prop;
	}
	
	/**
	* setter for the page
	*
	* @param string $page  the page to set
	* @access public
	*/
	public function setPage(string $page) : void {
		$this->page = $page;
	}
	
	/**
	* setter vor the title
	*
	* @param string $title  the title to set
	* @access public
	*/
	public function setTitle(string $title) : void {
		$this->title = $title;
	}
	
	/**
	* setter for the text
	*
	* @param string $text  the text to set
	* @access public
	*/
	public function setText(string $text) : void {
		$this->text = $text;
	}
	
	/**
	* setter for the section
	*
	* @param int $section  the section to set
	* @access public
	*/
	public function setSection(int $section) : void {
		$this->section = $section;
	}
	
	/**
	* executor for the API-request
	*
	* @return SimpleXMLElement  the SimpleXMLElement-representation of the query result
	* @access public
	*/
	public function execute() : SimpleXMLElement {
		if((isset($this->text) || isset($this->title)) && (isset($this->page) || isset($this->section))) {
			throw new Error("Cannot combine text or title with page or section");
		}
		$request = new APIRequest($this->url);
		$request->setCookieFile($this->cookiefile);
		$request->addToGetFields("action", "parse");
		$request->addToGetFields("format", "xml");
		// only set page if is set
		if(isset($this->page)) { $request->addToGetFields("page", $this->page); }
		// only set section if is set
		if(isset($this->section)) { $request->addToGetFields("section", $this->section); }
		// only set title if is set
		if(isset($this->title)) { $request->addToGetFields("title", $this->title); }
		// only set text if is set
		if(isset($this->text)) { $request->addToPostFields("text", $this->text); }
		$request->addToGetFields("prop", $this->prop);
		$request->addToGetFields("disablelimitreport", "1");
		$request->addToGetFields("disableeditsection", "1");
		$request->addToGetFields("disabletoc", "1");
		$request->addToGetFields("preview", "1");
		return $request->execute();
	}
	
	/**
	* getter for the API-request for executing multiple requests at once
	*
	* @return CurlHandle  a reference to the request handle
	* @access public
	*/
	public function &getRequest() : CurlHandle {
		if((isset($this->text) || isset($this->title)) && (isset($this->page) || isset($this->section))) {
			throw new Error("Cannot combine text or title with page or section");
		}
		$request = new APIRequest($this->url);
		$request->setCookieFile($this->cookiefile);
		$request->addToGetFields("action", "parse");
		$request->addToGetFields("format", "xml");
		// only set page if is set
		if(isset($this->page)) { $request->addToGetFields("page", $this->page); }
		// only set section if is set
		if(isset($this->section)) { $request->addToGetFields("section", $this->section); }
		// only set title if is set
		if(isset($this->title)) { $request->addToGetFields("title", $this->title); }
		// only set text if is set
		if(isset($this->text)) { $request->addToPostFields("text", $this->text); }
		$request->addToGetFields("prop", $this->prop);
		$request->addToGetFields("disablelimitreport", "1");
		$request->addToGetFields("disableeditsection", "1");
		$request->addToGetFields("disabletoc", "1");
		$request->addToGetFields("preview", "1");
		return $request->getRequest();
	}
}