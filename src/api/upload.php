<?php
/**
* A class for representing API-requests for uploading
*
* The class Upload allows the representation of API-requests for uploading files from a local system to a wiki
*
* @method void setFilepath(String $filepath)
* @method void setFilename(String $filename)
* @method void setText(String $text)
* @method void setComment(String $comment)
* @method void setIgnorewarnings(String $ignorewarnings)
* @method SimpleXMLElement execute(String $token)
*/
class Upload extends Request {
	private String $filepath;
	private String $filename;
	private String $text;
	private String $comment;
	private String $ignorewarnings;
	
	/**
	* constructor for class Upload
	*
	* @param String $url             url to the wiki
	* @param String $filepath        path to the file on the local system
	* @param String $filename        name of the file on the wiki
	* @param String $text            initial text of the file page
	* @param String $comment         comment displayed when uploading the file
	* @param String $ignorewarnings  whether warnings should be ignored when uploading a file or not
	* @access public
	*/
	public function __construct(String $url, String $filepath, String $filename, String $text, String $comment = "", String $ignorewarnings = "1") {
		$this->url = $url;
		$this->filepath = $filepath;
		$this->filename = $filename;
		$this->text = $text;
		$this->comment = $comment;
		$this->ignorewarnings = $ignorewarnings;
		$this->cookiefile = "cookies.txt";
	}
	
	/**
	* setter for the filepath
	*
	* @param String $filepath  the file path to be set
	* @access public
	*/
	public function setFilepath(String $filepath) {
		$this->filepath = $filepath;
	}
	
	/**
	* setter for the filename
	*
	* @param String $filename  the filename to be set
	* @access public
	*/
	public function setFilename(String $filename) {
		$this->filename = $filename;
	}
	
	/**
	* setter for the text
	*
	* @param String $text  the text to be set
	* @access public
	*/
	public function setText(String $text) {
		$this->text = $text;
	}
	
	/**
	* setter for the comment
	*
	* @param String $comment  the comment to be set
	* @access public
	*/
	public function setComment(String $comment) {
		$this->comment = $comment;
	}
	
	/**
	* setter for ignorewarnings
	*
	* @param String $ignorewarnings  the new value to be set
	* @access public
	*/
	public function setIgnorewarnings(String $ignorewarnings) {
		$this->ignorewarnings = $ignorewarnings;
	}
	
	/**
	* executor for the API-request
	*
	* @param String $token      the token required for uploading a file
	* @return SimpleXMLElement  the SimpleXMLElement-representation of the query result
	* @access public
	*/
	public function execute(String $token) {
		$upload = new APIRequest($this->url);
		$upload->setCookieFile($this->cookiefile);
		$upload->addToGetFields("action", "upload");
		$upload->addToGetFields("format", "xml");
		$upload->addToPostFields("file", curl_file_create($this->filepath));
		$upload->addToPostFields("filename", $this->filename);
		$upload->addToPostFields("text", $this->text);
		$upload->addToPostFields("comment", $this->comment);
		$upload->addToPostFields("ignorewarnings", $this->ignorewarnings);
		$upload->addToPostFields("token", $token);
		return $upload->execute();
	}
}
?>