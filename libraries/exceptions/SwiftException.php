<?php

namespace exceptions;
use \Exception;

/**
 * Parent exception class, all exceptions should inherit from, each child is responsible for
 * its own loggin.  Logging should only occur in exceptions.  If you need to log something but
 * not stop application flow simply throw an error and catch it
 *
 * @author     Jesse Cascio
 * @package    SwiftMVC
 * @subpackage exceptions  
 * @copyright  jessesnet.com
 * @version    1.0
 * @link       
 * @todo       
 */

abstract class SwiftException extends Exception
{
	public function __construct($message) 
	{
		parent::__construct($message);
	}
}	

?>
