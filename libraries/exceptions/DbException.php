<?php

namespace exceptions;
use utilities\Log;

/**
 * Handles all database related errors
 *
 * @author     Jesse Cascio
 * @package    SwiftMVC
 * @subpackage exceptions  
 * @copyright  jessesnet.com
 * @version    1.0
 * @link       
 * @todo       
 */

class DbException extends SwiftException
{
	public function __construct($file, $line, $message) 
	{
		$message = date("Y-m-d H:i:s") . " - DB ERROR ('" . $file . "', #" . $line . ") : " . $message;
		
		Log::write($message, 'sql_log.log');

        parent::__construct($message);
	}
}	

?>