<?php
/**
* A class for logging requests and responses
*
* The class Logger helps in logging all synchronous requests and responses send from a bot
* It is activated in the bot itself and does everything from there on its own
*
* @method bool isLogging()
* @method void startLogging()
* @method void stopLogging()
* @method void logRequest(string $url, array $get, array $post)
* @method void logResponse(CurlHandle $res)
* @method string formatTimestamp()
*/
class Logger {
	private bool $isLogging = false;
	private string $logfile;
	private mixed $file;
	
	/**
	* constructor for class Logger
	*
	* @param string $logfile  the name of the logfile that will be used for logging
	* @access public
	*/
	public function __construct(string $logfile) {
		$this->logfile = $logfile;
	}
	
	/**
	* destructor for class Logger
	* makes sure file writer is closed
	*
	* @access public
	*/
	public function __destruct() {
		if($this->isLogging) { $this->stopLogging(); }
	}
	
	/**
	* getter to check if Logger is actively logging requests
	*
	* @return bool  true if the logger is logging, false otherwise
	* @access public
	*/
	public function isLogging() : bool {
		return $this->isLogging;
	}
	
	/**
	* start the logger with the current log file
	*
	* @access public
	*/
	public function startLogging() : void {
		$this->file = fopen($this->logfile, "a");
		if($this->file === false) { throw new Exception("Could not set up Logger, file could not be opened."); }
		$this->isLogging = true;
	}
	
	/**
	* stop the logger
	*
	* @access public
	*/
	public function stopLogging() : void {
		if(isset($this->file)) { fclose($this->file); }
		$this->isLogging = false;
	}
	
	/**
	* logs a request
	*
	* @param string $url  the url of the request
	* @param array $get   the get part of the request
	* @param array $post  the post part of the request
	* @access public
	*/
	public function logRequest(string $url, array $get, array $post) : void {
		if(!$this->isLogging()) { return; }
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
	public function logResponse(CurlHandle &$res) : void {
		if(!$this->isLogging()) { return; }
		fwrite($this->file, "[" . $this->formatTimestamp() . "][RESPONSE]");
		fwrite($this->file, "\tResponse: " . curl_getinfo($res, CURLINFO_RESPONSE_CODE));
		fwrite($this->file, "\tLoaded " . curl_getinfo($res, CURLINFO_CONTENT_LENGTH_DOWNLOAD) . " bytes");
		fwrite($this->file, " in " . round(curl_getinfo($res, CURLINFO_TOTAL_TIME_T) / 1000) . " ms");
		fwrite($this->file, PHP_EOL);
	}
	
	/**
	* formats a timestamp for logging
	*
	* @return string  the formatted date
	* @access private
	*/
	private function formatTimestamp() : string {
		return date_format(new DateTime(), "c");
	}
}