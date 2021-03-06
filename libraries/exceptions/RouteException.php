<?php

namespace exceptions;
use utilities\Log;

/**
 * Handles all route related errors
 *
 * @author     Jesse Cascio
 * @package    SwiftMVC
 * @subpackage exceptions  
 * @copyright  jessesnet.com
 * @version    1.0
 * @link       
 * @todo       
 */

class RouteException extends SwiftException
{
	public function __construct($file, $line, $message) 
	{
		$message = date("Y-m-d H:i:s") . " - ROUTE ERROR ('" . $file . "', #" . $line . ") : " . $message;
		
		Log::write($message);

        parent::__construct($message);
	}

}	

?>

