<?php

namespace exceptions;

/**
 * Handles all log related errors, doesnt log because would cause never ending loop
 *
 * @author     Jesse Cascio
 * @package    SwiftMVC
 * @subpackage exceptions  
 * @copyright  jessesnet.com
 * @version    1.0
 * @link       
 * @todo       
 */

class LogException extends SwiftException
{
	public function __construct($file, $line, $message) 
	{
    	parent::__construct($message);
	}
}	

?>
