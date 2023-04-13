<?php
/**
* A class for parsing and expanding texts and pages
*
* The class Parser allows the parsing of wikitext to html
* or expanding templates, variables and magic words to final wikitext before it is parsed to html
*
* @method void setPage(string $page)
* @method void setTitle(string $title)
* @method void setText(string $text)
* @method void setSection(int $section)
* @method Page parseText()
* @method Generator|Section getSections()
* @method string expandText()
*/
class Parser {
	private Bot $bot;
	private string $page;
	private string $title;
	private string $text;
	private int $section;
	
	/**
	* constructor for class Parser
	*
	* @param Bot $bot  a reference to the bot object
	* @access public
	*/
	public function __construct(Bot &$bot) {
		$this->bot = $bot;
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
	* setter for the title
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
	* parser for parsing wikitext to html
	*
	* @return Page  an object for the page with the expanded html associated to it
	* @access public
	*/
	public function parseText() : Page {
		$request = new ParseRequest($this->bot->getUrl(), "text");
		$request->setCookieFile($this->bot->getCookieFile());
		$request->setLogger($this->bot->getLogger());
		if(isset($this->page)) { $request->setPage($this->page); }
		if(isset($this->title)) { $request->setTitle($this->title); }
		if(isset($this->text)) { $request->setText($this->text); }
		if(isset($this->section)) { $request->setSection($this->section); }
		$queryResult = $request->execute();
		
		$page = new Page((isset($queryResult->parse["title"]) ? (string)$queryResult->parse["title"] : ""));
		if(isset($queryResult->parse["pageid"])) { $page->setId((int)$queryResult->parse["pageid"]); }
		$page->setExpandedText((string)$queryResult->parse->text);
		return $page;
	}
	
	/**
	* generator for all sections on either a page or the given text
	*
	* @return Generator|Section  a generator for all sections
	* @access public
	*/
	public function getSections() : Generator|Section {
		$request = new ParseRequest($this->bot->getUrl(), "sections");
		$request->setCookieFile($this->bot->getCookieFile());
		$request->setLogger($this->bot->getLogger());
		if(isset($this->page)) { $request->setPage($this->page); }
		if(isset($this->title)) { $request->setTitle($this->title); }
		if(isset($this->text)) { $request->setText($this->text); }
		if(isset($this->section)) { $request->setSection($this->section); }
		$queryResult = $request->execute();
		
		foreach($queryResult->parse->sections->s as $section) {
			$sectionData = new Section(
				(int)$section["toclevel"],
				(int)$section["level"],
				(string)$section["line"],
				(string)$section["number"],
				(int)$section["index"],
				(int)$section["byteoffset"],
				(string)$section["anchor"]
			);
			yield $sectionData;
		}
	}
	
	/**
	* expander for the wikitext, expanding all templates, variables and magic words
	*
	* @return string  the fully expanded wikitext
	* @access public
	*/
	public function expandText() : string {
		if(!isset($this->text)) { throw new Error("Text must be set"); }
		$request = new ExpandRequest($this->bot->getUrl(), $this->text);
		$request->setCookieFile($this->bot->getCookieFile());
		$request->setLogger($this->bot->getLogger());
		if(isset($this->title)) { $request->setTitle($this->title); }
		$queryResult = $request->execute();
		return (string)$queryResult->expandtemplates->wikitext;
	}
}