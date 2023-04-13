<?php
/**
* A class for representing TemplateParameters
*
* The class TemplateParameter represents parameters of templates, storing their name,
* value and whether they are accessed by an index or not
*
* @method string getName()
* @method array|string getValue()
* @method bool isIndex()
*/
class TemplateParameter {
	private string $name;
	private array|string $value;
	private bool $isIndex;
	
	/**
	* constrcutor for class TemplateParameter
	*
	* @param string $name         the name of the parameter
	* @param array|string $value  the value of the parameter
	* @param bool $isIndex        whether the parameter is an index or not
	* @access public
	*/
	public function __construct(string $name, array|string $value, bool $isIndex = false) {
		$this->name = $name;
		$this->value = $value;
		$this->isIndex = $isIndex;
	}
	
	/**
	* getter for the name
	*
	* @return string  the name of the parameter
	* @access public
	*/
	public function getName() : string {
		return $this->name;
	}
	
	/**
	* getter for the value
	*
	* @return array|string  the value of the parameter
	* @access public
	*/
	public function getValue() : array|string {
		return $this->value;
	}
	
	/**
	* getter to check if the parameter is an index or not
	*
	* @return bool  true if the parameter is an index, false otherwise
	* @access public
	*/
	public function isIndex() : bool {
		return $this->isIndex;
	}
	
	/**
	* debug function
	*
	* @return array  debug info about this object
	* @access public
	*/
	public function __debugInfo() : array {
		$info = array();
		$info["name"] = $this->name;
		$info["value"] = $this->value;
		$info["isIndex"] = $this->isIndex;
		return $info;
	}
}