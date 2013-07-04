<?php
 
namespace utilities;

/**
 * This class formats a request uri
 *
 * @author     Jesse Cascio
 * @package    SwiftMVC
 * @subpackage utilities  
 * @copyright  jessesnet.com
 * @version    1.0
 * @link       
 * @todo       
 */

class Uri
{
    /**
     * Formats the request uri
     * @param string - $_SERVER['REQUEST_URI']
     * @param string - web root folder name i.e. 'public'
     * @param bool - whether this is development environment
     * @return string - request portion without leading trailing / or ? i.e. index/about
     */ 
    public static function getRequestPath($request_uri, $webroot, $dev)
    {
        // have to modify the uri in the dev environment
        if ($dev) {
            $request_uri = substr($request_uri,strpos($request_uri, $webroot) + strlen($webroot));
        } else {
            $request_uri = $request_uri;
        }

        return self::_cleanUri($request_uri);
    }

    /**
     * Removes unwanted characters from the uri
     * @param string - request uri
     * @return string - cleaned request uri
     */
    private static function _cleanUri($request_uri)
    {
        //removes everything after, and including, the ?
        $clean_uri = strpos($request_uri,'?') > 0 ? substr($request_uri,0,strpos($request_uri,'?')) : $request_uri;

        //remove any unwanted characters
        $clean_uri = Scrubber::washAlphaNumeric($clean_uri,array('-','/','.'));

        $paths = explode('/',$clean_uri);
        $count = 0;
        $uri_path = '';
        
        // rebuild path removing extra / and ignoring .
        if (is_array($paths)) {
            foreach ($paths as $path) {
                if (trim($path) && strpos($path,'.') === FALSE) {
                    $uri_path .= $path."/";
                }
            }
        }           

        //remove the final trailing /
        return trim($uri_path) ? substr($uri_path,0,strlen($uri_path)-1) : $uri_path;
     }
}

?>