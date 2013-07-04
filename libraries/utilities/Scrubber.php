<?php

namespace utilities;

/**
 * This class handles all sanitization of user inputs
 *
 * @author     Jesse Cascio
 * @package    SwiftMVC
 * @subpackage utilities  
 * @copyright  jessesnet.com
 * @version    1.0
 * @link       
 * @todo       
 */

class Scrubber
{
	
	/**
	 * Removes everything but letters, spaces, allowed_chars array
	 * @param array - array of additionally allowed characters
	 * @return string - cleaned string
	 */	
	public static function washAlpha($string,$allowed_chars=array())
	{
		$allow = '';

		if (count($allowed_chars) > 0) {
			foreach ($allowed_chars as $key => $value) {
				$allow .= '\\'.$value;
			}
		}
		
		return (string)preg_replace('/[^a-zA-Z '.$allow.']/','',$string); 
	}
	
	/**
	 * Removes everything but letters, numbers, spaces, allowed_chars array
	 * @param array - array of additionally allowed characters
	 * @return string - cleaned string
	 */
	public static function washAlphaNumeric($string,$allowed_chars=array())
	{
		$allow = '';

		if (count($allowed_chars) > 0) {
			foreach ($allowed_chars as $key => $value) {
				$allow .= '\\'.$value;
			}
		}
		
		return (string)preg_replace('/[^a-zA-Z0-9 '.$allow.']/','',$string); 
	}
}

?>