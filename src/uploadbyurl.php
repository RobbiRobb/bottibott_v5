<?php
spl_autoload_register(function($class) {require(strtolower($class).".php");});

/**
* A class for representing API-requests for uploading by url
*
* The class Uploadbyurl allows the representation of API-requests for uploading files from a url to a wiki
*
* @method void setFileurl(String $fileurl)
* @method void setFilename(String $filename)
* @method void setText(String $text)
* @method void setComment(String $comment)
* @method void setIgnorewarnings(String $ignorewarnings)
* @method SimpleXMLElement execute(String $token)
*/
class Uploadbyurl extends Request {
	private String $fileurl;
	private String $filename;
	private String $text;
	private String $comment;
	private String $ignorewarnings;
	
	/**
	* constructor for class Uploadbyurl
	*
	* @param String $url             url to the wiki
	* @param String $fileurl         url to the file that should be uploaded
	* @param String $filename        name of the file on the wiki
	* @param String $text            initial text of the file page
	* @param String $comment         comment displayed when uploading the file
	* @param String $ignorewarnings  whether warnings should be ignored when uploading a file or not
	* @access public
	*/
	public function __construct(String $url, String $fileurl, String $filename, String $text, String $comment = "", String $ignorewarnings = "1") {
		$this->url = $url;
		$this->fileurl = $fileurl;
		$this->filename = $filename;
		$this->text = $text;
		$this->comment = $comment;
		$this->ignorewarnings = $ignorewarnings;
	}
	
	/**
	* setter for the fileurl
	*
	* @param String $fileurl  the url to the file that should be set
	* @access public
	*/
	public function setFileurl(String $fileurl) {
		$this->fileurl = $fileurl;
	}
	
	/**
	* setter for the filename
	*
	* @param String $filename  the filename that should be set
	* @access public
	*/
	public function setFilename(String $filename) {
		$this->filename = $filename;
	}
	
	/**
	* setter for the text
	*
	* @param String $text  the text that should be set
	* @access public
	*/
	public function setText(String $text) {
		$this->text = $text;
	}
	
	/**
	* setter for the comment
	*
	* @param String $comment  the comment that should be set
	* @access public
	*/
	public function setComment(String $comment) {
		$this->comment = $comment;
	}
	
	/**
	* setter for ignorewarnings
	*
	* @param String $ignorewarnings  the value that should be set
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
		$uploadbyurl = new APIRequest($this->url);
		$uploadbyurl->setCookieFile($this->cookiefile);
		$uploadbyurl->addToGetFields("action", "upload");
		$uploadbyurl->addToGetFields("format", "xml");
		$uploadbyurl->addToPostFields("url", $this->fileurl);
		$uploadbyurl->addToPostFields("filename", $this->filename);
		$uploadbyurl->addToPostFields("text", $this->text);
		$uploadbyurl->addToPostFields("comment", $this->comment);
		$uploadbyurl->addToPostFields("ignorewarnings", $this->ignorewarnings);
		$uploadbyurl->addToPostFields("token", $token);
		return $uploadbyurl->execute();
	}
}
?>