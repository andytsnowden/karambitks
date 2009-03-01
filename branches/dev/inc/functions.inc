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
 * @version    SVN: $Id$
 * @link       http://code.google.com/p/karambitks/
 * @link       http://www.eve-online.com/
 */
 
/**
 *  Unregister_globals: unsets all global variables set from a superglobal array
 *  This is useful if you don't know the configuration of PHP on the server the application
 *  will be run
 * Place this in the first lines of all of your scripts
 * Don't forget that the register_global of $_SESSION is done after session_start() so after
 * 
*/ 
function unregister_globals()
{
    if (!ini_get('register_globals'))
    {
        return false;
    }

    foreach (func_get_args() as $name)
    {
        foreach ($GLOBALS[$name] as $key=>$value)
        {
            if (isset($GLOBALS[$key]))
                unset($GLOBALS[$key]);
        }
    }
}

/**
 * This fuction will look at requestor user agent to see if they are using IE // IE is know to have display issues compaired to FF/Opera/ETC
 * This can be used to make changes to the broswer code if needed for support
 * @author Andy Snowden
 * @return false/true
 */ 
function detect_ie()
{
    if (isset($_SERVER['HTTP_USER_AGENT']) && 
    (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))
        return true;
    else
        return false;
}

/**
 * Fuction returns ip address of conected user // Can be used on any server configuration
 * @author Andy Snowden
 * @var none
 * @return formatted ip address 123.123.123.123
 */
function getRealIpAddr()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
      $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
      $ip=$_SERVER['REMOTE_ADDR'];
    }
    $partip = explode(",", $ip);
    if (count($partip) > 1){
    	preg_match('/^((1?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(1?\d{1,2}|2[0-4]\d|25[0-5]){1}$/',$partip[0], $matches);
    	$ip = $matches[0];
    } else {
    	$partip[0] = str_replace(":","",$partip[0]);
    	$partip[0] = str_replace("f","",$partip[0]);
    	preg_match('/^((1?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(1?\d{1,2}|2[0-4]\d|25[0-5]){1}$/',$partip[0], $matches);
    	$ip = $matches[0];
    }
    return $ip;
}
 
/**
 * @author Andy Snowden
 * @copyright 2008
 * @var $t1 = old time
 * @var $t2 = Current Time
 * @example 
print_r(FormatTimeDiff(1, 86402));
print_r(FormatTimeDiff(1, 86402, 'hms')); // distribute into hours, minutes and seconds only
print_r(FormatTimeDiff(1, 86402, 'ds')); // distribute into days and minutes only
print_r(FormatTimeDiff(86402, 1, 'm')); // distribute into only minutes 
 */

function FormatTimeDiff($t1, $t2=null, $format='yfwdhms'){
 $t2 = $t2 === null ? time() : $t2;
 $s = abs($t2 - $t1);
 $sign = $t2 > $t1 ? 1 : -1;
 $out = array();
 $left = $s;
 $format = array_unique(str_split(preg_replace('`[^yfwdhms]`', '', strtolower($format))));
 $format_count = count($format);
 $a = array('y'=>31556926, 'f'=>2629744, 'w'=>604800, 'd'=>86400, 'h'=>3600, 'm'=>60, 's'=>1);
 $i = 0;
 foreach($a as $k=>$v){
  if(in_array($k, $format)){
   ++$i;
   if($i != $format_count){
    $out[$k] = $sign * (int)($left / $v);
    $left = $left % $v;
   }else{
    $out[$k] = $sign * ($left / $v);
   }
  }else{
   $out[$k] = 0;
  }
 }
 return $out;
} 


/**
     * removes the following charaters replacing them with spaces
     * Generates pw's'
     * various options: 1 adds in upper case consonants
	 * 2 adds in upper case vowels, 4 adds in numbers and 8 adds in special characters. 
	 * make_password(8,3); would geberate an 8 character password with upper and lower consonants and vowels. 
	 * make_password(8,5); would generate an 8 character password with upper case consonants and numbers.
     * @var string
     */
function make_password($length,$strength=0) {
  $vowels = 'aeiouy';
  $consonants = 'bdghjlmnpqrstvwxz';
  if ($strength & 1) {
    $consonants .= 'BDGHJLMNPQRSTVWXZ';
  }
  if ($strength & 2) {
    $vowels .= "AEIOUY";
  }
  if ($strength & 4) {
    $consonants .= '0123456789';
  }
  if ($strength & 8) {
    $consonants .= '@#$%^';
  }
  $password = '';
  $alt = time() % 2;
  srand(time());
  for ($i = 0; $i < $length; $i++) {
    if ($alt == 1) {
      $password .= $consonants[(rand() % strlen($consonants))];
      $alt = 0;
    } else {
        $password .= $vowels[(rand() % strlen($vowels))];
      $alt = 1;
    }
  }
  return $password;
}


/**
     * removes the following charaters
     * ~ ` ! @ # $ % ^ & * ( ) - _ + = [ ] { } \ | ; : ' " , < . > ? /
     * @var string
     */
function master_escape($tag){
	
	$tag = str_replace(".","",$tag);
	$tag = str_replace("-","",$tag);
	$tag = str_replace("[","",$tag);
	$tag = str_replace("]","",$tag);
	$tag = str_replace("!","",$tag);
	$tag = str_replace("@","",$tag);
	$tag = str_replace("#","",$tag);
	$tag = str_replace("$","",$tag);
	$tag = str_replace("%","",$tag);
	$tag = str_replace("^","",$tag);
	$tag = str_replace("&","",$tag);
	$tag = str_replace("*","",$tag);
	$tag = str_replace("(","",$tag);
	$tag = str_replace(")","",$tag);
	$tag = str_replace("`","",$tag);
	$tag = str_replace("~","",$tag);
	$tag = str_replace(",","",$tag);
	$tag = str_replace("<","",$tag);
	$tag = str_replace(">","",$tag);
	$tag = str_replace("/","",$tag);
	$tag = str_replace("?","",$tag);
	$tag = str_replace("{","",$tag);
	$tag = str_replace("}","",$tag);
	$tag = str_replace("/","",$tag);
	$tag = str_replace("|","",$tag);
	$tag = str_replace('"',"",$tag);
	$tag = str_replace("'","",$tag);
	$tag = str_replace(":","",$tag);
	$tag = str_replace(";","",$tag);
	$tag = str_replace("_","",$tag);	
	$tag = str_replace("+","",$tag);
	$tag = str_replace("=","",$tag);
	
	return $tag;
}

/**
 * Debug tool for Arrays prints the in color/expandable format
 * @uses debug_var('nametodisplay', print_r($array, TRUE));
 * @return NA prints to screen
function debug_var($name, $data)
{
    $captured = preg_split("/\r?\n/", $data);
    print "<script>function toggleDiv(num){
      var span = document.getElementById('d'+num);
      var a = document.getElementById('a'+num);
      var cur = span.style.display;
      if(cur == 'none'){
        a.innerHTML = '-';
        span.style.display = 'inline';
      }else{
        a.innerHTML = '+';
        span.style.display = 'none';
      }
    }</script>";
    print "<b>$name</b>\n";
    print "<pre>\n";
    foreach ($captured as $line) {
        print debug_colorize_string($line) . "\n";
    }
    print "</pre>\n";
}

function next_div($matches)
{
    static $num = 0;
    ++$num;
    return "$matches[1]<a id=a$num href=\"javascript: toggleDiv($num)\">+</a><span id=d$num style=\"display:none\">(";
}

/**
 * colorize a string for pretty display
 *
 * @access private
 * @param $string string info to colorize
 * @return string HTML colorized
 * @global
 */
function debug_colorize_string($string)
{
    $string = preg_replace("/\[(\w*)\]/i", '[<font color="red">$1</font>]', $string);
    $string = preg_replace_callback("/(\s+)\($/", 'next_div', $string);
    $string = preg_replace("/(\s+)\)$/", '$1)</span>', $string);
    /* turn array indexes to red */
    /* turn the word Array blue */
    $string = str_replace('Array', '<font color="blue">Array</font>', $string);
    /* turn arrows graygreen */
    $string = str_replace('=>', '<font color="#556F55">=></font>', $string);
    return $string;
}

/**
 * return a unixtime stamp from a mysql stamp
 * @var mysql_timestamp
 * @return unix time stamp
 */
function UnixTime($mysql_timestamp)
{
    if (preg_match('/(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/', $mysql_timestamp,
        $pieces) || preg_match('/(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/', $mysql_timestamp,
        $pieces)) {
        $unix_time = mktime($pieces[4], $pieces[5], $pieces[6], $pieces[2], $pieces[3],
            $pieces[1]);
    } elseif (preg_match('/\d{4}\-\d{2}\-\d{2} \d{2}:\d{2}:\d{2}/', $mysql_timestamp) ||
    preg_match('/\d{2}\-\d{2}\-\d{2} \d{2}:\d{2}:\d{2}/', $mysql_timestamp) ||
        preg_match('/\d{4}\-\d{2}\-\d{2}/', $mysql_timestamp) || preg_match('/\d{2}\-\d{2}\-\d{2}/',
        $mysql_timestamp)) {
        $unix_time = strtotime($mysql_timestamp);
    } elseif (preg_match('/(\d{4})(\d{2})(\d{2})/', $mysql_timestamp, $pieces) ||
    preg_match('/(\d{2})(\d{2})(\d{2})/', $mysql_timestamp, $pieces)) {
        $unix_time = mktime(0, 0, 0, $pieces[2], $pieces[3], $pieces[1]);
    }
    return $unix_time;
}
 ?>