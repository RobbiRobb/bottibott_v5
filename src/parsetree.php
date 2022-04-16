<?php
spl_autoload_register(function($class) {require(strtolower($class).".php");});

/**
* A class for representing API-requests for parsetrees
*
* The class Parsetree allows the representation of parsetrees
* It allows the bot to generate and expand those parsetrees
*
* @method void setContent(String $content)
* @method Generator|Parser match(String $expandedContent)
* @method Parsetree expand()
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
	public function getTitle() {
		return $this->title;
	}
	
	/**
	* setter for the title
	*
	* @param String $title  the title that should be set
	* @access public
	*/
	public function setTitle(String $title) {
		$this->title = $title;
	}
	
	/**
	* getter for the content
	*
	* @return String  the content of the page
	*/
	public function getContent() {
		return $this->content;
	}
	
	/**
	* setter for the content
	*
	* @param String $content  the content that should be set
	* @access public
	*/
	public function setContent(String $content) {
		$this->content = $content;
	}
	
	/**
	* getter for the expandedContent
	*
	* @return String  the expandedContent of the page
	*/
	public function getExpandedContent() {
		return $this->expandedContent;
	}
	
	/**
	* setter for the expandedContent
	*
	* @param String $expandedContent  the expandedContent that should be set
	* @access public
	*/
	public function setExpandedContent(String $expandedContent) {
		$this->expandedContent = $expandedContent;
	}
	
	/**
	* matcher and replacer for unmatched Strings in expandedContent
	*
	* @return Generator|Parser  a Parser-object representing the expanded source code including Strings
	* @access public
	*/
	public function match() {
		$this->expandedContent = preg_replace("/<value>([^<]+?(?=<))<template/",      "<value><template>$1</template><template",     $this->expandedContent);
		$this->expandedContent = preg_replace("/<\/template>([^<]+?(?=<))<\/value>/", "</template><template>$1</template></value>",  $this->expandedContent);
		$this->expandedContent = preg_replace("/<\/template>([^<]+?(?=<))<template/", "</template><template>$1</template><template", $this->expandedContent);
		$this->expandedContent = preg_replace("/<root>([^<]+?(?=<))<template/",       "<root><template>$1</template><template",      $this->expandedContent);
		$this->expandedContent = preg_replace("/<\/template>([^<]+?(?=<))<\/root>/",  "</template><template>$1</template></root>",   $this->expandedContent);
		$this->expandedContent = preg_replace("/<\/template>([^<]+?(?=<))<h/",        "</template><template>$1</template><h",        $this->expandedContent);
		$this->expandedContent = preg_replace("/<\/h>([^<]+?(?=<))<template/",        "</h><template>$1</template><template",        $this->expandedContent);
		
		foreach(simplexml_load_string($this->expandedContent)->template as $template) {
			yield new Parser($template);
		}
	}
	
	/**
	* executor for the API-request, delegating the expansion to the match method
	*
	* @return Parsetree  returns a call to it's own match method
	* @access public
	*/
	public function expand() {
		$parsetree = new APIRequest($this->url);
		$parsetree->setCookieFile($this->cookiefile);
		$parsetree->addToGetFields("action", "parse");
		$parsetree->addToGetFields("prop", "parsetree");
		$parsetree->addToGetFields("format", "xml");
		$parsetree->addToPostFields("text", $this->content);
		$this->expandedContent = (String)$parsetree->execute()->expandtemplates->parsetree;
		return $this->match();
	}
	
	/**
	* getter for the API-request for executing multiple requests at once
	*
	* @return CurlHandle  a reference to the request handle
	* @access public
	*/
	public function &getRequest() {
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