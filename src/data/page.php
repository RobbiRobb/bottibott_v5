<?php
/**
* A class representing pages
*
* The class Page represents wiki pages and their attributes.
* It is supplied by varios functions and allows reading and modifiying data
*
* @method void setTitle(string $title)
* @method string getTitle()
* @method void addCategory(string $category)
* @method Generator|string getCategories()
* @method bool inCategory(string $category)
* @method void setExists(bool $exists)
* @method bool exists()
* @method void addLink(Page $link)
* @method Generator|Page getLinks()
* @method void addLangLink(string $lang, string $link)
* @method string getLangLink(string $lang)
* @method Generator|string getLangLinks()
* @method void addRevision(Revision $revision)
* @methd Generator|Revision getRevisions()
* @method void setExpandedText(string $text)
* @method string getExpandedText()
* @method void setContent(string $content)
* @method string getContent()
* @method void addTemplate(Template $template)
* @method Generator|Template getTemplates()
* @method string templatesToContent()
*/
class Page {
	private string $title;
	private readonly int $id;
	private readonly int $namespace;
	private readonly bool $exists;
	private string $content;
	private array $categories;
	private array $langlinks;
	private array $revisions;
	private array $templates;
	
	/**
	* constructor for class Page
	*
	* @param string $title  the title of the page
	* @access public
	*/
	public function __construct(string $title = "") {
		$this->title = $title;
	}
	
	/**
	* setter for the title
	*
	* @param string $title  the title that will be set
	* @access public
	*/
	public function setTitle(string $title) : void {
		$this->title = $title;
	}
	
	/**
	* getter for the title
	*
	* @return string  the title of the page
	* @access public
	*/
	public function getTitle() : string {
		return $this->title;
	}
	
	/**
	* setter for the id
	*
	* @param int $id  the id that will be set
	* @access public
	*/
	public function setId(int $id) : void {
		$this->id = $id;
	}
	
	/**
	* getter for the id
	*
	* @return int  the id of the page
	* @access public
	*/
	public function getId() : int {
		if(!isset($this->id)) { throw new Exception("Id is not set"); }
		return $this->id;
	}
	
	/**
	* setter for the namespace
	*
	* @param int $namespace  the namespace that will be set
	* @access public
	*/
	public function setNamespace(int $namespace) : void {
		$this->namespace = $namespace;
	}
	
	/**
	* getter for the namespace
	*
	* @return int  the namespace of the page
	* @access public
	*/
	public function getNamespace() : int {
		if(!isset($this->namespace)) { throw new Exception("Namespace is not set"); }
		return $this->namespace;
	}
	
	/**
	* adder for a new category the page is in
	*
	* @param string $category  the category to add
	* @access public;
	*/
	public function addCategory(string $category) : void {
		if(!isset($this->categories)) { $this->categories = array(); }
		if(in_array($category, $this->categories)) { return; }
		array_push($this->categories, $category);
	}
	
	/**
	* getter generator for all categories
	*
	* @return Generator|string  a generator of all categories the page is in
	* @access public
	*/
	public function getCategories() : Generator|string {
		if(!isset($this->categories)) { throw new Exception("Categories are not set"); }
		foreach($this->categories as $category) {
			yield $category;
		}
	}
	
	/**
	* check if the page is in a category
	*
	* @param string $category  the category to check
	* @return bool             true if the page is in that category, false otherwise
	* @access public
	*/
	public function inCategory(string $category) : bool {
		if(!isset($this->categories)) { throw new Exception("Categories are not set"); }
		return in_array($category, $this->categories);
	}
	
	/**
	* set the existence level of a page
	*
	* @param bool $exists  new value for existence
	* @access public
	*/
	public function setExists(bool $exists) : void {
		$this->exists = $exists;
	}
	
	/**
	* check if the page exists. Does not exist if no name is set or existence is set to false
	*
	* @return bool  true if the page is considered to exist, false otherwise
	* @access public
	*/
	public function exists() : bool {
		return isset($this->title) && isset($this->exists) && $this->exists || !isset($this->exists);
	}
	
	/**
	* add a page this page links to
	*
	* @param Page $link  the page this page is linking to
	* @access public
	*/
	public function addLink(Page &$link) : void {
		if(!isset($this->links)) { $this->links = array(); }
		array_push($this->links, $link);
	}
	
	/**
	* get all pages this page links to
	*
	* @return Generator|Page  generator of pages that are linked
	* @access public
	*/
	public function &getLinks() : Generator|Page {
		if(!isset($this->links)) { throw new Exception("Links are not set"); }
		foreach($this->links as $link) {
			yield $link;
		}
	}
	
	/**
	* adder for langlinks
	*
	* @param string $lang  the language of the langlink
	* @param string $link  the name of the page in the given language
	* @access public
	*/
	public function addLangLink(string $lang, string $link) : void {
		if(!isset($this->langlinks)) { $this->langlinks = array(); }
		if(isset($this->langlinks[$lang])) { return; }
		$this->langlinks[$lang] = $link;
	}
	
	/**
	* getter for the link of a page in a specific language
	*
	* @param string $lang  the language
	* @return string       the name of the page in the given language
	* @access public
	*/
	public function getLangLink(string $lang) : string {
		if(!isset($this->langlinks)) { throw new Exception("Langlinks are not set"); }
		if(isset($this->langlinks[$lang])) {
			return $this->langlinks[$lang];
		} else {
			throw new Exception("Language not found");
		}
	}
	
	/**
	* generator for all langlinks
	*
	* @return Generator|string  list of all langlinks
	* @access public
	*/
	public function getLangLinks() : Generator|string {
		if(!isset($this->langlinks)) { throw new Exception("Langlinks are not set"); }
		foreach($this->langlinks as $lang => $langlink) {
			yield $lang => $langlink;
		}
	}
	
	/**
	* adder for revisions
	*
	* @param Revision $revision  the revision to add
	* @access public
	*/
	public function addRevision(Revision &$revision) : void {
		if(!isset($this->revisions)) { $this->revisions = array(); }
		array_push($this->revisions, $revision);
	}
	
	/**
	* generator for all revisions
	*
	* @return Generator|Revision  list of all revisions
	* @access public
	*/
	public function &getRevisions() : Generator|Revision {
		if(!isset($this->revisions)) { throw new Exception("Revisions are not set"); }
		foreach($this->revisions as $revision) {
			yield $revision;
		}
	}
	
	/**
	* setter for the expanded text
	*
	* @param string $text  the previously expanded text
	* @access public
	*/
	public function setExpandedText(string $text) : void {
		$this->expandedText = $text;
	}
	
	/**
	* getter for the expanded text
	*
	* @return string  the previously expanded text
	* @access public
	*/
	public function getExpandedText() : string {
		if(!isset($this->expandedText)) { throw new Exception("ExpandedText is not set"); }
		return $this->expandedText;
	}
	
	/**
	* setter for the content
	*
	* @param string $content  the content that will be set
	* @access public
	*/
	public function setContent(string $content) : void {
		$this->content = $content;
	}
	
	/**
	* getter for the content. Will return content from storage or will rebuild from templates
	*
	* @return string  the content of the page
	* @access public
	*/
	public function getContent() : string {
		if(isset($this->templates)) {
			return $this->templatesToContent();
		} elseif(isset($this->content)) {
			return $this->content;
		} else {
			throw new Exception("Content is not set");
		}
	}
	
	/**
	* add a template to the list of templates on the page
	*
	* @param Template $template  the template to add
	* @access public
	*/
	public function addTemplate(Template &$template) : void {
		if(!isset($this->templates)) { $this->templates = array(); }
		array_push($this->templates, $template);
	}
	
	/**
	* generator yielding all templates on the page
	*
	* @return Generator|Template  the templates on the page
	* @access public
	*/
	public function &getTemplates() : Generator|Template {
		if(!isset($this->templates)) { throw new Exception("Templates are not set"); }
		foreach($this->templates as $template) {
			yield $template;
		}
	}
	
	/**
	* helper function for turning the list of templates into the content
	*
	* @return string  the string represenation of all templates
	* @access private
	*/
	private function templatesToContent() : string {
		if(!isset($this->templates)) { throw new Exception("Templates are not set"); }
		$content = "";
		foreach($this->getTemplates() as $template) {
			$content .= $template->rebuild();
		}
		return $content;
	}
	
	/**
	* debug function
	*
	* @return array  debug info about this object
	* @access public
	*/
	public function __debugInfo() : array {
		$info = array();
		if(isset($this->title)) { $info["title"] = $this->title; }
		if(isset($this->id)) { $info["id"] = $this->id; }
		if(isset($this->namespace)) { $info["namespace"] = $this->namespace; }
		if(isset($this->categories)) { $info["categories"] = $this->categories; }
		if(isset($this->exists)) { $info["exists"] = $this->exists; }
		if(isset($this->langlinks)) { $info["langlinks"] = $this->langlinks; }
		if(isset($this->revisions)) { $info["revisions"] = $this->revisions; }
		if(isset($this->expandedText)) { $info["expandedText"] = $this->expandedText; }
		if(isset($this->content)) { $info["content"] = $this->content; }
		if(isset($this->templates)) { $info["templates"] = $this->templates; }
		return $info;
	}
}