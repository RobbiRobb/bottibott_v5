<?php
/**
* A class representing pages
*
* The class Page represents wiki pages and their attributes.
* It is supplied by varios functions and allows reading and modifiying data
*
* @method void setTitle(string $title)
* @method string getTitle()
* @method void setContent(string $content)
* @method string getContent()
* @method void addTemplate(Template $template)
* @method Generator|Template getTemplates()
* @method string templatesToContent()
*/
class Page {
	private string $title;
	private string $content;
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
	* @param string $title  the title that wil be set
	* @access public
	*/ 
	public function setTitle(string $title) : void {
		$this->title = $title;
	}
	
	/**
	* getter for the title
	*
	* @return string  the title of the template
	* @access public
	*/
	public function getTitle() : string {
		return $this->title;
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
		} else if(isset($this->content)) {
			return $this->content;
		} else {
			return NULL;
		}
	}
	
	/**
	* add a template to the list of templates on the page
	*
	* @param Template $template  the template to add
	* @access public
	*/
	public function addTemplate(Template $template) : void {
		if(!isset($this->templates)) $this->templates = array();
		array_push($this->templates, $template);
	}
	
	/**
	* generator yielding all templates on the page
	*
	* @return Generator|Template  the templates on the page
	* @access public
	*/
	public function &getTemplates() : Generator|Template {
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
		if(!isset($this->templates)) Throw new Error("No templates set");
		$content = "";
		foreach($this->getTemplates() as $template) {
			$content .= $template->rebuild();
		}
		return $content;
	}
}
?>