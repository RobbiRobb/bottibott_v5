<?php
/**
* A class for representing API-requests for parsetrees
*
* The class Parsetree allows the representation of parsetrees
* It allows the bot to generate and expand those parsetrees
*
* @method String getTitle()
* @method void setTitle(String $title)
* @method String getContent()
* @method void setContent(String $content)
* @method String getExpandedContent()
* @method void setExpandedContent(String $expandedContent)
* @method Page parseToPage()
* @method stdClass parseToTree()
* @method Generator|Template parseTemplate(stdClass $template)
* @method Page execute()
* @method CurlHandle getRequest()
*/
class Parsetree extends Request {
	private ?String $title = null;
	private ?String $content = null;
	private ?String $expandedContent = null;
	
	/**
	* constructor for class Parsetree
	*
	* @param String $url      the url to the wiki
	* @access public
	*/
	public function __construct(String $url) {
		$this->url = $url;
		$this->cookiefile = "cookies.txt";
	}
	
	/**
	* getter for the title
	*
	* @return String  the title of the page
	*/
	public function getTitle() : String {
		return $this->title;
	}
	
	/**
	* setter for the title
	*
	* @param String $title  the title that should be set
	* @access public
	*/
	public function setTitle(String $title) : void {
		$this->title = $title;
	}
	
	/**
	* getter for the content
	*
	* @return String  the content of the page
	*/
	public function getContent() : String {
		return $this->content;
	}
	
	/**
	* setter for the content
	*
	* @param String $content  the content that should be set
	* @access public
	*/
	public function setContent(String $content) : void {
		$this->content = $content;
	}
	
	/**
	* getter for the expandedContent
	*
	* @return String  the expandedContent of the page
	*/
	public function getExpandedContent() : String {
		return $this->expandedContent;
	}
	
	/**
	* setter for the expandedContent
	*
	* @param String $expandedContent  the expandedContent that should be set
	* @access public
	*/
	public function setExpandedContent(String $expandedContent) : void {
		$this->expandedContent = $expandedContent;
	}
	
	/**
	* transforms the tree representation of all templates to a page object containing all templates on one page
	*
	* @return Page  a page object containing all templates that were expanded
	* @access public
	*/
	public function parseToPage() : Page {
		$tree = $this->parseToTree();
		$page = new Page((isset($this->title) ? $this->title : ""));
		
		foreach($this->parseTemplate($tree) as $template) {
			$page->addTemplate($template);
		}
		
		return $page;
	}
	
	/**
	* parses the expanded content to a tree representing all templates
	* taken from https://www.php.net/manual/en/function.xml-parse-into-struct.php#66487, modified
	*
	* @return stdClass  a stdClass object containing all templates that were expanded
	* @access public
	*/
	public function parseToTree() : stdClass {
		$parser = xml_parser_create();
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parse_into_struct($parser, $this->expandedContent, $tags);
		xml_parser_free($parser);

		$elements = array();
		$stack = array();
		foreach($tags as $tag) {
			$index = count($elements);
			if($tag['type'] == "complete" || $tag['type'] == "open" || $tag['type'] == "cdata") {
				$elements[$index] = new stdClass();
				$elements[$index]->name = $tag['tag'];
				if(isset($tag['attributes'])) $elements[$index]->attributes = $tag['attributes'];
				if(isset($tag['value'])) $elements[$index]->content = $tag['value'];
				if($tag['type'] == "open") {
					$elements[$index]->children = array();
					$stack[count($stack)] = &$elements;
					$elements = &$elements[$index]->children;
				}
			}
			if($tag['type'] == "close") {
				$elements = &$stack[count($stack) - 1];
				unset($stack[count($stack) - 1]);
			}
		}
		return $elements[0];
	}
	
	/**
	* parser for the template structure. Converts the tree representation to a template object
	*
	* @param stdClass $template   the template that will be parsed to a template object
	* @return Generator|Template  a template object representing the parsed template
	* @access public
	*/
	public function parseTemplate(stdClass $template) : Generator|Template {
		$t = new Template();
		if($template->name == "template") { // parse regular template
			foreach($template->children as $parts) {
				if($parts->name === "title") {
					$title = $parts->content;
					if(isset($parts->children)) {
						foreach($parts->children as $subTemplate) {
							foreach($this->parseTemplate($subTemplate) as $parsedSubTemplate) {
								$title .= $parsedSubTemplate->rebuild();
							}
						}
					}
					$t->setTitle($title);
				} else if($parts->name === "part") {
					foreach($parts->children as $part) {
						if($part->name === "name") {
							if(isset($part->content)) {
								$param = $part->content;
							} else {
								$param = $part->attributes["index"];
							}
						} else if($part->name === "value") {
							if(isset($part->children)) {
								$subTemplates = array();
								if(isset($part->content)) {
									$subTemplate = new Template();
									$subTemplate->setText($part->content);
									array_push($subTemplates, $subTemplate);
								}
								foreach($part->children as $child) {
									foreach($this->parseTemplate($child) as $parsedSubTemplate) {
										array_push($subTemplates, $parsedSubTemplate);
									}
								}
								$value = $subTemplates;
							} else {
								if(isset($part->content)) {
									$value = $part->content;
								} else {
									$value = "";
								}
							}
						}
					}
					$t->addParam($param, $value);
					unset($param, $value);
				}
			}
			yield $t;
		} else if($template->name === "root" || $template->name === "h" || $template->name === "value" || $template->name === "comment" || $template->name === "title") {
			// parse text at root level, heading, value as part of subtemplates, comment or template title in nested templates in titles
			$t->setText((isset($template->content) ? $template->content : ""));
			yield $t;
			if(isset($template->children)) {
				foreach($template->children as $parts) {
					yield from $this->parseTemplate($parts);
				}
			}
		} else if($template->name === "ext") { // parse parser extension, treat as regular text
			$text = "<";
			foreach($template->children as $parts) {
				if($parts->name === "name" || $parts->name === "attr" || $parts->name === "close") {
					if(isset($parts->content)) {
						$text .= $parts->content;
					}
				} else if($parts->name === "inner") {
					$text .= ">" . $parts->content;
				}
			}
			$t->setText($text);
			yield $t;
		} else if($template->name === "ignore" || $template->name === "tplarg") {
			throw new Error("Parsing templates is currently not supported");
		} else { // debug output, will have to be added when it shows up
			var_dump($template);
			die();
		}
		return $t;
	}
	
	/**
	* executor for the API-request
	*
	* @return Page  a page object containing all templates that were expanded
	* @access public
	*/
	public function execute() : Page {
		$parsetree = new APIRequest($this->url);
		$parsetree->setCookieFile($this->cookiefile);
		$parsetree->setLogger($this->logger);
		$parsetree->addToGetFields("action", "parse");
		$parsetree->addToGetFields("prop", "parsetree");
		$parsetree->addToGetFields("format", "xml");
		if(isset($this->content)) $parsetree->addToPostFields("text", $this->content);
		if(isset($this->title)) $parsetree->addToGetFields("page", $this->title);
		$this->expandedContent = (String)$parsetree->execute()->parse->parsetree;
		return $this->parseToPage();
	}
	
	/**
	* getter for the API-request for executing multiple requests at once
	*
	* @return CurlHandle  a reference to the request handle
	* @access public
	*/
	public function &getRequest() : CurlHandle {
		$parsetree = new APIRequest($this->url);
		$parsetree->setCookieFile($this->cookiefile);
		$parsetree->addToGetFields("action", "parse");
		$parsetree->addToGetFields("prop", "parsetree");
		$parsetree->addToGetFields("format", "xml");
		$parsetree->addToGetFields("page", $this->title);
		return $parsetree->getRequest();
	}
}
?>