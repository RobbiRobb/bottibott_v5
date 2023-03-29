<?php
/**
* DirectoryLoader for the bot
* Will recursively load files from directory if required
*/
function loadDirectory(String $directory, String $class) {
	$dir = new DirectoryIterator($directory);
	
	foreach($dir as $item) {
		if($item->isDot()) continue;
		
		if($item->isDir()) {
			loadDirectory($item->getRealPath(), $class);
		} else if($item->isFile() && strtolower($item->getExtension()) === "php") {
			if(str_replace("." . $item->getExtension(), "", $item->getFilename()) == strtolower($class)) require_once($item->getRealPath());
		}
	}
}

/**
* Autoloader for the BottiBott v5 MediaWiki bot
* Will automatically include all relevant files to guarantee classes are loaded
*/
spl_autoload_register(function($class) {
	loadDirectory(__DIR__, $class);
});
?>