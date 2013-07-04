<?php

namespace core;
use exceptions\RequestException;

/**
 * This class is responsible for storing information about the current request
 *
 * @author     Jesse Cascio
 * @package    SwiftMVC
 * @subpackage core  
 * @copyright  jessesnet.com
 * @version    1.0
 * @link       
 * @todo       Next version will allow ability to specify which REQUEST data to accept
 */

class Request
{
	/**
	 * @var string - Current request uri
	 */	
	private $_uri  = '';

	/**
	 * @var array - Array of request data
	 */	
	private $_data = array();
	
	/**
	 * Defines the needed constants, time zone, error displaying
	 * @param string - Expected a "cleaned" request path from Uri i.e. REQUEST_PATH
	 */	
	public function __construct($uri='')
	{
		$this->_uri = $uri;		
		// set the request data into array
		$this->_setVars();
	}

	/**
	 * Allows for retrieval from the private data array
	 * $this->request->varName, can be called from any controller
	 * @return mixed
	 */
	public function __get($var)
	{
		return isset($this->_data[$var]) ? $this->_data[$var] : '';
	}

	/**
	 * @return string - The current request Uri
	 */
	public function getUri()
	{
		return $this->_uri;
	}

	/**
	 * Terminates application if current request is not an ajax request
	 * @throws RequestException
	 */
	public function requireAjax()
	{
		if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
			throw new RequestException(__FILE__, __LINE__, "Non ajax call");
		}
	}

	/**
	 * Grab all the vars with the request and store in an array
	 * Also additional params passed via url i.e. controller/action/var1/value1
	 * Variable need to use urlencode() when appended this way
	 */
	private function _setVars()
	{
		// break up the uri
		$paths = explode('/',$this->_uri);
		
		array_shift($paths); // remove controller
		array_shift($paths); // remove action

		// any left over data will be vars
		if (count($paths) > 0) {
			foreach ($paths as $kV) {
				if (isset($paths[0]) && trim($paths[0]) && isset($paths[1]) && trim($paths[1]))	{
					$key = array_shift($paths);
					$this->_data[$key] = urldecode(array_shift($paths));
				}
			}
		}

		// grab all the request data, sanitization should be handled when data used
		foreach ($_REQUEST as $key => $value) {
			$this->_data[$key] = $value;
		}
	}
}	

?>