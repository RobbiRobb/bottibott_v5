<?php
spl_autoload_register(function($class) {require(strtolower($class).".php");});

/**
* A class for logging requests and responses
*
* The class Logger helps in logging all synchronous requests and responses send from a bot
* It is activated in the bot itself and does everything from there on its own
*
* @method bool isLogging()
* @method void startLogging()
* @method void stopLogging()
* @method void logRequest(String $url, Array $get, Array $post)
* @method void logResponse(CurlHandle $res)
* @method String formatTimestamp()
*/
class Logger {
	private bool $isLogging = false;
	private ?String $logfile = null;
	private $file = null;
	
	/**
	* constructor for class Logger
	*
	* @param String $logfile  the name of the logfile that will be used for logging
	* @access public
	*/
	public function __construct(String $logfile) {
		$this->logfile = $logfile;
	}
	
	/**
	* destructor for class Logger
	* makes sure file writer is closed
	*
	* @access public
	*/
	public function __destruct() {
		if($this->isLogging) $this->stopLogging();
	}
	
	/**
	* getter to check if Logger is actively logging requests
	*
	* @access public
	*/
	public function isLogging() {
		return $this->isLogging;
	}
	
	/**
	* start the logger with the current log file
	*
	* @access public
	*/
	public function startLogging() {
		$this->file = fopen($this->logfile, "a");
		if($this->file === false) throw new Error("Could not set up Logger, file could not be opened.");
		$this->isLogging = true;
	}
	
	/**
	* stop the logger
	*
	* @access public
	*/
	public function stopLogging() {
		if($this->file !== null) fclose($this->file);
		$this->isLogging = false;
	}
	
	/**
	* logs a request
	*
	* @param String $url  the url of the request
	* @param Array $get   the get part of the request
	* @param Array $post  the post part of the request
	* @access public
	*/
	public function logRequest(String $url, Array $get, Array $post) {
		if(!$this->isLogging()) return;
		fwrite($this->file, "[" . $this->formatTimestamp() . "][REQUEST]");
		fwrite($this->file, "\tRequesting " . $url . "?" . http_build_query($get));
		fwrite($this->file, " with body size of " . strlen(implode("", array_keys($post))) + strlen(implode("", array_values($post))));
		fwrite($this->file, PHP_EOL);
	}
	
	/**
	* logs a response
	*
	* @param CurlHandle $res  the response of an executed curl request
	* @access public
	*/
	public function logResponse(CurlHandle &$res) {
		if(!$this->isLogging()) return;
		fwrite($this->file, "[" . $this->formatTimestamp() . "][RESPONSE]");
		fwrite($this->file, "\tResponse: " . curl_getinfo($res, CURLINFO_RESPONSE_CODE));
		fwrite($this->file, "\tLoaded " . curl_getinfo($res, CURLINFO_CONTENT_LENGTH_DOWNLOAD) . " bytes");
		fwrite($this->file, " in " . round(curl_getinfo($res, CURLINFO_TOTAL_TIME_T) / 1000) . " ms");
		fwrite($this->file, PHP_EOL);
	}
	
	/**
	* formats a timestamp for logging
	*
	* @access private
	*/
	private function formatTimestamp() {
		return date_format(new DateTime(), "c");
	}
}
?>