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
* method Parsetree expand()
*/
class Parsetree extends Request {
	private String $content;
	
	/**
	* constructor for class Parsetree
	*
	* @param String $url      the url to the wiki
	* @param String $content  the content that should be expanded
	* @access public
	*/
	public function __construct(String $url, String $content) {
		$this->url = $url;
		$this->content = $content;
		$this->cookiefile = "cookies.txt";
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
	* matcher and replacer for unmatched Strings in expandedContent
	*
	* @param String $expandedContent  the content on which the matcher should work
	* @return Generator|Parser        a Parser-object representing the expanded source code including Strings
	* @access private
	*/
	private function match(String $expandedContent) {
		$expandedContent = preg_replace("/<value>([^<]+?(?=<))<template/",      "<value><template>$1</template><template",     $expandedContent);
		$expandedContent = preg_replace("/<\/template>([^<]+?(?=<))<\/value>/", "</template><template>$1</template></value>",  $expandedContent);
		$expandedContent = preg_replace("/<\/template>([^<]+?(?=<))<template/", "</template><template>$1</template><template", $expandedContent);
		$expandedContent = preg_replace("/<root>([^<]+?(?=<))<template/",       "<root><template>$1</template><template",      $expandedContent);
		$expandedContent = preg_replace("/<\/template>([^<]+?(?=<))<\/root>/",  "</template><template>$1</template></root>",   $expandedContent);
		$expandedContent = preg_replace("/<\/template>([^<]+?(?=<))<h/",        "</template><template>$1</template><h",        $expandedContent);
		$expandedContent = preg_replace("/<\/h>([^<]+?(?=<))<template/",        "</h><template>$1</template><template",        $expandedContent);
		
		foreach(simplexml_load_string($expandedContent)->template as $template) {
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
		$parsetree->addToGetFields("action", "expandtemplates");
		$parsetree->addToGetFields("prop", "parsetree");
		$parsetree->addToGetFields("format", "xml");
		$parsetree->addToPostFields("text", $this->content);
		$queryResult = (String)$parsetree->execute()->expandtemplates->parsetree;
		return $this->match($queryResult);
	}
}
?>