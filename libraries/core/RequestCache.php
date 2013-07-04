<?php

namespace core;

/**
 * This class handles all request specific caching operations
 *
 * @author     Jesse Cascio
 * @package    SwiftMVC
 * @subpackage core  
 * @copyright  jessesnet.com
 * @version    1.0
 * @todo       Implement SESSION / COOKIE caching restrictions
 */

class RequestCache extends Cache
{
	/**
	 * @var array - default caching options
	 */
	private $_cache_options = 
		array(
			'caching'  		=> CACHING,				// whether caching is on or off, found in index.php
			'cache_dir'		=> CACHE_PATH,			// absolute path to cache directory
			'cache_info'	=> DEV_ENVIRONMENT,		// whether to output to screen when cache is loaded, only in dev
			'cache_debug'	=> FALSE,			    // whether to log output into a file
			'lifetime' 		=> 10,					// how long before the cache expires, seconds
			'settings'		=> 
				array(		
					'cache_with_get_variables' 		=> FALSE,	// to use/set cache when GET vars present ?
					'cache_with_post_variables' 	=> FALSE,	// to use/set cache when POST vars present ?
					'cache_with_session_variables'	=> TRUE,	// to use/set cache when SESSION vars present ?
					'cache_with_cookie_variables'	=> TRUE 	// to use/set cache when COOKIE vars present ?
				   )
		);

	/**
	 * @var string - current uri
	 */
	private $_request_path;

	/**
	 * @param string - current cleaned uri i.e. REQUEST_PATH from Uri
	 */
	public function __construct($request_path)
	{
		$this->_request_path = $request_path;
	}

	/**
	 * Tries to load the cahce base off the current uri, terminates application if view found
	 * @return bool - returns false on not found
	 */
	public function load()
	{	
		// verify we should be caching
		if (!$this->_verifyCaching()) {
			return FALSE;
		}

		// check whether the cached file exists
		$view_name = md5($this->_request_path);
		$view_file = $this->_cache_options['cache_dir'] . "/" . $view_name;

		if (file_exists($view_file)) {
			// time since the cache was created			
			$elapsed_time = time() - filemtime($view_file);

			// only load cache if still recent enough
			if ($elapsed_time <= (int) $this->_cache_options['lifetime']) {
				// uncompress view and display
				if( ($handle = @fopen($view_file,'r')) !== FALSE) {
					$view = gzuncompress(fread($handle, filesize($view_file)));
					echo $view;
					$this->_cache_options['cache_info'] ? die($this->_cacheInfo()) : die();
				} 
			}
		}

		return FALSE;
	}

	/**
	 * Saves a view to a cache file
	 * @return bool - returns false on not created
	 */
	public function cacheView($view)
	{
		// verify we should be caching
		if ( !$this->_verifyCaching() ) {
			return FALSE;
		}

		$view = gzcompress($view);
		$view_name = md5($this->_request_path);
		// save file
		if (($view_file = @fopen($this->_cache_options['cache_dir']  . '/' . $view_name,'w')) !== FALSE) {
			fwrite($view_file, $view);
        	fclose($view_file);
		}
	}

	/**
	 * @return bool - whether we should be caching this request
	 */
	private function _verifyCaching()
	{
		// first verify we should even be caching
		if ( $this->_cache_options['caching'] === FALSE )
		{
			return FALSE;
		}

		if ( $this->_cache_options['settings']['cache_with_post_variables'] === FALSE && count($_POST) > 0 )
		{	
			return FALSE;
		}

		if ( $this->_cache_options['settings']['cache_with_get_variables'] === FALSE && count($_GET) > 0 )
		{	
			return FALSE;
		}

		// add SESSION and COOKIE support

		return TRUE;
	}

	/**
	 * @return string - message to display when cache file read
	 */
	private function _cacheInfo()
	{	
		$css = "position: absolute;
				left: 500px;
				top: 10px;
				background-color: white;
				padding: 2px 5px;
				color: red;
				font-weight: bold;
				font-size: 16px;
				border:2px solid black;
				font-style:italic";

		return '<span style="' . $css . '">Page From Cache</span>';
	}
}	

?>

