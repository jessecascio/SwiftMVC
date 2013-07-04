<?php

namespace utilities;
use exceptions\LogException;

/**
 * This class is responsible for writing message to log files
 *
 * @author     Jesse Cascio
 * @package    SwiftMVC
 * @subpackage utilities  
 * @copyright  jessesnet.com
 * @version    1.0
 * @link       
 */

class Log
{
	/**
	 * Write message to a specified log file
     * @param string - message to write
     * @param string - log file name
	 */		
	public static function write($message, $log_file = "error_log.log")
	{
        $file_path = LOG_PATH . DIRECTORY_SEPARATOR . $log_file;
		
        if (($handle = @fopen($file_path, "a")) !== FALSE) {
            fwrite($handle, $message . PHP_EOL . PHP_EOL);
            fclose($handle);
        } else {
        	throw new LogException(__FILE__, __LINE__, "Unable to open file: " . $file_path);
        }
    }	
}

?>