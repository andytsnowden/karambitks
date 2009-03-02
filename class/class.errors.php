<?php

/**
 * Karambit Killboard System
 *
 * Builds and checks the path constants.
 *
 * PHP version 5
 *
 * LICENSE: This file is part of EVE MultiPurpose Application also know as KarambitKS.
 * KarambitKS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3 of the License.
 *
 * KarambitKS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with KarambitKS.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author     Michael Cummings <mgcummings@yahoo.com>
 * @author     Stephen Gulickk <stephenmg12@gmail.com>
 * @author     Andy Snowden <forumadmin@eve-razor.com>
 * @copyright  2009 (C) Michael Cummings, Stephen Gulick, and Andy Snowden 
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @package    KarambitKS
 * @version    SVN: $Id: class.killlist.php 17 2009-03-01 20:22:27Z stephenmg12 $
 * @link       http://code.google.com/p/karambitks/
 * @link       http://www.eve-online.com/
 */


/**
 * Errors
 * 
 * @package KarambitKS
 * @author Andy Snowden
 * @copyright 2009
 * @version $Id: class.errors.php 17 2009-03-01 6:15:00 forumadmin $
 * @access public
 */
class errors 
{
    /**
     * Error level
     * @var int 
     * @access public
     */
    public $errno;

    /**
     * Error message
     * @var string 
     * @access public
     */
    public $errstr;

    /**
     * Filename where error happened
     * @var string 
     * @access public
     */
    public $errfile;

    /**
     * Line number
     * @var int 
     * @access public
     */
    public $errline;

    /**
     * Symbol table at point where error occurred
     * @var array 
     * @access public
     */
    public $errcontext;

    /**
     * Array of observers // server // viewer data
     * @var array
     * @static
     * @access protected
     */
   
    public function __construct($errno, $errstr, $errfile = null, $errline = null, $errcontext = null)
	{
        $this->errno      = $errno;
        $this->errstr     = $errstr;
        $this->errfile    = $errfile;
        $this->errline    = $errline;
        $this->errcontext = $errcontext;

        $this->notify();
	}

    /**
     * Handle PHP Errors
     *
     * Creates a Cgiapp2_Error from a PHP error (if the PHP error handler has
     * been set to this method).
     *
     * @static
     * @access public
     */
    public static function handler($errno, $errstr, $errfile = null, $errline = null, $errcontext = null)
    {
        return new errors($errno, $errstr, $errfile, $errline, $errcontext);
    }

    /**
     * String representation of exception
     * 
     * @access public
     * @return void
     */
    public function __toString()
    {
        return __CLASS__ . ': [' . $this->code . ']: ' . $this->message . "\n";
    }    

    /**
     * Notify observers of an error event
     * 
     * @access public
     * @return void
     */
    final public function notify()
    {
    	//needs a good deal of work Plan to expain to identify and classify errors and handle them how we define in config  for now it will just output them to the screen.
		 switch ($this->errno){ 
			case 2:
				print 
				"ERROR! </br>
				 Error No: $this->errno </br>
				 Error String : $this->errstr </br>
				 Error File : $this->errfile </br>
				 Error Line : $this->errline \n";
			break;
		
			case 8:
				print 
				"ERROR! </br>
				 Error No: $this->errno </br>
				 Error String : $this->errstr </br>
				 Error File : $this->errfile </br>
				 Error Line : $this->errline \n";
			break;
		
			case 256:
				print 
				"ERROR! </br>
				 Error No: $this->errno </br>
				 Error String : $this->errstr </br>
				 Error File : $this->errfile </br>
				 Error Line : $this->errline \n";
			break;
			
			case 512:
				print 
				"ERROR! </br>
				 Error No: $this->errno </br>
				 Error String : $this->errstr </br>
				 Error File : $this->errfile </br>
				 Error Line : $this->errline \n";
			break;
			
			case 1024:
				print 
				"ERROR! </br>
				 Error No: $this->errno </br>
				 Error String : $this->errstr </br>
				 Error File : $this->errfile </br>
				 Error Line : $this->errline \n";
			break;
			
			case 4096:
				print 
				"ERROR! </br>
				 Error No: $this->errno </br>
				 Error String : $this->errstr </br>
				 Error File : $this->errfile </br>
				 Error Line : $this->errline \n";
			break;
		
			default :
				print 
				"ERROR! </br>
				 Error No: $this->errno </br>
				 Error String : $this->errstr </br>
				 Error File : $this->errfile </br>
				 Error Line : $this->errline \n";
		}

    }
} 

?>