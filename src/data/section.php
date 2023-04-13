<?php
/**
* A class for representing sections of pages
*
* The class Section represents a section on a page, storing all relevant data
*
* @method int getToclevel()
* @method int getLevel()
* @method string getTitle()
* @method string getNumber()
* @method int getIndex()
* @method int getOffset()
* @method string getAnchor()
*/
class Section {
	private readonly int $toclevel;
	private readonly int $level;
	private readonly string $title;
	private readonly string $number;
	private readonly int $index;
	private readonly int $offset;
	private readonly string $anchor;
	
	/**
	* constructor for class Section
	*
	* @param int $toclevel   the toclevel of the section
	* @param int $level      the level of the section
	* @param string $title   the title of the section
	* @param string $number  the number of the section as it appears in the toc
	* @param int $index      the index of the section
	* @param int offset      the offset of the section on the page in bytes
	* @param string $anchor  the urlencoded anchor of the section
	* @access public
	*/
	public function __construct(
		int $toclevel,
		int $level,
		string $title,
		string $number,
		int $index,
		int $offset,
		string $anchor
	) {
		$this->toclevel = $toclevel;
		$this->level = $level;
		$this->title = $title;
		$this->number = $number;
		$this->index = $index;
		$this->offset = $offset;
		$this->anchor = $anchor;
	}
	
	/**
	* getter for the toclevel
	*
	* @return int  the toclevel of the section
	* @access public
	*/
	public function getToclevel() : int {
		return $this->toclevel;
	}
	
	/**
	* getter for the level
	*
	* @return int  the level of the section
	* @access public
	*/
	public function getLevel() : int {
		return $this->level;
	}
	
	/**
	* getter for the title
	*
	* @return string  the title of the section
	* @access public
	*/
	public function getTitle() : string {
		return $this->title;
	}
	
	/**
	* getter for the number
	*
	* @return string  the number of the section
	* @access public
	*/
	public function getNumber() : string {
		return $this->number;
	}
	
	/**
	* getter for the index
	*
	* @return int  the index of the section
	* @access public
	*/
	public function getIndex() : int {
		return $this->index;
	}
	
	/**
	* getter for the offset
	*
	* @return int  the offset of the section in byte
	* @access public
	*/
	public function getOffset() : int {
		return $this->offset;
	}
	
	/**
	* getter for the anchor
	*
	* @return string  the anchor of the section
	* @access public
	*/
	public function getAnchor() : string {
		return $this->anchor;
	}
	
	/**
	* debug function
	*
	* @return array  debug info about this object
	* @access public
	*/
	public function __debugInfo() : array {
		$info = array();
		$info["toclevel"] = $this->toclevel;
		$info["level"] = $this->level;
		$info["title"] = $this->title;
		$info["number"] = $this->number;
		$info["index"] = $this->index;
		$info["offset"] = $this->offset;
		$info["anchor"] = $this->anchor;
		return $info;
	}
}