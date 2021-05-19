<?php
spl_autoload_register(function($class) {require(strtolower($class).".php");});

/**
* A class for representing template Parser
*
* The class Parser allows the representation of template Parsers
* It will parse an expanded template and convert it to allow a rebuilding of the original String
*
* @method void setExpandedContent(SimpleXMLElement $expandedContent)
* @method String getTitle()
* @method TemplateRebuilder parse()
* @method mixed parseTemplate()
*/
class Parser {
	private SimpleXMLElement $expandedContent;
	
	/**
	* constructor for class Parser
	*
	* @param SimpleXMLElement $expandedContent  the previously expanded content
	* @access public
	*/
	public function __construct(SimpleXMLElement $expandedContent) {
		$this->expandedContent = $expandedContent;
	}
	
	/**
	* setter for the expandedContent
	*
	* @param SimpleXMLElement $expandedContent  the expandedContent that should be set
	* @access public
	*/
	public function setExpandedContent(SimpleXMLElement $expandedContent) {
		$this->expandedContent = $expandedContent;
	}
	
	/**
	* getter for the title
	*
	* @return String  returns the title of the template
	* @access public
	*/
	public function getTitle() {
		return (String)$this->expandedContent->title;
	}
	
	/**
	* executor of the parser
	*
	* @return TemplateRebuilder  returns a TemplateRebuilder-object that allows the manipulation and rebuilding of a previously expanded template
	* @access public
	*/
	public function parse() {
		return new TemplateRebuilder($this->parseTemplate(), $this->getTitle());
	}
	
	/**
	* actual template parser
	*
	* @return mixed  returns either an array or a String corresponding to the expanded template
	* @access private
	*/
	private function parseTemplate() {
		if(isset($this->expandedContent->part)) {
			$array = array();
			foreach($this->expandedContent->part as $parameters) {
				if(isset($parameters->value->template)) {
					foreach($parameters->value as $subTemplate) {
						$count = count($subTemplate->template);
						for($i = 0; $i < $count; $i++) {
							if(isset($parameters->name['index'])) {
								if(empty(trim((String)$subTemplate->template[$i]->title))) {
									$parser = new Parser($subTemplate->template[$i]);
									$array[trim((String)$parameters->name['index'])]["value"][$i] = $parser->parseTemplate();
								} else {
									$parser = new Parser($subTemplate->template[$i]);
									$array[trim((String)$parameters->name['index'])]["name"] = (String)$parameters->name['index'];
									$array[trim((String)$parameters->name['index'])]["value"][$i][trim((String)$subTemplate->template[$i]->title)] = $parser->parseTemplate();
								}
							} else {
								if(empty(trim((String)$subTemplate->template[$i]->title))) {
									$parser = new Parser($subTemplate->template[$i]);
									$array[trim((String)$parameters->name)]["value"][$i] = $parser->parseTemplate();
								} else {
									$parser = new Parser($subTemplate->template[$i]);
									$array[trim((String)$parameters->name)]["name"] = (String)$parameters->name;
									$array[trim((String)$parameters->name)]["value"][$i][trim((String)$subTemplate->template[$i]->title)] = $parser->parseTemplate();
								}
							}
						}
					}
				} else {
					if(isset($parameters->name['index'])) {
						$array[trim((String)$parameters->name['index'])]["name"] = (String)$parameters->name['index'];
						$array[trim((String)$parameters->name['index'])]["value"] = (String)$parameters->value;
					} else {
						$array[trim((String)$parameters->name)]["name"] = (String)$parameters->name;
						$array[trim((String)$parameters->name)]["value"] = (String)$parameters->value;
					}
				}
			}
			return $array;
		} else {
			return (String)$this->expandedContent;
		}
	}
}
?>