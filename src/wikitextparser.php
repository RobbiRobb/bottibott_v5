<?php
spl_autoload_register(function($class) {require(strtolower($class).".php");});

/**
* A class for representing API-requests for parsing wiki text
*
* The class Wikitextparser allows the representation of API-requests for parsing
* given wikitext or text from an article, that exists in a wiki
* It can also be used to get the sections of an article
* or get the section index from a name
*
* @method void setTitle(String $title)
* @method String getTitle()
* @method void setText(String $text)
* @method String getText()
* @method mixed getParsedText()
* @method void generateSectionData(String $section)
* @method Generator|mixed getSections(String $section)
* @method mixed getSectionFromName(String $name)
* @method Wikitextparser parseFromTitle(String $section)
* @method Wikitextparser parseText()
*/
class Wikitextparser extends Request {
	private String $title = "";
	private String $text = "";
	private array $sectionData = array();
	private mixed $parsedText = false;
	
	/**
	* constructor for class Wikitextparser
	*
	* @param String $url    the url to the wiki
	* @param String $title  a title for a page which should be parsed or can be used in the context of expanding wikitext
	* @param String $text   the text to be parsed
	* @access public
	*/
	public function __construct(String $url, String $title = "", String $text = "") {
		$this->url = $url;
		$this->title = $title;
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
	* getter for the title
	*
	* @return String  the title of the page
	* @access public
	*/
	public function getTitle() {
		return $this->title;
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
	* getter for the text
	*
	* @return String  the text of the page
	* @access public
	*/
	public function getText() {
		return $this->text;
	}
	
	/**
	* getter for the previously parsed text
	*
	* @return mixed  the parsed text of the page. False if no text has been parsed yet
	* @access public
	*/
	public function getParsedText() {
		return $this->parsedText;
	}
	
	/**
	* helper method for generation of the section data
	*
	* @param String $section  the number of the section that should be parsed
	* @access private
	*/
	private function generateSectionData(String $section = "") {
		$sections = new APIRequest($this->url);
		$sections->setCookieFile($this->cookiefile);
		$sections->addToGetFields("action", "parse");
		$sections->addToGetFields("format", "xml");
		$sections->addToGetFields("page", $this->title);
		$sections->addToGetFields("prop", "sections");
		$sections->addToGetFields("disablelimitreport", "1");
		$sections->addToGetFields("disableeditsection", "1");
		$sections->addToGetFields("preview", "1");
		if(!empty($section) || $section === "0") { $sections->addToGetFields("section", $section); } // MediaWiki doesn't like set but empty section
		$sections = $sections->execute()->parse->sections;
		foreach($sections->s as $section) {
			$sectionData["index"] = (String)$section["index"];
			$sectionData["level"] = (String)$section["level"];
			$sectionData["name"] = (String)$section["line"];
			array_push($this->sectionData, $sectionData);
		}
	}
	
	/**
	* generator for the section data
	*
	* @param String $section   the index of a section that should be parsed
	* @return Generator|mixed  an array containing index, level and name of a section. False if title is not set
	* @access public
	*/
	public function getSections(String $section = "") {
		if(empty($this->title)) {
			yield false;
		} else {
			if(empty($this->sectionData)) {
				$this->generateSectionData($section);
			}
			
			foreach($this->sectionData as $section) {
				yield $section;
			}
		}
	}
	
	/**
	* get the index of a section by it's name
	*
	* @param String $name  the name of the section to look for
	* @return mixed        the index of the section. False if there is no section with the given name
	* @access public
	*/
	public function getSectionFromName(String $name) {
		if(empty($this->sectionData)) {
			$this->generateSectionData();
		}
		
		foreach($this->sectionData as $section) {
			if($section["name"] == $name) {
				return $section;
			}
		}
		
		return false;
	}
	
	/**
	* parse an article from a given title
	*
	* @param String $section  only parse a given section of a page
	* @return Wikitextparser  itself to allow the chaining of calls
	*/
	public function parseFromTitle(String $section = "") {
		$text = new APIRequest($this->url);
		$text->setCookieFile($this->cookiefile);
		$text->addToGetFields("action", "parse");
		$text->addToGetFields("format", "xml");
		$text->addToGetFields("page", $this->title);
		$text->addToGetFields("prop", "text");
		$text->addToGetFields("disablelimitreport", "1");
		$text->addToGetFields("disableeditsection", "1");
		$text->addToGetFields("preview", "1");
		if(!empty($section) || $section === "0") { $text->addToGetFields("section", $section); } // MediaWiki doesn't like set but empty section
		$this->parsedText = (String)$text->execute()->parse->text;
		return $this;
	}
	
	/**
	* parse the given text in the context of the given page
	*
	* @return Wikitextparser  itself to allow the chaining of calls
	*/
	public function parseText() {
		$text = new APIRequest($this->url);
		$text->setCookieFile($this->cookiefile);
		$text->addToGetFields("action", "parse");
		$text->addToGetFields("format", "xml");
		if(!empty($this->title)) { $text->addToGetFields("title", $this->title); } // MediaWiki doesn't like set but empty title
		$text->addToGetFields("text", $this->text);
		$text->addToGetFields("prop", "text");
		$text->addToGetFields("disablelimitreport", "1");
		$text->addToGetFields("disableeditsection", "1");
		$text->addToGetFields("preview", "1");
		$this->parsedText = (String)$text->execute()->parse->text;
		return $this;
	}
}