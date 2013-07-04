<?php

namespace exceptions;
use utilities\Log;

/**
 * Handles all log request errors, doesnt log because would cause never ending loop
 *
 * @author     Jesse Cascio
 * @package    SwiftMVC
 * @subpackage exceptions  
 * @copyright  jessesnet.com
 * @version    1.0
 * @link       
 * @todo       
 */

class RequestException extends SwiftException
{
	public function __construct($file, $line, $message) 
	{
		$message = date("Y-m-d H:i:s") . " - REQUEST ERROR ('".$file."', #".$line.") : ".$message;
		
		Log::write($message);

        parent::__construct($message);
	}
}	

?>