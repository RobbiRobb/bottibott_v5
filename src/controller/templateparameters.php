<?php
/**
* A class for representing a list of template parameters
*
* The class TemplateParameters represents a list of all parameters a template offers
*
* @method void setPage(string $page)
* @method void setContent(string $content)
* @method Generator|string getParameters()
*/
class TemplateParameters {
	private Bot $bot;
	private string $page;
	private string $content;
	
	/**
	* constructor for lass TemplateParameters
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
	* setter for the content
	*
	* @param string $content  the content to set
	* @access public
	*/
	public function setContent(string $content) : void {
		$this->content = $content;
	}
	
	/**
	* getter for all parameters of a template
	* combines regex and parsing the template to get a full list
	* might contain false positives, parsing template arguments is hard
	*
	* @return Generator|string  all arguments of a template sorted alphabetically
	* @access public
	*/
	public function getParameters() : Generator|string {
		if(isset($this->page) && isset($this->content)) { throw new Error("Can not set both page and content"); }
		if(!isset($this->page) && !isset($this->content)) { throw new Error("Either page or content must be set"); }
		$parsetree = new Parsetree($this->bot);
		if(isset($this->page)) { $parsetree->setPages($this->page); }
		if(isset($this->content)) { $parsetree->setContent($this->content); }
		
		$args = array();
		
		foreach($parsetree->expand()->getTemplates() as $template) {
			$func = (function(Template $template) use (&$func, &$args) {
				if($template->isArg()) { array_push($args, $template->getTitle()); }
				if($template->isString()) {
					preg_match_all("/\{\{\{([^\|\}\{]+)/", preg_replace(
						"/<noinclude>(?<=\<noinclude\>)(.*?)(?=\<\/noinclude\>)<\/noinclude>/",
						"",
						$template->rebuild()
					), $matches);
					
					$args = array_merge($args, array_unique($matches[1]));
				} else {
					foreach($template->getTitleArgs() as $arg) {
						$func($arg);
					}
					foreach($template->getParams() as $param) {
						if(is_array($param->getValue())) {
							foreach($param->getValue() as $subTemplate) {
								$func($subTemplate);
							}
						}
					}
				}
			});
			
			$func($template);
		}
		$args = array_unique($args);
		asort($args);
		
		foreach($args as $arg) {
			yield $arg;
		}
	}
}