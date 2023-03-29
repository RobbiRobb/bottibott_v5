<?php
/**
* A class representing templates
*
* The class Template allows the representation of rebuilding and manipulating a previously expanded and parsed template
* It allows to change the template by adding to it or removing parameters
* It is also possible to search for parameters and values in a template
*
* @method string getTitle()
* @method void setTitle(string $title)
* @method string getParam(string $param)
* @method Generator|mixed getParams()
* @method bool contains(string $param)
* @method bool strContains(string $needle)
* @method bool isString()
* @method Template addParam(string $param, mixed $value)
* @method Template addParamBefore(string $before, string $newParam, string $value)
* @method Template addParamAfter(string $after, string $newParam, string $value)
* @method Template removeParam(string $param)
* @method Template renameParam(string $oldName, string $newName)
* @method Template setParam(string $param, string $value)
* @method Template setText(string $text)
* @method Template strReplace(string $search, string $replace, int $limit)
* @method string rebuild()
* @method string rebuildParam(string $param)
*/
class Template {
	private mixed $template;
	private string $title;
	
	/**
	* constructor for class Template
	*
	* @param mixed $template  the previously expanded and parsed template
	* @param string $title    the title of the template
	* @access public
	*/
	public function __construct(mixed $template = array(), string $title = "") {
		$this->template = $template;
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
	* setter for the name of the template
	*
	* @param string $title  the new name of the template
	* @return Template      itself to allow the chaining of calls
	* @access public
	*/
	public function setTitle(string $title) : Template {
		$this->title = $title;
		return $this;
	}
	
	/**
	* getter for a specific param
	*
	* @param string $param  the parameter name to look for
	* @return mixed         the value of the parameter
	* @access public
	*/
	public function getParam(string $param) : mixed {
		if(!$this->contains($param)) throw new Error("Parameter {$param} does not exist");
		return $this->template["value"];
	}
	
	/**
	* generator for all parameters of a template
	*
	* @return Generator|mixed  yields every parameter of the template or the entire template, if the template is just a string
	* @access public
	*/
	public function getParams() : Generator {
		if(!is_array($this->template)) throw new Error("Parameter operations are not supported if template is a string");
		if(is_array($this->template)) {
			foreach($this->template as $template) {
				yield $template["name"] => $template["value"];
			}
		} else {
			yield $this->template;
		}
	}
	
	/**
	* check if a given parameter is set in the template
	*
	* @param string $param  the parameter to look for
	* @return boolean       true if the parameter exists, false otherwise
	* @access public
	*/
	public function contains(string $param) : bool {
		if(!is_array($this->template)) throw new Error("Parameter operations are not supported if template is a string");
		return isset($this->template[$param]);
	}
	
	/**
	* check if the template contains a string
	*
	* @param string $needle  the needle to search for
	* @return bool           true if the template contains the string, false otherwise
	* @access public
	*/
	public function strContains(string $needle) : bool {
		if(!$this->isString()) throw new Error("string operations are not supported of template is not a string");
		return str_contains($this->template, $needle);
	}
	
	/**
	* check if the template is representing a string or an actual template
	*
	* @return bool  true if the template is a string, false otherwise
	* @access public
	*/
	public function isString() : bool {
		return is_string($this->template);
	}
	
	/**
	* add a parameter and a value to the template. Will not change the template if the parameter already exists
	*
	* @param string $param  the name of the parameter to add
	* @param string $value  the content of the value to add
	* @return Template      itself to allow the chaining of calls
	* @access public
	*/
	public function addParam(string $param, mixed $value) : Template {
		if(!is_array($this->template)) throw new Error("Parameter operations are not supported if template is a string");
		if($this->contains($param)) return $this;
		$this->template[trim($param)]["name"] = $param;
		$this->template[trim($param)]["value"] = $value;
		return $this;
	}
	
	/**
	* add a parameter and a value before another parameter
	* if $before is not set in the template it will attempt to add the parameter at the end of the template
	* if the new parameter is already set the template will not be changed
	*
	* @param string $before  the name of the param before which the new param should be added
	* @param string $param   the name of the parameter to add
	* @param string $value   the content of the value to add
	* @return Template       itself to allow the chaining of calls
	* @access public
	*/
	public function addParamBefore(string $before, string $newParam, string $value) : Template {
		if(!is_array($this->template)) throw new Error("Parameter operations are not supported if template is a string");
		if(!$this->contains($before)) return $this->addParam($param, $value);
		
		$newTemplate = array();
		
		foreach($this->getParams() as $param => $paramValue) {
			if(trim($param) === $before) {
				$newTemplate[trim($newParam)] = array("name" => $newParam, "value" => $value);
				$newTemplate[trim($param)] = array("name" => $param, "value" => $paramValue);
			} else {
				$newTemplate[trim($param)] = array("name" => $param, "value" => $paramValue);
			}
		}
		
		$this->template = $newTemplate;
		
		return $this;
	}
	
	/**
	* add a parameter and a value after another parameter
	* if $after is not set in the template it will attempt to add the parameter at the end of the template
	* if the new parameter is already set the template will not be changed
	*
	* @param string $after  the name of the param after which the new param should be added
	* @param string $param  the name of the parameter to add
	* @param string $value  the content of the value to add
	* @return Template      itself to allow the chaining of calls
	* @access public
	*/
	public function addParamAfter(string $after, string $newParam, string $value) : Template {
		if(!is_array($this->template)) throw new Error("Parameter operations are not supported if template is a string");
		if(!$this->contains($after)) {
			return $this->addParam($newParam, $value);
		}
		
		$newTemplate = array();
		
		foreach($this->getParams() as $param => $paramValue) {
			if(trim($param) === $after) {
				$newTemplate[trim($param)] = array("name" => $param, "value" => $paramValue);
				$newTemplate[trim($newParam)] = array("name" => $newParam, "value" => $value);
			} else {
				$newTemplate[trim($param)] = array("name" => $param, "value" => $paramValue);
			}
		}
		
		$this->template = $newTemplate;
		
		return $this;
	}
	
	/**
	* remove an existing parameter from the template
	*
	* @param string $param  the name of the parameter to remove
	* @return Template      itself to allow the chaining of calls
	* @access public
	*/
	public function removeParam(string $param) : Template {
		if(!is_array($this->template)) throw new Error("Parameter operations are not supported if template is a string");
		unset($this->template[$param]);
		return $this;
	}
	
	/**
	* rename a parameter in a template without changing it's value. If the parameter does not exists, the template will not be changed
	*
	* @param string $oldName  the old name of the parameter
	* @param string $newName  the new name of the parameter
	* @return Template        itself to allow the chaining of calls
	* @access public
	*/
	public function renameParam(string $oldName, string $newName) : Template {
		if(!is_array($this->template)) throw new Error("Parameter operations are not supported if template is a string");
		if(!$this->contains($oldName)) {
			return $this;
		}
		
		$newTemplate = array();
		
		foreach($this->getParams() as $param => $paramValue) {
			if(trim($param) === $oldName) {
				$newTemplate[trim($newName)] = array("name" => $newName, "value" => $paramValue);
			} else {
				$newTemplate[trim($param)] = array("name" => $param, "value" => $paramValue);
			}
		}
		
		$this->template = $newTemplate;
		
		return $this;
	}
	
	/**
	* add or overwrite an existing parameter and a value to a template
	*
	* @param string $param  the name of the parameter to add
	* @param string $value  the content of the value to add
	* @return Template      itself to allow the chaining of calls
	* @access public
	*/
	public function setParam(string $param, string $value) : Template {
		if(!is_array($this->template)) throw new Error("Parameter operations are not supported if template is a string");
		$this->template[trim($param)]["name"] = $param;
		$this->template[trim($param)]["value"] = $value;
		return $this;
	}
	
	/**
	* set or overwrite current template with a string
	*
	* @param string $text  the text to set
	* @return Template     itself to allow the chaining of calls
	* @access public
	*/
	public function setText(string $text) : Template {
		$this->template = $text;
		return $this;
	}
	
	/**
	* do a string replace on a text template
	*
	* @param string $search   the search value
	* @param string $replace  the replace value
	* @param int $limit       maximum number of replacements
	* @retunr Template        itself to allow the chaining of calls
	* @access public
	*/
	public function strReplace(string $search, string $replace, int $limit = 0) : Template {
		if(!$this->isString()) throw new Error("string operations are not supported of template is not a string");
		$this->template = str_replace($search, $replace, $this->template, $limit);
		return $this;
	}
	
	/**
	* rebuilds the template from the parameters
	*
	* @return string  the string representation of the template without opening and closing braces as well as the name of the template itself
	* @access public
	*/
	public function rebuild() : string {
		if(is_array($this->template)) {
			$s = "{{" . $this->getTitle();
			foreach($this->template as $param) {
				if(is_numeric($param["name"])) {
					if($param["name"] === "1") {
						$s .= "|";
						if(is_array($param["value"])) {
							foreach($param["value"] as $subTemplate) {
								$s .= $subTemplate->rebuild();
							}
						} else {
							$s .= $param["value"];
						}
					} else {
						for($i = 2; $i <= trim($param["name"]); $i++) {
							if(!isset($this->template[$i])) {
								$s .= "|" . $param["name"] . "=";
								break;
							} else if($i == trim($param["name"])) {
								$s .= "|";
							}
						}
						
						if(is_array($param["value"])) {
							foreach($param["value"] as $subTemplate) {
								$s .= $subTemplate->rebuild();
							}
						} else {
							$s .= $param["value"];
						}
					}
				} else {
					if(is_array($param["value"])) {
						$s .= "|" . $param["name"] . "=";
						foreach($param["value"] as $subTemplate) {
							$s .= $subTemplate->rebuild();
						}
					} else {
						$s .= "|" . $param["name"] . "=" . $param["value"];
					}
				}
			}
			return $s . "}}";
		} else {
			return $this->template;
		}
	}
	
	/**
	* rebuilder for a specific param
	*
	* @param string $param  the parameter name to look for
	* @return string        the string value of the parameter
	* @access public
	*/
	public function rebuildParam(string $param) : string {
		if(!$this->contains($param)) throw new Error("Parameter {$param} does not exist");
		
		if(is_array($this->template[$param]["value"])) {
			$s = "";
			foreach($this->template[$param]["value"] as $subTemplate) {
				$s .= $subTemplate->rebuild();
			}
			return $s;
		} else {
			return $this->template[$param]["value"];
		}
	}
}
?>