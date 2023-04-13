<?php
/**
* A class for representing parsetrees
*
* The class Parsetree represents parsetrees expanded by the MediaWiki parser
* Those parsetrees can be parsed into Templates, where they can be modified and returned to a string
* which allows them to be send back to the wiki to change the content of a page
*
* @method void setPages(array|string $pages)
* @method void setTitle(string $title)
* @method void setContent(string $content)
* @method Generator|Page expand(bool $generator)
* @method Generator|Page expandFromNamespace(string $namespace, string $filter, string $limit)
* @method Generator|Page expandFromBacklinks(string $link, string $limit)
* @method Generator|Page expandFromCategorymembers(string $category, string $limit, array $types)
* @method Generator|Page expandFromLinklist(string $link, string $limit)
* @method Generator|Page expandFromTransclusions(string $link, string $limit)
* @method stdClass parseToTree(string $xml)
* @method Generator|Template parseTemplate(stdClass $template)
*/
class Parsetree {
	private Bot $bot;
	private array $pages;
	private string $title;
	private string $content;
	
	/**
	* constructor for class Parsetree
	*
	* @param Bot $bot  a reference to the bot object
	* @access public
	*/
	public function __construct(Bot &$bot) {
		$this->bot = $bot;
	}
	
	/**
	* setter for one or multiple pages
	* multiple pages as a string have to be divided by "|"
	*
	* @param array|string $pages  the pages to set
	* @access public
	*/
	public function setPages(array|string $pages) : void {
		if(gettype($pages) === "string") { $pages = explode("|", $pages); }
		$this->pages = $pages;
	}
	
	/**
	* setter for the title
	* can not be used in combination with pages
	*
	* @param string $title  the title to set
	* @access public
	*/
	public function setTitle(string $title) : void {
		$this->title = $title;
	}
	
	/**
	* setter for the content
	* can not be used in combination with pages
	*
	* @param string $content  the content to set
	* @access public
	*/
	public function setContent(string $content) : void {
		$this->content = $content;
	}
	
	/**
	* executor for parsetree
	*
	* @param bool $generator  will always return a generator if set to true
	* @return Generator|Page  a generator of all pages which were expanded,
	*                         a page if only a single page was expanded or a page including expanded text
	* @access public
	*/
	public function expand(bool $generator = false) : Generator|Page {
		if(isset($this->pages)) {
			$requester = new APIMultiRequest();
			$pages = array();
			
			foreach($this->pages as $page) {
				$request = new ParsetreeRequest($this->bot->getUrl());
				$request->setCookieFile($this->bot->getCookieFile());
				$request->setLogger($this->bot->getLogger());
				$request->setPage($page);
				$requester->addRequest($request->getRequest());
			}
			
			foreach($requester->execute() as $queryResult) {
				$page = new Page((string)$queryResult->parse["title"]);
				$page->setId((int)$queryResult->parse["pageid"]);
				
				foreach($this->parseTemplate($this->parseToTree((string)$queryResult->parse->parsetree)) as $template) {
					$page->addTemplate($template);
				}
				
				array_push($pages, $page);
			}
			
			if(count($pages) === 1 && $generator === false) {
				foreach($pages as $page) {
					return $page;
				}
			} else {
				return (function() use ($pages) {
					foreach($pages as $page) {
						yield $page;
					}
				})();
			}
		} elseif(isset($this->content)) {
			$request = new ParsetreeRequest($this->bot->getUrl());
			$request->setCookieFile($this->bot->getCookieFile());
			$request->setLogger($this->bot->getLogger());
			$request->setContent($this->content);
			if(isset($this->title)) { $request->setTitle($this->title); }
			$queryResult = $request->execute();
			
			$page = new Page(isset($this->title) ? $this->title : "");
			
			foreach($this->parseTemplate($this->parseToTree((string)$queryResult->parse->parsetree)) as $template) {
				$page->addTemplate($template);
			}
			
			return $page;
		} else {
			throw new Error("Either pages or content must be set before expanding");
		}
	}
	
	/**
	* list generator for namespaces
	*
	* @param string $namespace  the namespace the pages belong to
	* @param string $limit      the maximum amount of pages to query
	* @param Strnig $filter     filter for the type of pages. Allowed values are "all", "redirects" and "nonredirects"
	* @return Generator|Page    a generator for all pages linking to the page
	* @access public
	*/
	public function expandFromNamespace(
		string $namespace,
		string $filter = "nonredirects",
		string $limit = "max"
	) : Generator|Page {
		$allpages = new NamespaceList($this->bot, $namespace, $filter, $limit);
		$this->pages = array_map(function(Page $page) {
			return $page->getTitle();
		}, iterator_to_array($allpages->getAllPages(), false));
		yield from $this->expand(true);
	}
	
	/**
	* list generator for backlinks
	*
	* @param string $link     the linked page
	* @param string $limit    the maximum amount of pages to query
	* @return Generator|Page  a generator for all pages linking to the page
	* @access public
	*/
	public function expandFromBacklinks(string $link, string $limit = "max") : Generator|Page {
		$backlinks = new BacklinkList($this->bot, $link, $limit);
		$this->pages = array_map(function(Page $page) {
			return $page->getTitle();
		}, iterator_to_array($backlinks->getAllBacklinks(), false));
		yield from $this->expand(true);
	}
	
	/**
	* list generator for all category members
	*
	* @param string $category  the linked page
	* @param string $limit     the maximum amount of pages to query
	* @param array $types      an array containing the types of the query
	*                          may contain any but at least one of "page", "subcat" or "file"
	* @return Generator|Page   a generator for all pages transcluded on or linking to the page
	* @access public
	*/
	public function expandFromCategorymembers(
		string $category,
		string $limit = "max",
		array $types = array("page", "subcat", "file")
	) : Generator|Page {
		$categorymembers = new Category($this->bot, $category, $limit, $types);
		$this->pages = array_map(function(Page $page) {
			return $page->getTitle();
		}, iterator_to_array($categorymembers->getMembers(), false));
		yield from $this->expand(true);
	}
	
	/**
	* list generator for an entire linklist
	* this includes both backlinks and transclusions
	*
	* @param string $link     the linked page
	* @param string $limit    the maximum amount of pages to query
	* @return Generator|Page  a generator for all pages transcluded on or linking to the page
	* @access public
	*/
	public function expandFromLinklist(string $link, string $limit = "max") : Generator|Page {
		yield from $this->fromBacklinks($link, $limit);
		yield from $this->fromTransclusions($link, $limit);
	}
	
	/**
	* list generator for transclusions
	*
	* @param string $link     the transcluded page
	* @param string $limit    the maximum amount of pages to query
	* @return Generator|Page  a generator for all pages transcluded on the page
	* @access public
	*/
	public function expandFromTransclusions(string $link, string $limit = "max") : Generator|Page {
		$transclusions = new TransclusionList($this->bot, $link, $limit);
		$this->pages = array_map(function(Page $page) {
			return $page->getTitle();
		}, iterator_to_array($transclusions->getTransclusions(), false));
		yield from $this->expand(true);
	}
	
	/**
	* parses the expanded content to a tree representing all templates
	* taken from https://www.php.net/manual/en/function.xml-parse-into-struct.php#66487, modified
	*
	* @param string $xml  the xml parsetree returned by the api
	* @return stdClass    a stdClass object containing all templates that were expanded
	* @access private
	*/
	private function parseToTree(string $xml) : stdClass {
		$parser = xml_parser_create();
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parse_into_struct($parser, $xml, $tags);
		xml_parser_free($parser);

		$elements = array();
		$stack = array();
		foreach($tags as $tag) {
			$index = count($elements);
			if($tag['type'] == "complete" || $tag['type'] == "open" || $tag['type'] == "cdata") {
				$elements[$index] = new stdClass();
				$elements[$index]->name = $tag['tag'];
				if(isset($tag['attributes'])) { $elements[$index]->attributes = $tag['attributes']; }
				if(isset($tag['value'])) { $elements[$index]->content = $tag['value']; }
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
	* @access private
	*/
	private function parseTemplate(stdClass $template) : Generator|Template {
		$t = new Template();
		if($template->name == "template" || $template->name === "tplarg") { // parse regular template and template arguments
			if($template->name === "tplarg") { $t->setIsArg(true); }
			foreach($template->children as $parts) {
				if($parts->name === "title") {
					$title = "";
					$titleArgs = array();
					if(isset($parts->content)) {
						$title .= $parts->content;
					}
					if(isset($parts->children)) {
						foreach($parts->children as $subTemplate) {
							foreach($this->parseTemplate($subTemplate) as $parsedSubTemplate) {
								$title .= $parsedSubTemplate->rebuild();
								array_push($titleArgs, $parsedSubTemplate);
							}
						}
					}
					$t->setTitle($title, $titleArgs);
				} elseif($parts->name === "part") {
					$index = false;
					foreach($parts->children as $part) {
						if($part->name === "name") {
							if(isset($part->content)) {
								$param = $part->content;
							} elseif(isset($part->attributes)) {
								$param = $part->attributes["index"];
								$index = true;
							} else {
								$param = "";
								foreach($part->children as $subTemplate) {
									foreach($this->parseTemplate($subTemplate) as $parsedSubTemplate) {
										$param .= $parsedSubTemplate->rebuild();
									}
								}
							}
						} elseif($part->name === "value") {
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
					$t->addParam($param, $value, $index);
					unset($param, $value);
				}
			}
			yield $t;
		} elseif(
			$template->name === "root"
			|| $template->name === "h"
			|| $template->name === "possible-h"
			|| $template->name === "value"
			|| $template->name === "comment"
			|| $template->name === "title"
			|| $template->name === "ignore"
			|| $template->name === "name"
			|| $template->name === "equals"
		) {
			// parse anything text related that is not a template but might be part of a template
			$t->setText((isset($template->content) ? $template->content : ""));
			yield $t;
			if(isset($template->children)) {
				foreach($template->children as $parts) {
					yield from $this->parseTemplate($parts);
				}
			}
		} elseif($template->name === "ext") { // parse parser extension, treat as regular text
			$text = "<";
			foreach($template->children as $parts) {
				if($parts->name === "name" || $parts->name === "attr" || $parts->name === "close") {
					if(isset($parts->content)) {
						$text .= $parts->content;
					}
				} elseif($parts->name === "inner") {
					$text .= ">" . $parts->content;
				}
			}
			if(!str_contains($text, ">")) {
				$text .= "/>";
			}
			$t->setText($text);
			yield $t;
		} else { // debug output, will have to be dealt with when it shows up
			var_dump($template);
			die();
		}
		return $t;
	}
}