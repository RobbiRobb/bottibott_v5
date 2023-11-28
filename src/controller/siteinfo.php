<?php
/**
* A class for representing info about a wiki
*
* The class Siteinfo represents info about a wiki
*
* @method void setProperties(array $properties)
* @method SiteinfoData execute()
*/
class Siteinfo {
	private Bot $bot;
	private array $properties = [];
	
	private static array $allowedProperties = [
		"extensions",
		"extensiontags",
		"fileextensions",
		"functionhooks",
		"interwikimap",
		"magicwords",
		"namespaces",
		"skins",
		"statistics",
		"usergroups",
		"variables"
	];
	
	/**
	* constructor for class Siteinfo
	*
	* @param Bot $bot  a reference to the bot object
	* @param array     an array of properties. Only specific properties are allowed
	* @access public
	*/
	public function __construct(Bot $bot, array $properties = array()) {
		$this->bot = $bot;
		
		foreach($properties as $property) {
			if(!in_array($property, self::$allowedProperties)) {
				throw new Exception("Unsupported property type: " . $property);
			} else {
				array_push($this->properties, $property);
			}
		}
	}
	
	/**
	* setter for properties
	*
	* @param array $properties  the properties to set. Only specific properties are allowed
	* @access public
	*/
	public function setProperties(array $properties) : void {
		$this->properties = [];
		
		foreach($properties as $property) {
			if(!in_array($property, self::$allowedProperties)) {
				throw new Exception("Unsupported property type: " . $property);
			} else {
				array_push($this->properties, $property);
			}
		}
	}
	
	/**
	* executor for Siteinfo
	*
	* @return SiteinfoData  an object containing all requested data about the wiki
	* @access public
	*/
	public function execute() : SiteinfoData {
		$request = new SiteinfoRequest($this->bot->getUrl(), $this->properties);
		$request->setCookieFile($this->bot->getCookieFile());
		$request->setLogger($this->bot->getLogger());
		return new SiteinfoData($request->execute());
	}
}