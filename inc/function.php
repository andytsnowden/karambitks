<?php
/**
 * EVE MultiPurpose Application
 *
 * function.php - Main functions for EMPA.
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
 * @internal Load functions that is both used by EMPA and EMPA Setup
 */
require_once(EMPA_INC.'globalfunctions.php');

/**
 * Output site header.
 *
 * More description will come when this function have been rewrited
 */
function includeHeader($title = NULL, $siteDisabled = false) {
  /*
   * get global values
   */
  global $con, $ds, $prefix, $config, $corpinfo, $dwoo, $metainfo, $links, $scriptlinks, $scripts, $user, $logierror;
  /*
   * Load a template file (name it as you please), this is reusable
   */
  $tpl = new Dwoo_Template_File(TEMPLATE_DIR . 'header.tpl');
  /*
   * Create Dwoo data set
   */
  $data = new Dwoo_Data();
  /*
   * Get server status
   */
  try {
    $query = "SELECT * FROM `" . PREFIX_YAPEAL . "serverServerStatus`";
    $rs = $con->Execute($query);
    $serverinfo = $rs->FetchRow();
    $rs->close();
  }
  catch(ADODB_Exception $e) {
    die($e->getMessage());
  }
  $links[] = 'rel="icon" type="image/png" href="http://www.evecorplogo.net/logo.php?id=' . $config['corporationID'] . '"';
  /*
   * Assign meta, link and script to template
   */
  $data->assign('metatag', $metainfo);
  $data->assign('links', $links);
	$data->assign('urlbase', URLBASE);
	$data->assign('scriptlinks', $scriptlinks);
  $data->assign('scripts', $scripts);
  /*
   * Assign Corp Info to template
   */
  $data->assign('corporationID', $config['corporationID']);
  if ($title !== NULL) {
    $data->assign('pagetitle', ' - ' . $dwoo->transString($title));
  } else {
    $data->assign('pagetitle', '');
  }; // if $title !== NULL
  $data->assign('corporationName', $corpinfo['corporationName']);
  $data->assign('memberCount', $corpinfo['memberCount']);
  $data->assign('ticker', $corpinfo['ticker']);
  /*
   * Assign Server Info to template
   */
  $data->assign('onlinePlayers', $serverinfo['onlinePlayers']);
  $data->assign('serverName', $serverinfo['serverName']);
  $data->assign('serverOpen', $serverinfo['serverOpen']);
  /*
   * Assign User Info to template
   */
  $userwarning = '';
  if ($user['UserId'] > 0 && inCorp() && $user['ApiStatus'] == 1) {
    $userwarning = $dwoo->transString('Your Api Key do not work. Your Character info will not be updated');
  } elseif ($user['UserId'] > 0 && inCorp() && $user['ApiStatus'] == 2) {
    $userwarning = $dwoo->transString('Your Character info will not be updated (Account CCP Payment missing)');
  } elseif ($user['UserId'] > 0 && inCorp() && $user['hasFullApiKey'] == 0) {
    $userwarning = $dwoo->transString('Your Api Key is not Full Api Key (Some data will not update)');
  }
  $data->assign('user', $user);
  $data->assign('userwarning', $userwarning);
  $data->assign('logierror', $logierror);
  if (isset($_REQUEST['login'])) {
    $data->assign('username', $_REQUEST['username']);
  } else {
    $data->assign('username', '');
  }; // if isset($_REQUEST['login'])
  $data->assign('loginurl', URLBASE . SCRIPT_NAME . urlRequests());
  $data->assign('registerurl', URLBASE . 'register.php/info');
  $exceptions = array('username', 'userpass', 'login', 'logout');
  $data->assign('logininputHiddenPost', inputHiddenPost($exceptions));
  /*
   * Handle site if disabled.
   */
  $data->assign('siteDisabled', $siteDisabled);
  if (!$siteDisabled) {
    /*
     * Assign Navbar Info to template
     */
    // Check what navbars to show
    if ($user['UserId'] > 0 && inCorp() && ($user['IsAdmin'] == 1 || $user['IsSuperAdmin'] == 1)) {
      $toNavList = '4';
    } elseif ($user['UserId'] > 0 && inCorp()) {
      $toNavList = '3';
    } elseif ($user['UserId'] > 0) {
      $toNavList = '2';
    } else {
      $toNavList = '1';
    };
    // Define navbar array to template
    $navbar = array();
    $data->assign('navbars', $navbar);
    // Get navbar list
    $query = "SELECT * FROM `" . PREFIX_EMPA . "NavGroups` WHERE `GrpId` <= '" . $toNavList . "' ORDER BY `weight` ASC";
    $rs = $con->Execute($query);
    // Build navbar
    while ($navgroups = $rs->FetchRow()) {
      $content = '';
      // Get navbar content list
      $query = "SELECT
                  N.`Mod`,
                  M.`modAdminName`,
                  M.`modName`,
                  M.`isSystem`
                FROM `" . PREFIX_EMPA . "NavList` N
                  LEFT JOIN `" . PREFIX_EMPA . "Mods` M ON N.`Mod` = M.`mod`
                WHERE N.`GrpId` = '" . $navgroups['GrpId'] . "' AND M.`isActive` = 1 ORDER BY N.`weight` ASC";
      // Add a predefined link in the navbar depending on navbar groupe
      if ($navgroups['GrpId'] == 1) {
        $content .= '<img src="' . TEMPLATE_IMG_DIR . '16x16/navMark.png" alt="" width="16" height="16" /> ' . '<a href="' . URLBASE . 'index.php" title="' . $corpinfo['corporationName'] . ' ' . $dwoo->transString('Home') . '">' . $dwoo->transString('Home') . '</a><br />' . PHP_EOL;
      }
      if ($navgroups['GrpId'] == 2) {
        $content.= '<img src="' . TEMPLATE_IMG_DIR . '16x16/navMark.png" alt="" width="16" height="16" /> ' . '<a href="' . URLBASE . 'user.php" title="' . $dwoo->transString('My Account') . '">' . $dwoo->transString('My Account') . '</a><br />' . PHP_EOL;
      }
      if ($navgroups['GrpId'] == 4) {
        $content.= '<img src="' . TEMPLATE_IMG_DIR . '16x16/navAdmin.png" alt="" width="16" height="16" /> ' . '<a href="' . URLBASE . 'admin.php" title="' . $corpinfo['corporationName'] . ' ' . $dwoo->transString('Admin Panel') . '">' . $dwoo->transString('Admin Panel') . '</a><br />' . PHP_EOL;
      }
      $rs2 = $con->Execute($query);
      // Build navbar content
      while ($navlist = $rs2->FetchRow()) {
        if ($navgroups['GrpId'] == 4) {
          $content.= '<img src="' . TEMPLATE_IMG_DIR . '16x16/navAdmin.png" alt="" width="16" height="16" /> ' . '<a href="' . URLBASE . 'admin.php?a=' . $navlist['Mod'] . '" title="' . $dwoo->transString('Edit') . ' ' . $dwoo->transString($navlist['modAdminName']) . '">' . $dwoo->transString('Edit') . ' ' . $dwoo->transString($navlist['modAdminName']) . '</a><br />' . PHP_EOL;
        } elseif ($navgroups['GrpId'] == 3) {
          $content.= '<img src="' . TEMPLATE_IMG_DIR . '16x16/navMark.png" alt="" width="16" height="16" /> ' . '<a href="' . URLBASE . 'corp.php?m=' . $navlist['Mod'] . '" title="' . $dwoo->transString($navlist['modName']) . '">' . $dwoo->transString($navlist['modName']) . '</a><br />' . PHP_EOL;
        } elseif ($navgroups['GrpId'] == 2) {
          $content.= '<img src="' . TEMPLATE_IMG_DIR . '16x16/navMark.png" alt="" width="16" height="16" /> ' . '<a href="' . URLBASE . 'user.php?m=' . $navlist['Mod'] . '" title="' . $dwoo->transString($navlist['modName']) . '">' . $dwoo->transString($navlist['modName']) . '</a><br />' . PHP_EOL;
        } else {
          $content.= '<img src="' . TEMPLATE_IMG_DIR . '16x16/navMark.png" alt="" width="16" height="16" /> ' . '<a href="' . URLBASE . 'index.php?m=' . $navlist['Mod'] . '" title="' . $navlist['modName'] . '">' . $navlist['modName'] . '</a><br />' . PHP_EOL;
        }
      }
      $rs2->close();
      // Output navbar if there is some content in it.
      if ($content != '') {
        $navbar = array('title' => $navgroups['Name'], 'content' => $content);
        $data->append('navbars', $navbar);
      }; // if (count($content)>0)
      unset($navgroups, $content);
    }
    $rs->close();
  }; // if !$siteDisabled
  /*
   * Output dwoo result
   */
  $dwoo->output($tpl, $data);
  unset($data, $tpl);
}

/**
 * Output site footer.
 *
 * More description will come when this function have been rewrited
 */
function includeFooter() {
  /*
   * get global values
   */
  global $dwoo, $siteStartTimer, $config;
  /*
   * Load a template file (name it as you please), this is reusable
   */
  $tpl = new Dwoo_Template_File(TEMPLATE_DIR . 'footer.tpl');
  /*
   * Create Dwoo data set
   */
  $data = new Dwoo_Data();
  /*
   * Assign some data to template
   */
  $data->assign('urlbase', URLBASE);
	$data->assign('empaversion', $config['version']);
  $siteStopTimer = microtime(true);
  $siteLoadTime = $siteStopTimer - $siteStartTimer;
  $data->assign('siteLoadTime', number_format($siteLoadTime, 3));
  /*
   * Output dwoo result
   */
  $dwoo->output($tpl, $data);
  unset($tpl, $data);
}

/**
 * Output Admin Panel.
 *
 * This will output the admin panel. just remember to use
 * the includeHeader function first
 */
function includeAdmin() {
  // get global values
  global $dwoo, $con, $ds, $prefix, $templateDir;
  // set values
  $linksperline = 5;
  /*
  * Main Admin Panel.
  */
  // Load template file
  $tpl = new Dwoo_Template_File(TEMPLATE_DIR . 'admin.tpl');
  // Create Dwoo data set
  $data = new Dwoo_Data();
  // Admin page links for system stuff
  $admininfo = array();
  $admininfo[] = array(
    'url' => $_SERVER['SCRIPT_NAME'] . '/syssetup',
    'img' => TEMPLATE_IMG_DIR . '64x64/system.png',
    'text' => 'System Setup'
  );
  $admininfo[] = array(
    'url' => $_SERVER['SCRIPT_NAME'] . '/modsetup',
    'img' => TEMPLATE_IMG_DIR . '64x64/mods.png',
    'text' => 'Mod Setup'
  );
  $admininfo[] = array(
    'url' => $_SERVER['SCRIPT_NAME'] . '/users',
    'img' => TEMPLATE_IMG_DIR . '64x64/user.png',
    'text' => 'Users'
  );
  $query = "SELECT * FROM `" . PREFIX_EMPA . "Mods` WHERE `isSystem` = 1";
  $rs = $con->Execute($query);
  if ($rs) {
    while ($info = $rs->FetchRow()) {
      if (file_exists(EMPA_MODS . $info['mod'] . $ds . 'admin' . $ds . 'main.php')) {
        if (isset($info['modAdminIcon']) && $info['modAdminIcon'] != '' && file_exists(EMPA_MODS . $info['mod'] . $ds . 'tpl' . $ds . $templateDir . $ds . 'img' . $ds . '64x64' . $ds . $info['modAdminIcon'])) {
          $img = 'mods/' . $info['mod'] . '/tpl/' . $templateDir . '/img/64x64/' . $info['modAdminIcon'];
        } else {
          $img = TEMPLATE_IMG_DIR . '64x64' . '/' . 'what.png';
        }; // if (isset($info['adminIcon']) && file_exists($info['adminIcon']))
        $admininfo[] = array(
          'url' => $_SERVER['SCRIPT_NAME'] . '/' . $info['mod'],
          'img' => $img,
          'text' => $info['modAdminName']
        );
      }; // if (file_exists(EMPA_MODS.$info['mod'].$ds.'admin'.$ds.'main.php'))
    }; // while $info
  }; // if ($rs)
  $rs->close();
  // Generate Admin Panel Layout
  $adminlines = array();
  for ($i = 0;$i < count($admininfo);) {
    $info = array();
    for ($f = 0;$f < $linksperline;$f++, $i++) {
      if (isset($admininfo[$i]) && is_array($admininfo[$i])) {
        $info[] = $admininfo[$i];
      } else {
        $info[] = array('url' => '', 'img' => '', 'text' => '');
      };
    };
    $adminlines[] = $info;
    unset($info);
  };
  unset($admininfo);
  // Assign admin panel links to template
  $data->assign('linksperline', ceil(100 / $linksperline));
  $data->assign('adminlines', $adminlines);
  // Output dwoo result
  OpenTable('Admin Menu');
  $dwoo->output($tpl, $data);
  unset($tpl, $data, $adminlines);
  CloseTable();
  /*
   * Mod Admin Panel.
   */
  // Load template file
  $tpl = new Dwoo_Template_File(TEMPLATE_DIR . 'admin.tpl');
  // Create Dwoo data set
  $data = new Dwoo_Data();
  // Admin page links for system stuff
  $admininfo = array();
  $query = "SELECT * FROM `" . PREFIX_EMPA . "Mods` WHERE `isSystem` = 0"; //  AND `isActive` = 1
  $rs = $con->Execute($query);
  if ($rs) {
    while ($info = $rs->FetchRow()) {
      if (file_exists(EMPA_MODS . $info['mod'] . $ds . 'admin' . $ds . 'main.php')) {
        if (isset($info['modAdminIcon']) && $info['modAdminIcon'] != '' && file_exists(EMPA_MODS . $info['mod'] . $ds . 'tpl' . $ds . $templateDir . $ds . 'img' . $ds . '64x64' . $ds . $info['modAdminIcon'])) {
          $img = URLBASE . 'mods/' . $info['mod'] . '/tpl/' . $templateDir . '/img/64x64/' . $info['modAdminIcon'];
        } else {
          $img = TEMPLATE_IMG_DIR . '64x64' . '/' . 'what.png';
        };// if (isset($info['adminIcon']) && file_exists($info['adminIcon']))
        $admininfo[] = array(
          'url' => $_SERVER['SCRIPT_NAME'] . '/' . $info['mod'],
          'img' => $img,
          'text' => $info['modAdminName']
        );
      }; // if (file_exists(EMPA_MODS.$info['mod'].$ds.'admin'.$ds.'main.php'))
    }; // while $info
  }; // if ($rs)
  $rs->close();
  // Generate Admin Panel Layout
  $adminlines = array();
  for ($i = 0;$i < count($admininfo);) {
    $info = array();
    for ($f = 0;$f < $linksperline;$f++, $i++) {
      if (isset($admininfo[$i]) && is_array($admininfo[$i])) {
        $info[] = $admininfo[$i];
      } else {
        $info[] = array('url' => '', 'img' => '', 'text' => '');
      };
    };
    $adminlines[] = $info;
    unset($info);
  };
  unset($admininfo);
  if (!empty($adminlines)) {
    // Assign admin panel links to template
    $data->assign('linksperline', ceil(100 / $linksperline));
    $data->assign('adminlines', $adminlines);
    // Output dwoo result
    OpenTable('Mod Admin Menu');
    $dwoo->output($tpl, $data);
    unset($tpl, $data, $adminlines);
    CloseTable();
  }; // if (!empty($adminlines))
}

/**
 * Open a table.
 *
 * This will open a table with a headline.
 *
 * @param string $headline The table headline. Can be blank so no headline is showen
 * @param string $align Set the table content align.
 * <pre>
 * left   = The content will be aligned to left.
 * center = The content will be aligned to center.
 * right  = The content will be aligned to right.
 * </pre>
 */
function OpenTable($headline = '', $align = 'left') {
  /*
   * get global values
   */
  global $dwoo;
  /*
   * Load a template file (name it as you please), this is reusable
   */
  $tpl = new Dwoo_Template_File(TEMPLATE_DIR . 'opentable.tpl');
  /*
   * Create Dwoo data set
   */
  $data = new Dwoo_Data();
  /*
   * Assign data to template
   */
  $data->assign('headline', $dwoo->transString($headline));
  $data->assign('align', $align);
  /*
   * Output dwoo result
   */
  $dwoo->output($tpl, $data);
  unset($data, $tpl);
}

/**
 * Close a table.
 *
 * This will close the table that you have created with OpenTable function
 */
function CloseTable() {
  /*
   * get global values
   */
  global $dwoo;
  /*
   * Load a template file (name it as you please), this is reusable
   */
  $tpl = new Dwoo_Template_File(TEMPLATE_DIR . 'closetable.tpl');
  /*
   * Output dwoo result
   */
  $dwoo->output($tpl);
  unset($tpl);
}

/**
 * Tell the user that they are not admins.
 *
 * This will output a full page with a warning that the user is not admin.
 */
function blockUserFromAdmin() {
  includeHeader('Not Admin');
  /*
   * get global values
   */
  global $dwoo;
  /*
   * Load a template file (name it as you please), this is reusable
   */
  $tpl = new Dwoo_Template_File(TEMPLATE_DIR . 'notadmin.tpl');
  /*
   * Output dwoo result
   */
  $dwoo->output($tpl);
  unset($tpl);
  includeFooter();
}

/**
 * Tell the user that the requestes admin page was not found.
 *
 * This will output a full page with a warning that the page was not found.
 */
function adminPageNotFound() {
  includeHeader('Page Not Found');
  /*
   * get global values
   */
  global $dwoo;
  /*
   * Load a template file (name it as you please), this is reusable
   */
  $tpl = new Dwoo_Template_File(TEMPLATE_DIR . 'noadminpage.tpl');
  /*
   * Output dwoo result
   */
  $dwoo->output($tpl);
  unset($tpl);
  includeFooter();
}

/**
 * Tell the user that the requestes admin page was not found.
 *
 * This will output a full page with a warning that the page was not found.
 */
function pageNotFound() {
  includeHeader('Page Not Found');
  /*
   * get global values
   */
  global $dwoo;
  /*
   * Load a template file (name it as you please), this is reusable
   */
  $tpl = new Dwoo_Template_File(TEMPLATE_DIR . 'pagenotfound.tpl');
  /*
   * Output dwoo result
   */
  $dwoo->output($tpl);
  unset($tpl);
  includeFooter();
}

/**
 * Create urlRequests.
 *
 * @internal This in only used by login.php, so don't use it!
 */
function urlRequests() {
  if (isset($_SERVER['PATH_INFO'])) {
    return $_SERVER['PATH_INFO'];
  };// if count $_GET ...
  return '';
}

/**
 * Generate a hash.
 *
 * This will generate a hash that can de used with passwords and cookies or anything else
 *
 * @param string $plainText This is the value to convert into an hash.
 * @param string $salt This is used if you need to return a fixed hash to be used in a password or somthing like that.
 * @return string|array This will return a generated hash string.
 * If there is no $salt specified, it will return an array where the 2 values is:
 * <pre>
 * [0] = The generated hash string.
 * [1] = The salt that was auto generated.
 *       This you can use in the $salt param if you need to check if the converted $plainText hash is the same as before.
 * </pre>
 */
function genHash($plainText, $salt = NULL) {
  if ($salt === NULL) {
    $salt = substr(md5(uniqid(rand() , true)) , 0, 8);
    return array(
      substr(sha1(EMPA_SALT . $plainText . $salt) , 4, 32) ,
      $salt
    );
  } else {
    return substr(sha1(EMPA_SALT . $plainText . $salt) , 4, 32);
  };// if ($salt === NULL)
};

/**
 * Check if user is in corp
 *
 * @param integer $charId This is the character id to check. If not set, it will use the loged in user info.
 * @param integer $corpId This is the character corporation id to check. If not set, it will use the loged in user info.
 * @return bool True if in corp and false if not.
 */
function inCorp($charId = NULL, $corpId = NULL) {
  global $con, $prefix, $user, $config;

  // Set search Criteria
  if ($charId == NULL) {
    $characterID = $user['characterID'];
  } else {
    $characterID = $charId;
  }
  if ($corpId == NULL) {
    $corporationID = $user['corporationID'];
  } else {
    $corporationID = $corpId;
  }

  // Get member list from corpMemberTracking
  $query = "SELECT `characterID` FROM `" . PREFIX_YAPEAL . "corpMemberTracking` WHERE `ownerID` = '" . $config['corporationID'] . "'";
  $rs = $con->Execute($query);
  $members = array();
  if ($rs) {
    while ($arr = $rs->FetchRow()) {
      $members[] = $arr['characterID'];
    }
  };
  $rs->close();

  // Check if user is in corp
  if (in_array($characterID, $members)) {
    return true;
  };

  // If not in corp then they might be new or just removed. So we check again by using a corp id.
  if ($corporationID === $config['corporationID']) {
    return true;
  };

  // If that didn't find it as well, then we asume the user is not in the corp.
  return false;
}

/**
 * Check role(s)
 *
 * Check if a charid have a specifik role.
 *
 * @param string $rolename This is the roll name to check for. You can find the roll name in class/EveApiRolls.php
 * @param integer $charId This is the character id to check. If not set, it will use the loged in user info.
 * @return bool True if the character have that roll or false if not.
 */
function haveRolls($rolename, $charId = NULL) {
  global $con, $prefix, $user, $config;
  /*
   * Set search Criteria
   */
  if ($charId == NULL) {
    $characterID = $user['characterID'];
  } else {
    $characterID = $charId;
  };
  /*
   * Get Rolls
   */
  $query = "SELECT `roles` FROM `" . PREFIX_YAPEAL . "corpMemberTracking` WHERE `ownerID` = '" . $config['corporationID'] . "' AND `characterID` = '" . $characterID . "'";
  $rs = $con->GetOne($query);
  if ($rs) {
    $memRoles = new EveApiRoles($rs);
    if (is_array($rolename)) {
      if ($memRoles->hasRoles($rolename)) {
        unset($memRoles);
        return true;
      }; // if ($memRoles->hasRoles($rolename))
    } else {
      if ($memRoles->hasRole($rolename)) {
        unset($memRoles);
        return true;
      }; // if ($memRoles->hasRole($rolename))
    }; // if (is_array($rolename))
    unset($memRoles);
  };
  /*
   * If no Roles
   */
  return false;
}

/**
 * Check if api info is working for the user.
 *
 * This is used to check if the api info is corect.
 *
 * @param array [$params] This need userID and apiKey to work.
 * an example:
 * <code>
 * $params = array(
 *             'userID'=>'Api user id',
 *             'apiKey'=>'Api key to check'
 *           );
 * </code>
 *
 * @return bool|integer This will return false if there is no problems or a number if there is a authentication problem.
 * <pre>
 * false = No problems
 *     1 = API authentication failure.
 *     2 = Login denied by account status witch is caused if the user account is disabled due to missing payment
 * </pre>
 */
function checkApiAuth(array $params) {
  $xml = getApiInfo($params, '/account/Characters.xml.aspx');
  // Check if $xml is false and output server is down if it is.
  if ($xml) {
    // Handle xml data
    if (isset($xml->error)) {
      if ((int)$xml->error['code'] == 201 ||
        (int)$xml->error['code'] == 202 ||
        (int)$xml->error['code'] == 203 ||
        (int)$xml->error['code'] == 204 ||
        (int)$xml->error['code'] == 205 ||
        (int)$xml->error['code'] == 210 ||
        (int)$xml->error['code'] == 212) {
        return 1;
      } elseif ((int)$xml->error['code'] == 211) {
        return 2;
      } else {
        return false;
      }; // if (int)$xml->error['code'] = XXX
    } else {
      return false;
    }; // if (isset($xml->error))
 } else {
    return false;
  }; // if ($xml)
}

/**
 * Check if api key is full api key
 *
 * @param array [$params] This need userID, apiKey and characterID to work.
 * an example:
 * <code>
 * $params = array(
 *             'userID'=>'Api user id',
 *             'apiKey'=>'Api key to check',
 *             'characterID'=>'The character id that is used'
 *           );
 * </code>
 *
 * @return bool This will return true if it has full api key or false if not.
 */
function hasFullApiKey(array $params) {
  $xml = getApiInfo($params, '/char/AccountBalance.xml.aspx');
  // Check if $xml is false and output server is down if it is.
  if ($xml) {
    // Handle xml data
    if (isset($xml->error)) {
      if ((int)$xml->error['code'] == 200) {
        return false;
      } else {
        return true;
      };// if (int)$xml->error['code'] = XXX
    } else {
      return true;
    }; // if (isset($xml->error))
  } else {
    return true;
  }; // if ($xml)
}

/**
 *  Unregister_globals: unsets all global variables set from a superglobal array
 *  This is useful if you don't know the configuration of PHP on the server the application
 *  will be run
 * Place this in the first lines of all of your scripts
 * Don't forget that the register_global of $_SESSION is done after session_start() so after
 */
function unregister_globals() {
  if (!ini_get('register_globals')) {
    return false;
  }; // if (!ini_get('register_globals'))
  foreach (func_get_args() as $name) {
    foreach ($GLOBALS[$name] as $key => $value) {
      if (isset($GLOBALS[$key])) {
        unset($GLOBALS[$key]);
      }; // if (isset($GLOBALS[$key]))
    }
  }
}

/**
 * Function to handle file and dir delete
 *
 * This function delete all files and directorys in the specified directory. It will also
 * delete the specified directory so if you don't want that to happen, use the clearDir function.
 * If using a file as the target, it will only delete that file.
 *
 * @param string [$_target] This is the target directory + (all sub directory and files) or file that need to be deleted.
 * @return bool This will return true on success and false on failure.
 */
function removeRessource($_target) {
  // If file?
  if (is_file($_target)) {
    if (is_writable($_target)) {
      if (@unlink($_target)) {
        return true;
      };
    };
    return false;
  };
  // If dir?
  if (is_dir($_target)) {
    if (is_writable($_target)) {
      foreach(new DirectoryIterator($_target) as $_res) {
        if ($_res->isDot()) {
          unset($_res);
          continue;
        };
        if ($_res->isFile()) {
          removeRessource($_res->getPathName());
        } elseif ($_res->isDir()) {
          removeRessource($_res->getRealPath());
        };
        unset($_res);
      };
      if (@rmdir($_target)) {
        return true;
      };
    };
    return false;
  };
}

/**
 * Function to handle file and dir delete
 *
 * This function delete all files and directorys in the specified directory.
 * It will keep the target dir.
 *
 * @param string [$_target] This is the target directory of all sub directory and files that need to be deleted.
 * @return bool This will return true on success and false on failure.
 */
function clearDir($_target) {
  // If dir?
  if (is_dir($_target)) {
    if (is_writable($_target)) {
      foreach(new DirectoryIterator($_target) as $_res) {
        if ($_res->isDot()) {
          unset($_res);
          continue;
        };
        if ($_res->isFile()) {
          removeRessource($_res->getPathName());
        } elseif ($_res->isDir()) {
          removeRessource($_res->getRealPath());
        };
        unset($_res);
      };
      return true;
    };
    return false;
  };
  return false;
}

/**
 * Check if table exists in database
 *
 * This function checks if a given table is allready created.
 *
 * @param string [$tablename] This is the table name to search fore.
 * @return bool This will return true if exists and false if not.
 */
function tableExists($tablename) {
  global $con;
  // Check If Table Exists
  $query = "SELECT count(*) FROM information_schema.tables
            WHERE table_schema = '" . EMPA_DB . "'
            AND table_name = '" . $tablename . "'";
  $rs = $con->GetOne($query);
  if ($rs > 0) {
    return true;
  } else {
    return false;
  };
}

/**
 * Update Per Api setup.
 *
 * Updates all Per Api so Yapeal get the corect api list to pull from each character.
 *
 * @return bool This will return true on success and false if failed.
 */
function updateAllPerApi() {
  // Get global values
  global $con, $prefix, $ds;
  // Get base per api
  require (EMPA_INSTALL . 'inc' . $ds . 'values.php');
  // Get base per api
  $query = "SELECT `charApi`, `corpApi` FROM `" . PREFIX_EMPA . "Mods`";
  $rs = $con->Execute($query);
  // Get mods per api
  if ($rs) {
    $apilist = array();
    while ($mod = $rs->FetchRow()) {
      $apilist[2][] = explode(' ', $mod['charApi']);
      $apilist[3][] = explode(' ', $mod['corpApi']);
    };
    $rs->close();
  };// if $rs
  // Fix value for $apilist[2] if no result
  if (empty($apilist[2]) || !is_array($apilist[2])) {
    $apilist[2] = array();
  }; // if (empty($apilist[2]) || !is_array($apilist[2]))
  // Fix value for $apilist[3] if no result
  if (empty($apilist[3]) || !is_array($apilist[3])) {
    $apilist[3] = array();
  }; // if (empty($apilist[3]) || !is_array($apilist[3]))

  // loop thou each array to check if there is some api missing.
  $dbdata = array();
  foreach ($accessPerApi as $level => $apiarray) {
    if ($level < 2) {
      sort($apiarray);
      $dbdata[] = array(
        'Id' => $level,
        'ApiList' => trim(implode(' ', $apiarray))
      );
      continue;
    }; // if ($level < 2)
    foreach ($apilist[$level] as $apis) {
      foreach ($apis as $api) {
        if (!in_array($api, $apiarray)) {
          $apiarray[] = $api;
        };// if in_array
      };// foreach
    };// foreach
    sort($apiarray);
    $dbdata[] = array(
      'Id' => $level,
      'ApiList' => trim(implode(' ', $apiarray))
    );
  };// foreach
  $types = array('Id' => 'I', 'ApiList' => 'C');
  $query = BuildInsertUpdateQuery($dbdata, $types, PREFIX_EMPA . 'AccessPerApi');
  unset($dbdata);
  // Update accessApi list
  try {
    $con->Execute($query);
    $con->Execute('TRUNCATE TABLE `'.PREFIX_EMPA.'Backend`');
    return true;
  }
  catch (ADODB_Exception $e) {
    return false;
  }
}

/**
 * Remove mod from navbar.
 *
 * This function vill remove all navbar links for a mod. This function is also
 * only used in the core system.
 *
 * @param string [$mod] This is the mod to remove.
 * @return bool Return true on success and false if failed.
 */
function removeModFromNavBar($mod) {
  // Get global values
  global $con, $prefix, $ds;
  try {
    // Delete Mod from navlist
    $query = "DELETE FROM `" . PREFIX_EMPA . "NavList` WHERE `Mod` = '" . $mod . "'";
    $con->Execute($query);
    // Get all groups
    $query = "SELECT `GrpId` FROM `" . PREFIX_EMPA . "NavGroups` ORDER BY `weight`";
    $rs = $con->Execute($query);
    if ($rs) {
      // update weight on each groups.
      while ($GrpId = $rs->FetchRow()) {
        $query = "SELECT * FROM `" . PREFIX_EMPA . "NavList` WHERE `GrpId` = " . $GrpId['GrpId'] . " ORDER BY `weight` ASC";
        $rs2 = $con->Execute($query);
        if ($rs2) {
          $i = 1;
          $data = array();
          // Build Navlist update query
          while ($Info = $rs2->FetchRow()) {
            $data[] = array(
              'GrpId' => $Info['GrpId'],
              'Mod' => $Info['Mod'],
              'weight' => $i
            );
            $i++;
          }; // While $Info
          $rs2->close();
          if (!empty($data)) {
            $types = array('GrpId' => 'I', 'Mod' => 'C', 'weight' => 'I');
            $query = BuildInsertUpdateQuery($data, $types, PREFIX_EMPA . 'NavList');
            // Update weight on working groupe.
            $con->Execute($query);
          };
        };// if ($rs)
      };// While $GrpId
      $rs->close();
    };// if ($rs)
    return true;
  }
  catch(ADODB_Exception $e) {
    return false;
  }
}
?>
