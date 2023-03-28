<?php
/**
* A class for representing TemplateRebuilder
*
* The class TemplateRebuilder allows the representation of rebuilding and manipulating a previously expanded and parsed template
* It allows to change the template by adding to it or removing parameters
* It is also possible to search for parameters and values in a template
*
* @method void setTemplate(mixed $template)
* @method String getTitle()
* @method TemplateRebuilder setTitle(String $title)
* @method mixed getParam(String $value)
* @method Generator|mixed getParams()
* @method mixed getValue(String $param)
* @method boolean contains(String $param)
* @method mixed paramContains(String $value)
* @method TemplateRebuilder removeParam(String $param)
* @method TemplateRebuilder addParamBefore(String $before, String $newParam, String $value)
* @method TemplateRebuilder addParamAfter(String $after, String $newParam, String $value)
* @method TemplateRebuilder addParam(String $param, String $value)
* @method TemplateRebuilder setParam(String $param, String $value)
* @method TemplateRebuilder renameParam(String $oldName, String $newName)
* @method TemplateRebuilder replaceString(String $search, String $replace, int $limit)
* @method String rebuild()
* @method mixed rebuildParam(String $param)
*/
class TemplateRebuilder {
	private mixed $template;
	private String $title;
	
	/**
	* constructor for class TemplateRebuilder
	*
	* @param mixed $template  the previously expanded and parsed template
	* @param String $title    the title of the template
	* @access public
	*/
	public function __construct(mixed $template, String $title = "") {
		$this->template = $template;
		$this->title = $title;
	}
	
	/**
	* setter for the template
	*
	* @param mixed $template  the previously expanded and parsed template
	* @access public
	*/
	public function setTemplate(mixed $template) {
		$this->template = $template;
	}
	
	/**
	* getter for the title
	*
	* @return String  the title of the template
	* @access public
	*/
	public function getTitle() {
		return $this->title;
	}
	
	/**
	* setter for the name of the template
	*
	* @param String $title       the new name of the template
	* @return TemplateRebuilder  itself to allow the chaining of calls
	* @access public
	*/
	public function setTitle(String $title) {
		$this->title = $title;
		return $this;
	}
	
	/**
	* getter for a parameter name given a value
	*
	* @param String $value  the value to look for
	* @return mixed         the name of the first parameter of which the value equals the value to look for, false if none is found
	* @access public
	*/
	public function getParam(String $value) {
		foreach($this->template as $template) {
			if(trim($template["value"]) == $value) {
				return $template["name"];
			}
		}
		return false;
	}
	
	/**
	* generator for all parameters of a template
	*
	* @return Generator|mixed   yields every parameter of the template or the entire template, if the template is just a string
	* @access public
	*/
	public function getParams() {
		if(is_array($this->template)) {
			foreach($this->template as $template) {
				if(!isset($template["name"])) continue; // ignore, probably a parser function
				yield $template["name"] => $template["value"];
			}
		} else {
			yield $this->template;
		}
	}
	
	/**
	* getter for the value given a parameter name
	*
	* @param String $param  the parameter name to look for
	* @return mixed         the value of the parameter if the parameter name exists, false if it doesn't
	* @access public
	*/
	public function getValue(String $param) {
		if(isset($this->template[$param])) {
			return $this->template[$param]["value"];
		} else {
			return false;
		}
	}
	
	/**
	* check if a given parameter is set in the template
	*
	* @param String $param  the parameter to look for
	* @return boolean       true if the parameter exists, false otherwise
	* @access public
	*/
	public function contains(String $param) {
		return isset($this->template[$param]);
	}
	
	/**
	* getter for a parameter name of which the value contains the given value
	*
	* @param String $value  the value to look for in all of the values
	* @return mixed         the name of first parameter of which the value contains the value to look for, false if none is found
	* @access public
	*/
	public function paramContains(String $value) {
		foreach($this->template as $template) {
			if(strpos($template["value"], $value) !== false) {
				return $template["name"];
			}
		}
		return false;
	}
	
	/**
	* remove an existing parameter from the template
	*
	* @param String $param       the name of the parameter to remove
	* @return TemplateRebuilder  itself to allow the chaining of calls
	* @access public
	*/
	public function removeParam(String $param) {
		unset($this->template[$param]);
		return $this;
	}
	
	/**
	* add a parameter and a value to the template. Will not change the template if the parameter already exists
	*
	* @param String $param       the name of the parameter to add
	* @param String $value       the content of the value to add
	* @return TemplateRebuilder  itself to allow the chaining of calls
	* @access public
	*/
	public function addParam(String $param, String $value) {
		if($this->contains($param)) {
			return $this;
		}
		$this->template[trim($param)]["name"] = $param;
		$this->template[trim($param)]["value"] = $value;
		return $this;
	}
	
	/**
	* add a parameter and a value before another parameter
	* if $before is not set in the template it will attempt to add the parameter at the end of the template
	* if the new parameter is already set the template will not be changed
	*
	* @param String $before      the name of the param before which the new param should be added
	* @param String $param       the name of the parameter to add
	* @param String $value       the content of the value to add
	* @return TemplateRebuilder  itself to allow the chaining of calls
	* @access public
	*/
	public function addParamBefore(String $before, String $newParam, String $value) {
		if(!$this->contains($before)) {
			return $this->addParam($param, $value);
		}
		
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
	* @param String $after       the name of the param after which the new param should be added
	* @param String $param       the name of the parameter to add
	* @param String $value       the content of the value to add
	* @return TemplateRebuilder  itself to allow the chaining of calls
	* @access public
	*/
	public function addParamAfter(String $after, String $newParam, String $value) {
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
	* add or overwrite an existing parameter and a value to a template
	*
	* @param String $param       the name of the parameter to add
	* @param String $value       the content of the value to add
	* @return TemplateRebuilder  itself to allow the chaining of calls
	* @access public
	*/
	public function setParam(String $param, String $value) {
		$this->template[trim($param)]["name"] = $param;
		$this->template[trim($param)]["value"] = $value;
		return $this;
	}
	
	/**
	* rename a parameter in a template without changing it's value. If the parameter does not exists, the template will not be changed
	*
	* @param String $oldName     the old name of the parameter
	* @param String $newName     the new name of the parameter
	* @return TemplateRebuilder  itself to allow the chaining of calls
	* @access public
	*/
	public function renameParam(String $oldName, String $newName) {
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
	* allow replacement if a template represents a string
	*
	* @param String $search      the search string
	* @param String $replace     the replacement string
	* @param int $limit          a limit for how many replacements will be executed
	* @return TemplateRebuilder  itself to allow the chaining of calls
	* @throws Exception          if the template is not a string
	* @access public
	*/
	public function replaceString(String $search, String $replace, int $limit = 0) {
		if(gettype($this->template) !== "string") throw new Error("String replace on actual templates is not supported. Use the other methods to replace on the name, parameters or values or use a regular string replace on the content of a page.");
		
		$this->template = str_replace($search, $replace, $this->template, $limit);
		return $this;
	}
	
	/**
	* rebuilds the template from the parameters
	*
	* @return String  the String representation of the template without opening and closing braces as well as the name of the template itself
	* @access public
	*/
	public function rebuild() {
		if(empty($this->title)) {
			return $this->template;
		} else {
			$s = "{{" . $this->getTitle();
			if(!empty($this->template)) {
				foreach($this->template as $param) {
					if(is_array($param["value"])) {
						if(is_numeric($param["name"])) {
							if(trim($param["name"]) == 1) {
								$s .= "|";
							} else {
								for($i = 2; $i <= $param["name"]; $i++) {
									if(!isset($this->template[$i])) {
										$s .= "|".$param["name"]."=";
										break;
									} else if($i == trim($param["name"])) {
										$s .= "|";
									}
								}
							}
						} else {
							$s .= "|".$param["name"]."=";
						}
						
						foreach($param["value"] as $subtemplates) {
							if(!is_array($subtemplates)) {
								$s .= $subtemplates;
							} else {
								foreach($subtemplates as $subTemplateName => $subTemplateValues) {
									$templateRebuilder = new TemplateRebuilder((gettype($subTemplateValues) === "string" ? array() : $subTemplateValues), $subTemplateName);
									$s .= $templateRebuilder->rebuild();
								}
							}
						}
					} else {
						if(is_numeric($param["name"])) {
							if(trim($param["name"]) == 1) {
								$s .= "|".$param["value"];
							} else {
								for($i = 2; $i <= $param["name"]; $i++) {
									if(!isset($this->template[$i])) {
										$s .= "|".$param["name"]."=".$param["value"];
										break;
									} else if($i == trim($param["name"])) {
										$s .= "|".$param["value"];
									}
								}
							}
						} else {
							$s .= "|".$param["name"]."=".$param["value"];
						}
					}
				}
			}
			return $s . "}}";
		}
	}
	
	/**
	* rebuilds the values of a given parameter
	*
	* @param String $param  the name of the param to rebuild
	* @return mixed         the string representation of the parameter. False if the parameter was not set or does not exist
	* @access public
	*/
	public function rebuildParam(String $param) {
		$value = $this->getValue($param);
		if($value === false) {
			return false;
		}
		if(gettype($value) == "string") {
			return $this->getValue($param);
		} else {
			$s = "";
			foreach($value as $part) {
				if(is_array($part)) {
					foreach($part as $subTemplateName => $subTemplateValues) {
						$templateRebuilder = new TemplateRebuilder((gettype($subTemplateValues) === "string" ? array() : $subTemplateValues), $subTemplateName);
						$s .= $templateRebuilder->rebuild();
					}
				} else {
					$s .= $part;
				}
			}
			return $s;
		}
	}
}
?>