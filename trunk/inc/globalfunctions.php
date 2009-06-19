<?php
/**
 * EVE MultiPurpose Application
 *
 * globalfunctions.php - Holds all functions that both EMPA and EMPA Setup uses.
 *
 * PHP version 5
 *
 * LICENSE: This file is part of EVE MultiPurpose Application also know as EMPA.
 * EMPA is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3 of the License.
 *
 * EMPA is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with EMPA.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author     Ben Cole <wengole@gmail.com>
 * @author     Claus Pedersen <satissis@gmail.com>
 * @author     Michael Cummings <dragonrun1@gmail.com>
 * @author     Stephen Gulick <stephenmg12@gmail.com>
 * @copyright  2008-2009 (C) Ben Cole, Claus Pedersen, Stephen Gulick, and Michael Cummings
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * @package    EMPA
 * @version    SVN: $Id$ (Only to be used in key files)
 * @link       http://code.google.com/p/empa/
 * @link       http://www.eve-online.com/
 */

/**
 * @internal Only let this code be included or required not ran directly.
 */
if (basename(__FILE__) == basename($_SERVER['SCRIPT_NAME'])) {
  exit();
};

/**
 * Check if Browser is EVE IGB
 * @internal Return true or false.
 */
function isIGB() {
  // Parse agent string by spliting on the '/'
  $parts = explode("/", @$_SERVER['HTTP_USER_AGENT']);
  // Test for Eve Minibrowser also test against broken Shiva IGB Agent
  if (($parts[0] == "EVE-minibrowser") or ($parts[0] == "Python-urllib")) {
    // IGB always sends this set to yes, or no,
    // so if it is missing, we smell something.
    if (!isset($_SERVER['HTTP_EVE_TRUSTED'])) {
      return false;
    };
    // return true at this point, User Agent matches,
    // and no phishy headers
    return true;
  } else {
    // User Agent, does not match required.
    return false;
  };
}

/**
 * Create <input> hidden post list
 *
 * This will create an HTML <inset type="hidden" ... /> for each $_POST name 
 * and values there is. If you specifi exceptions, then those post values will not be created.
 *
 * @param array|string $exceptions This is the post names that we don't want to be included.
 * @return string An generated HTML <inset type="hidden" ... /> list of all post
 */
function inputHiddenPost($exceptions = '') {
  $hiddenPost = '';
  foreach ($_POST as $postName => $postValue) {
    /*
     * Check for exceptions to leave out of the post list
     */
    if (is_array($exceptions) && in_array($postName, $exceptions)) {
      continue;
    } elseif ($exceptions == 'ALL_POST_CLEAN') {
      continue;
    };
    /*
     * check if the value is a array
     */
    if (is_array($postValue)) {
      $hiddenPost .= inputHiddenPostArray($postValue, $postName, $exceptions);
    } else {
      $hiddenPost .= '<input type="hidden" name="' . $postName . '" value="' . $postValue . '" />' . PHP_EOL;
    }; // if is_array($postValue)
  };// foreach $_POST ...
  return $hiddenPost;
}

/**
 * Create <input> hidden post list array
 *
 * @see inputHiddenPost
 * @internal
 */
function inputHiddenPostArray($postValueArray, $Name, $exceptions = '') {
  $hiddenPost = '';
  foreach ($postValueArray as $postName => $postValue) {
    /*
     * Check for exceptions to leave out of the post list
     */
    if (is_array($exceptions) && in_array($Name . '[' . $postName . ']', $exceptions)) {
      continue;
    } elseif ($exceptions == 'ALL_POST_CLEAN') {
      continue;
    };
    /*
     * check if the value is a array
     */
    if (is_array($postValue)) {
      $hiddenPost .= inputHiddenPostArray($postValue, $Name . '[' . $postName . ']');
    } else {
      $hiddenPost .= '<input type="hidden" name="' . $Name . '[' . $postName . ']" value="' . $postValue . '" />' . PHP_EOL;
    }; // if is_array($postValue1)
  };// foreach $postValueArray ...
  return $hiddenPost;
}

/**
 * Get API info from cURL request
 *
 * This will get an EVE Api xml result for a specifik Api.
 * It will also check if the page is loaded corectly, if there is error and
 * if it's a valid EVE Api xml file
 *
 * @param array $params This is the request parameter that EVE Api server uses
 * to get the right info.
 * @param string $request This is the request url from EVE Api server,
 * without http://api.eve-online.com
 * Example of an corp Account Balance:
 * <code>
 * $params = array(
 *   'userID'=>'The Api user id',
 *   'apiKey'=>'Api key to use',
 *   'characterID'=>'The Character id'
 * );
 * $reqResult = getApiInfo($params, '/char/AccountBalance.xml.aspx');
 * </code>
 *
 * @return string The requested xml result.
 */
function getApiInfo(array $params, $request) {
  $result = cURLRequest($params, $request);
  // Now check for errors.
  if ($result['curl_error']) {
    //echo $result['curl_error'];
    return false;
  };
  if (200 != $result['http_code']) {
    //echo 'Bad http_code';
    return false;
  };
  if (!$result['body']) {
    //echo 'No result';
    return false;
  };
  if (!strpos($result['body'], '<eveapi version="')) {
    //echo 'Not eveapi<br />'.$result['body'];
    return false;
  };
  return simplexml_load_string($result['body']);
}

/**
 * Do cURL Request
 *
 * This is almost the same as @see getApiInfo except that this one only returns the
 * raw xml file without checking for Api server is down or other page error.
 *
 * This is a subfunction to @see getApiInfo
 *
 * @param array $params This is the request parameter that EVE Api server uses
 * to get the right info.
 * @param string $request This is the request url from EVE Api server,
 * without http://api.eve-online.com
 * Example of an corp Account Balance:
 * <code>
 * $params = array(
 *   'userID'=>'The Api user id',
 *   'apiKey'=>'Api key to use',
 *   'characterID'=>'The Character id'
 * );
 * $reqResult = cURLRequest($params, '/char/AccountBalance.xml.aspx');
 * </code>
 *
 * @return string The requested page result.
 */
function cURLRequest(array $params, $request) {
  // poststring
  if (count($params) > 0) {
    $poststring = http_build_query($params, NULL, '&');
  } else {
    $poststring = "";
  };
  $ch = curl_init();
  $header = array(
    "Accept: text/xml,application/xml,application/xhtml+xml;q=0.9,text/html;q=0.8,text/plain;q=0.7,image/png;q=0.6,*/*;q=0.5",
    "Accept-Language: en-us;q=0.9,en;q=0.8,*;q=0.7",
    "Accept-Charset: utf-8;q=0.9,windows-1251;q=0.7,*;q=0.6",
    "Keep-Alive: 300"
  );
  $options = array(
    CURLOPT_URL => 'http://api.eve-online.com' . $request,
    CURLOPT_ENCODING => '',
    CURLOPT_FOLLOWLOCATION => TRUE,
    CURLOPT_HEADER => TRUE,
    CURLOPT_MAXREDIRS => 5,
    CURLOPT_RETURNTRANSFER => TRUE,
    CURLOPT_SSL_VERIFYHOST => FALSE,
    CURLOPT_SSL_VERIFYPEER => FALSE,
    CURLOPT_VERBOSE => FALSE,
    CURLOPT_USERAGENT => 'PHPApi',
    CURLOPT_HTTPHEADER => $header,
    CURLOPT_POST => TRUE,
    CURLOPT_POSTFIELDS => $poststring
  );
  curl_setopt_array($ch, $options);
  /*
   * Execute cURL query and get result
   */
  $response = curl_exec($ch);
  $error = curl_error($ch);
  $result = array(
    'header' => '',
    'body' => '',
    'curl_error' => '',
    'http_code' => '',
    'last_url' => ''
  );
  if ($error != "") {
    $result['curl_error'] = $error;
    return $result;
  };
  $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
  $result['header'] = substr($response, 0, $header_size);
  $result['body'] = substr($response, $header_size);
  $result['http_code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $result['last_url'] = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
  curl_close($ch);
  return $result;
}

/**
 * Function to build a multi-values insert ... on duplicate key update query
 * Example of how to use:
 * <code>
 * $data=array(
 *         array(
 *           'tableName'=>'eve-api-pull',
 *           'ownerID'=>0,
 *           'cachedUntil'=>'2008-01-01 00:00:01'
 *         )
 *       );
 * $types=array(
 *          'tableName'=>'C',
 *          'ownerID'=>'I',
 *          'cachedUntil'=>'T'
 *        );
 * $sql = BuildInsertUpdateQuery($data,$types,'CacheUntil');
 * </code>
 *
 *
 * @param array $data Values to be put into query
 * @param array $types Keys are parameter names and values their types
 * @param string $table Table to use in query's from clause
 *
 * @return string Returns a complete SQL statement ready to be used by a
 * ADOdb exec
 *
 * @throws ADODB_Exception if connection used to do quoting fails.
 */
function BuildInsertUpdateQuery(array $data, array $types, $table) {
  global $con;
  if (empty($data) || empty($types)) {
    return FALSE;
  };
  $pkeys = array_keys($types);
  $dkeys = array_keys($data[0]);
  $fields = array_intersect($pkeys, $dkeys);
  // Need this so we can do quoting.
  $needsQuote = array('C', 'X', 'D', 'T');
  // Build query sections
  $insert = 'INSERT INTO `' . $table . '` (`';
  $insert .= implode('`,`', $pkeys) . '`)';
  $values = ' VALUES';
  $sets = array();
  foreach ($data as $row) {
    $set = array();
    foreach ($fields as $field) {
      if (in_array($types[$field], $needsQuote)) {
        $set[] = $con->qstr($row[$field]);
      } else {
        $set[] = $row[$field];
      };// else in_array $params...
    };// foreach $fields ...
    $sets[] = '(' . implode(',', $set) . ')';
  };
  $values .= ' ' . implode(',', $sets);
  $dupup = ' ON DUPLICATE KEY UPDATE ';
  // Loop thru and build update section.
  $updates = array();
  foreach ($types as $k => $v) {
    $updates[] = '`' . $k . '`=VALUES(`' . $k . '`)';
  };
  $dupup .= implode(',', $updates);
  return $insert . $values . $dupup;
}// function BuildInsertUpdateQuery

/**
 * Add Output to dwoo template for progressbar in mod install, mod update and EMPA Setup.
 *
 * @param array $output The output array to put in the template
 * @internal This should only be used on the core system!
 */
function OutPut($output) {
  // get global values
  global $dwoo;
  // Load a template file (name it as you please), this is reusable
  if (defined('EMPA_VERSION')) {
    $temptpl = new Dwoo_Template_File(TEMPLATE_DIR . 'update_output.tpl');
  } else {
    $temptpl = new Dwoo_Template_File(SETUP_TEMPLATE_DIR . 'dodb_output.tpl');
  }; // if defined('EMPA_VERSION')
  // Create Dwoo data set
  $tempdata = new Dwoo_Data();
  // Assign data to template
  $tempdata->assign('output', $output);
  // Output dwoo result
  $dwoo->output($temptpl, $tempdata);
  unset($tempdata, $tpl);
  @ob_flush();
  flush();
}

?>
