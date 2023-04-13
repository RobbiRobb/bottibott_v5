<?php
/**
* A class for representing users
*
* The class User represents users and allows the storage of data on them
*
* @method string getUsername()
* @method int getId()
* @method void setEditcount(int $editcount)
* @method int getEditcount()
* @method void setRegistration(int $registration)
* @method int getRegistration()
* @method void setRecentactions(int $recentactions)
* @method int getRecentactions()
* @method void addContribution(Revision $contrib)
* @method Generator|Revision getContributions()
* @method void addGroup(string $group)
* @method Generator|string getGroups()
* @method void addRight(string $right)
* @method Generator|string getRights()
*/
class User {
	private readonly string $username;
	private readonly int $id;
	private readonly int $editcount;
	private readonly int $registration;
	private readonly int $recentactions;
	private array $contributions;
	private array $groups;
	private array $rights;
	
	/**
	* constructor for class User
	*
	* @param string $username  the name of the user
	* @param int $id           the id of the user
	* @access public
	*/
	public function __construct(string $username, int $id) {
		$this->username = $username;
		$this->id = $id;
	}
	
	/**
	* gettfer for the username
	*
	* @return string  the username
	* @access public
	*/
	public function getUsername() : string {
		return $this->username;
	}
	
	/**
	* getter for the id
	*
	* @return int  the id
	* @access public
	*/
	public function getId() : int {
		return $this->id;
	}
	
	/**
	* setter for the editcount
	*
	* @param int $editcount  the new editcount
	* @access public
	*/
	public function setEditcount(int $editcount) : void {
		$this->editcount = $editcount;
	}
	
	/**
	* getter for the editcount
	*
	* @return int  the editcount
	* @access public
	*/
	public function getEditcount() : int {
		if(!isset($this->editcount)) { throw new Exception("Editcount is not set"); }
		return $this->editcount;
	}
	
	/**
	* setter for the registration date
	*
	* @param int $registration  the registration date as a UNIX timestamp
	* @access public
	*/
	public function setRegistration(int $registration) : void {
		$this->registration = $registration;
	}
	
	/**
	* getter for the registration date
	*
	* @return int  the registration date as a UNIX timestamp
	* @access public
	*/
	public function getRegistration() : int {
		if(!isset($this->registration)) { throw new Exception("Registration is not set"); }
		return $this->registration;
	}
	
	/**
	* setter for the recentactions
	*
	* @param int $recentactions  the new recentactions
	* @access public
	*/
	public function setRecentactions(int $recentactions) : void {
		$this->recentactions = $recentactions;
	}
	
	/**
	* getter for the recentactions
	*
	* @return int  the recentactions
	* @access public
	*/
	public function getRecentactions() : int {
		if(!isset($this->recentactions)) { throw new Exception("Recentactions are not set"); }
		return $this->recentactions;
	}
	
	/**
	* adder for contributions
	*
	* @param Revision $contrib  the contribution to add
	* @access public
	*/
	public function addContribution(Revision &$contrib) : void {
		if(!isset($this->contributions)) { $this->contributions = array(); }
		array_push($this->contributions, $contrib);
	}
	
	/**
	* getter for all contributions
	*
	* @return Generator|Revision  all contributions of a user
	* @access public
	*/
	public function &getContributions() : Generator|Revision {
		if(!isset($this->contributions)) { throw new Exception("Contributions are not set"); }
		foreach($this->contributions as $contrib) {
			yield $contrib;
		}
	}
	
	/**
	* adder for groups
	*
	* @param string $group  the group to add
	* @access public
	*/
	public function addGroup(string $group) : void {
		if(!isset($this->groups)) { $this->groups = array(); }
		array_push($this->groups, $group);
		asort($this->groups);
	}
	
	/**
	* getter for all groups
	*
	* @return Generator|string  a generator of all groups the user is in
	* @access public
	*/
	public function getGroups() : Generator|string {
		if(!isset($this->groups)) { throw new Exception("Groups are not set"); }
		foreach($this->groups as $group) {
			yield $group;
		}
	}
	
	/**
	* adder for rights
	*
	* @param string $right  the right to add
	* @access public
	*/
	public function addRight(string $right) : void {
		if(!isset($this->rights)) { $this->rights = array(); }
		array_push($this->rights, $right);
		asort($this->rights);
	}
	
	/**
	* getter for all rights
	*
	* @return Generator|string  a generator of all rights
	* @access public
	*/
	public function getRights() : Generator|string {
		if(!isset($this->rights)) { throw new Exception("Rights are not set"); }
		foreach($this->rights as $right) {
			yield $right;
		}
	}
	
	/**
	* debug function
	*
	* @return array  debug info about this object
	* @access public
	*/
	public function __debugInfo() : array {
		$info = array();
		if(isset($this->username)) { $info["username"] = $this->username; }
		if(isset($this->id)) { $info["id"] = $this->id; }
		if(isset($this->editcount)) { $info["editcount"] = $this->editcount; }
		if(isset($this->registration)) { $info["registration"] = $this->registration; }
		if(isset($this->recentactions)) { $info["recentactions"] = $this->recentactions; }
		if(isset($this->contributions)) { $info["contributions"] = $this->contributions; }
		if(isset($this->groups)) { $info["groups"] = $this->groups; }
		if(isset($this->rights)) { $info["rights"] = $this->rights; }
		return $info;
	}
}