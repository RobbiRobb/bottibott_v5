<?php
/**
* A class representing info about a wiki
*
* The class SiteinfoData represents meta information about the wiki
*
* @method void parseExtensions(SimpleXMLElement $data)
* @method array getExtensions()
* @method void parseExtensiontags(SimpleXMLElement $data)
* @method array getExtensiontags()
* @method void parseFileextensions(SimpleXMLElement $data)
* @method array getFileextensions()
* @method void parseFunctionhooks(SimpleXMLElement $data)
* @method array getFunctionhooks()
* @method void parseInterwikimap(SimpleXMLElement $data)
* @method array getInterwikimap()
* @method void parseMagiwords(SimpleXMLElement $data)
* @method array getMagicwords()
* @method void parseNamespaces(SimpleXMLElement $data)
* @method array getNamespaces()
* @method void parseSkins(SimpleXMLElement $data)
* @method array getSkins()
* @method void parseStatistics(SimpleXMLElement $data)
* @method array getStatistics()
* @method void parseUsergroups(SimpleXMLElement $data)
* @method array getUsergroups()
* @method void parseVariables(SimpleXMLElement $data)
* @method array getVariables()
*/
class SiteinfoData {
	private array $extensions;
	private array $extensiontags;
	private array $fileextensions;
	private array $functionhooks;
	private array $interwikimap;
	private array $magicwords;
	private array $namespaces;
	private array $skins;
	private array $statistics;
	private array $usergroups;
	private array $variables;
	
	/**
	* constructor for class Siteinfo
	*
	* @param SimpleXMLElement $data  the data returned by the wiki
	* @access public
	*/
	public function __construct(SimpleXMLElement $data) {
		$this->parseExtensions($data);
		$this->parseExtensiontags($data);
		$this->parseFileextensions($data);
		$this->parseFunctionhooks($data);
		$this->parseInterwikimap($data);
		$this->parseMagiwords($data);
		$this->parseNamespaces($data);
		$this->parseSkins($data);
		$this->parseStatistics($data);
		$this->parseUsergroups($data);
		$this->parseVariables($data);
	}
	
	/**
	* parser for all extensions installed on the wiki. Automatically parses all installed extensions
	*
	* @param SimpleXMLElement $data  the data returned by the wiki
	* @access private
	*/
	private function parseExtensions(SimpleXMLElement $data) : void {
		if(!isset($data->query->extensions)) { return; }
		
		$this->extensions = array();
		foreach($data->query->extensions->ext as $ext) {
			$extData = array();
			foreach($ext->attributes() as $key => $value) {
				$extData[$key] = (string)$value;
			}
			array_push($this->extensions, $extData);
		}
	}
	
	/**
	* getter for all extensions installed on the wiki
	*
	* @return array  all extensions installed on the wiki
	* @access public
	*/
	public function getExtensions() : array {
		if(!isset($this->extensions)) { throw new Exception("Extensions are not set"); }
		return $this->extensions;
	}
	
	/**
	* parser for all extensiontags registered on the wiki. Automatically parses all extensiontags
	*
	* @param SimpleXMLElement $data  the data returned by the wiki
	* @access private
	*/
	private function parseExtensiontags(SimpleXMLElement $data) : void {
		if(!isset($data->query->extensiontags)) { return; }
		
		$this->extensiontags = array();
		foreach($data->query->extensiontags->t as $tag) {
			array_push($this->extensiontags, (string)$tag);
		}
	}
	
	/**
	* getter for all extensiontags registered on the wiki
	*
	* @return array  all extensiontags registered on the wiki
	* @access public
	*/
	public function getExtensiontags() : array {
		if(!isset($this->extensiontags)) { throw new Exception("Extensiontags are not set"); }
		return $this->extensiontags;
	}
	
	/**
	* parser for all fileextensions registered on the wiki. Automatically parses all fileextensions
	*
	* @param SimpleXMLElement $data  the data returned by the wiki
	* @access private
	*/
	private function parseFileextensions(SimpleXMLElement $data) : void {
		if(!isset($data->query->fileextensions)) { return; }
		
		$this->fileextensions = array();
		foreach($data->query->fileextensions->fe as $ext) {
			array_push($this->fileextensions, (string)$ext["ext"]);
		}
	}
	
	/**
	* getter for all fileextensions registered on the wiki
	*
	* @return array  all fileextensions registered on the wiki
	* @access public
	*/
	public function getFileextensions() : array {
		if(!isset($this->fileextensions)) { throw new Exception("Fileextensions are not set"); }
		return $this->fileextensions;
	}
	
	/**
	* parser for all functionhooks registered on the wiki. Automatically parses all functionhooks
	*
	* @param SimpleXMLElement $data  the data returned by the wiki
	* @access private
	*/
	private function parseFunctionhooks(SimpleXMLElement $data) : void {
		if(!isset($data->query->functionhooks)) { return; }
		
		$this->functionhooks = array();
		foreach($data->query->functionhooks->h as $hook) {
			array_push($this->functionhooks, (string)$hook);
		}
	}
	
	/**
	* getter for all functionhooks registered on the wiki
	*
	* @return array  all functionhooks registered on the wiki
	* @access public
	*/
	public function getFunctionhooks() : array {
		if(!isset($this->functionhooks)) { throw new Exception("Functionhooks are not set"); }
		return $this->functionhooks;
	}
	
	/**
	* parser for the interwikimap the wiki. Automatically parses the interwikimap
	*
	* @param SimpleXMLElement $data  the data returned by the wiki
	* @access private
	*/
	private function parseInterwikimap(SimpleXMLElement $data) : void {
		if(!isset($data->query->interwikimap)) { return; }
		
		$this->interwikimap = array();
		foreach($data->query->interwikimap->iw as $interwiki) {
			$this->interwikimap[(string)$interwiki["prefix"]] = (string)$interwiki["url"];
		}
	}
	
	/**
	* getter for the interwikimap of the wiki
	*
	* @return array  the interwikimap of the wiki
	* @access public
	*/
	public function getInterwikimap() : array {
		if(!isset($this->interwikimap)) { throw new Exception("Interwikimap is not set"); }
		return $this->interwikimap;
	}
	
	/**
	* parser for all magicwords registered on the wiki. Automatically parses all magicwords
	*
	* @param SimpleXMLElement $data  the data returned by the wiki
	* @access private
	*/
	private function parseMagiwords(SimpleXMLElement $data) : void {
		if(!isset($data->query->magicwords)) { return; }
		
		$this->magicwords = array();
		foreach($data->query->magicwords->magicword as $magicword) {
			$aliases = array();
			foreach($magicword->aliases->alias as $alias) {
				array_push($aliases, (string)$alias);
			}
			$this->magicwords[(string)$magicword["name"]] = $aliases;
		}
	}
	
	/**
	* getter for all magicwords registered on the wiki
	*
	* @return array  all magicwords registered on the wiki
	* @access public
	*/
	public function getMagicwords() : array {
		if(!isset($this->magicwords)) { throw new Exception("Magicwords are not set"); }
		return $this->magicwords;
	}
	
	/**
	* parser for all namespaces registered on the wiki. Automatically parses all namespaces
	*
	* @param SimpleXMLElement $data  the data returned by the wiki
	* @access private
	*/
	private function parseNamespaces(SimpleXMLElement $data) : void {
		if(!isset($data->query->namespaces)) { return; }
		
		$this->namespaces = array();
		foreach($data->query->namespaces->ns as $ns) {
			$this->namespaces[(string)$ns["id"]] = (string)$ns;
		}
	}
	
	/**
	* getter for all namespaces registered on the wiki
	*
	* @return array  all namespaces registered on the wiki
	* @access public
	*/
	public function getNamespaces() : array {
		if(!isset($this->namespaces)) { throw new Exception("Namespaces is not set"); }
		return $this->namespaces;
	}
	
	/**
	* parser for all skins registered on the wiki. Automatically parses all skins
	*
	* @param SimpleXMLElement $data  the data returned by the wiki
	* @access private
	*/
	private function parseSkins(SimpleXMLElement $data) : void {
		if(!isset($data->query->skins)) { return; }
		
		$this->skins = array();
		foreach($data->query->skins->skin as $skin) {
			$this->skins[(string)$skin["code"]] = (string)$skin;
		}
	}
	
	/**
	* getter for all skins registered on the wiki
	*
	* @return array  all skins registered on the wiki
	* @access public
	*/
	public function getSkins() : array {
		if(!isset($this->skins)) { throw new Exception("Skins is not set"); }
		return $this->skins;
	}
	
	/**
	* parser for the statistics of the wiki. Automatically parses all statistics
	*
	* @param SimpleXMLElement $data  the data returned by the wiki
	* @access private
	*/
	private function parseStatistics(SimpleXMLElement $data) : void {
		if(!isset($data->query->statistics)) { return; }
		
		$this->statistics = array();
		foreach($data->query->statistics as $stat) {
			$this->statistics["pages"] = (string)$stat["pages"];
			$this->statistics["articles"] = (string)$stat["articles"];
			$this->statistics["edits"] = (string)$stat["edits"];
			$this->statistics["images"] = (string)$stat["images"];
			$this->statistics["users"] = (string)$stat["users"];
			$this->statistics["activeusers"] = (string)$stat["activeusers"];
			$this->statistics["admins"] = (string)$stat["admins"];
			$this->statistics["jobs"] = (string)$stat["jobs"];
		}
	}
	
	/**
	* getter for site statistics
	*
	* @return array  all site statistics
	* @access public
	*/
	public function getStatistics() : array {
		if(!isset($this->statistics)) { throw new Exception("Statistics is not set"); }
		return $this->statistics;
	}
	
	/**
	* parser for the usergroups available on the wiki. Automatically parses all usergroups
	*
	* @param SimpleXMLElement $data  the data returned by the wiki
	* @access private
	*/
	private function parseUsergroups(SimpleXMLElement $data) : void {
		if(!isset($data->query->usergroups)) { return; }
		
		$this->usergroups = [];
		foreach($data->query->usergroups->group as $group) {
			$usergroup = [];
			
			if(isset($group->rights)) {
				$usergroup["permissions"] = [];
				foreach($group->rights->permission as $permission) {
					array_push($usergroup["permissions"], (string)$permission);
				}
			}
			if(isset($group->add)) {
				$usergroup["add"] = [];
				foreach($group->add->group as $add) {
					array_push($usergroup["add"], (string)$add);
				}
			}
			if(isset($group->remove)) {
				$usergroup["remove"] = [];
				foreach($group->remove->group as $remove) {
					array_push($usergroup["remove"], (string)$remove);
				}
			}
			
			$this->usergroups[(string)$group["name"]] = $usergroup;
		}
	}
	
	/**
	* getter for all usergroups
	*
	* @return array  the usergroups available on the wiki
	* @access public
	*/
	public function getUsergroups() : array {
		if(!isset($this->usergroups)) { throw new Exception("Usergroups are not set"); }
		return $this->usergroups;
	}
	
	/**
	* parser for the variables registered on the wiki. Automatically parses all variables
	*
	* @param SimpleXMLElement $data  the data returned by the wiki
	* @access private
	*/
	private function parseVariables(SimpleXMLElement $data) : void {
		if(!isset($data->query->variables)) { return; }
		
		$this->variables = array();
		foreach($data->query->variables->v as $variable) {
			array_push($this->variables, (string)$variable);
		}
	}
	
	/**
	* getter for variables
	*
	* @return array  the variables registered in the wiki
	* @access public
	*/
	public function getVariables() : array {
		if(!isset($this->variables)) { throw new Exception("Variables is not set"); }
		return $this->variables;
	}
	
	/**
	* debug function
	*
	* @return array  debug info about this object
	* @access public
	*/
	public function __debugInfo() : array {
		$info = array();
		if(isset($this->extensions)) { $info["extensions"] = $this->extensions; }
		if(isset($this->extensiontags)) { $info["extensiontags"] = $this->extensiontags; }
		if(isset($this->fileextensions)) { $info["fileextensions"] = $this->fileextensions; }
		if(isset($this->functionhooks)) { $info["functionhooks"] = $this->functionhooks; }
		if(isset($this->interwikimap)) { $info["interwikimap"] = $this->interwikimap; }
		if(isset($this->magicwords)) { $info["magicwords"] = $this->magicwords; }
		if(isset($this->namespaces)) { $info["namespaces"] = $this->namespaces; }
		if(isset($this->skins)) { $info["skins"] = $this->skins; }
		if(isset($this->statistics)) { $info["statistics"] = $this->statistics; }
		if(isset($this->usergroups)) { $info["usergroups"] = $this->usergroups; }
		if(isset($this->variables)) { $info["variables"] = $this->variables; }
		return $info;
	}
}