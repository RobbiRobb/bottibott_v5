<?php
spl_autoload_register(function($class) {require(strtolower($class).".php");});

/**
* A class for representing TemplateRebuilder
*
* The class TemplateRebuilder allows the representation of rebuilding and manipulating a previously expanded and parsed template
* It allows to change the template by adding to it or removing parameters
* It is also possible to search for parameters and values in a template
*
* @method void setTemplate(mixed $template)
* @method String getTitle()
* @method mixed getParam(String $value)
* @method mixed getValue(String $param)
* @method boolean contains(String $param)
* @method mixed paramContains(String $value)
* @method TemplateRebuilder removeParam(String $param)
* @method TemplateRebuilder addParam(String $param, String $value)
* @method TemplateRebuilder setParam(String $param, String $value)
* @method TemplateRebuilder renameParam(String $oldName, String $newName)
* @method String rebuild()
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
		
		$offset = array_search($oldName, array_keys($this->template));
		
		$this->template = array_merge(
			array_slice($this->template, 0, $offset),
			array(trim($newName) => array("name" => $newName, "value" => $this->template[trim($oldName)]["value"])),
			array_slice($this->template, $offset)
		);
		unset($this->template[trim($oldName)]);
		
		return $this;
	}
	
	/**
	* rebuilds the template from the parameters
	*
	* @return String  the String representation of the template without opening and closing braces as well as the name of the template itself
	* @access public
	*/
	public function rebuild() {
		if(gettype($this->template) == "string") {
			return $this->template;
		} else {
			$s = "";
			foreach($this->template as $paramName => $param) {
				if(is_array($param["value"])) {
					if(is_numeric($param["name"])) {
						if(trim($param["name"]) == 1) {
							$s .= "|";
						} else {
							for($i = 2; $i <= $paramName; $i++) {
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
								$templateRebuilder = new TemplateRebuilder($subTemplateValues);
								$s .= "{{".$subTemplateName.$templateRebuilder->rebuild()."}}";
							}
						}
					}
				} else {
					if(is_numeric($param["name"])) {
						if(trim($param["name"]) == 1) {
							$s .= "|".$param["value"];
						} else {
							for($i = 2; $i <= $paramName; $i++) {
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
			return $s;
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
					foreach($part as $subTemplateName => $template) {
						$templateRebuilder = new TemplateRebuilder($template, $subTemplateName);
						$s .= "{{".$subTemplateName.$templateRebuilder->rebuild()."}}";
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