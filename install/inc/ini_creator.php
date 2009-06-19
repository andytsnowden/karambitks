<?php
/**
 * EVE MultiPurpose Application
 *
 * EMPA Setup.
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
 * @subpackage EMPASetup
 * @version    SVN: $Id$ (Only to be used in key files)
 * @link       http://code.google.com/p/empa/
 * @link       http://www.eve-online.com/
 */
/**
 * @internal Only let this code be included or required not ran directly.
 */
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
  exit();
};
/*
* Set path to kks.ini
*/
$empaConfigFile = KKS_CONFIG;
/**
 * Set true or false value
 *
 * This is used to corect the values from the ini file.
 *
 * @param string|bool $value The value to convert
 * @return string Return string "TRUE" if the value is true or string "FALSE" if not
 */
function trueOrFalse($value) {
  if ($value) {
    return 'TRUE';
  } else {
    return 'FALSE';
  }; // if ($value)
}
/*
* Build kks.ini
*
* If run from setup.php
*/
if (defined('UPDATE_EMPA')) {
  $EMPAinidata = ';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
; Comments on setting up config/kks.ini manually can be found in
; config/empa-example.ini.
; Additional information can be found in the Wiki at:
; http://code.google.com/p/empa/
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
; REQUIRED Sections: Api, Database, EMPALogging, Prefix, Logging.
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
date="$Date:: ' . date('Y-m-d H:i:s', time()) . ' #$"
stability="alpha"
version="' . KKS_VERSION . '"
salt="' . $iniVars['salt'] . '"

; Used for Yapeal
[Api]
account_active=' . trueOrFalse($iniVars['Api']['account_active']) . '
cache_xml=' . trueOrFalse($iniVars['Api']['cache_xml']) . '
char_active=' . trueOrFalse($iniVars['Api']['char_active']) . '
corp_active=' . trueOrFalse($iniVars['Api']['corp_active']) . '
eve_active=' . trueOrFalse($iniVars['Api']['eve_active']) . '
file_suffix="' . $iniVars['Api']['file_suffix'] . '"
map_active=' . trueOrFalse($iniVars['Api']['map_active']) . '
server_active=' . trueOrFalse($iniVars['Api']['server_active']) . '
url_base="' . $iniVars['Api']['url_base'] . '"

; Used for KKS and Yapeal
[Database]
database="' . $iniVars['Database']['database'] . '"
driver="' . $iniVars['Database']['driver'] . '"
host="' . $iniVars['Database']['host'] . '"
suffix="' . $iniVars['Database']['suffix'] . '"
table_prefix="' . $iniVars['Database']['table_prefix'] . '"
username="' . $iniVars['Database']['username'] . '"
password="' . $iniVars['Database']['password'] . '"
xmldriver="' . $iniVars['Database']['xmldriver'] . '"


; Used for Yapeal
[Logging]
error_log="yapeal_error.log"
log_level=E_ERROR|E_WARNING|E_USER_ERROR|E_USER_WARNING
notice_log="yapeal_notice.log"
warning_log="yapeal_warning.log"
trace_active=FALSE
trace_level=0
trace_log="yapeal_trace.log"
trace_output="file"
trace_section=YAPEAL_TRACE_NONE

; Used for KKS
[Cache]
killlist=60
stats=900
killdetail=1800
dwoo=300


; Used for KKS
[Prefix]
kks="' . $iniVars['Prefix']['kks'] . '"
yapeal="' . $iniVars['Prefix']['yapeal'] . '"
eve="' . $iniVars['Prefix']['eve'] . '"';

  // Create kks.ini
  $fp = fopen(KKS_CONFIG . 'kks.ini', 'w');
  fwrite($fp, $EMPAinidata);
  fclose($fp);
  if (!(@is_readable(KKS_CONFIG . 'kks.ini') ||
    @is_file(KKS_CONFIG . 'kks.ini') ||
    @parse_ini_file(KKS_CONFIG . 'kks.ini'))) {
    OutPut(array(
            'action' => 'Create',
            'info' => 'kks.ini (Config file)',
            'status' => 'Failed' ,
            'check' => 0
          ));
    $stop++;
  } else {
    OutPut(array(
            'action' => 'Create',
            'info' => 'kks.ini (Config file)',
            'status' => 'Done' ,
            'check' => 1
          ));
  }; // if else
/*
* If run from setup.php
*/
} else {
  $EMPAinidata = ';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
; Comments on setting up config/kks.ini manually can be found in
; config/empa-example.ini.
; Additional information can be found in the Wiki at:
; http://code.google.com/p/empa/
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
; REQUIRED Sections: Api, Database, EMPALogging, Prefix, Logging.
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
date="$Date:: ' . date('Y-m-d H:i:s', time()) . ' #$"
stability="alpha"
version="' . $KKS_Version . '"
salt="' . $_REQUEST['siteSalt'] . '"

; Used for Yapeal
[Api]
account_active=TRUE
cache_xml=FALSE
char_active=TRUE
corp_active=TRUE
eve_active=TRUE
file_suffix=".xml.aspx"
map_active=TRUE
server_active=TRUE
url_base="http://api.eve-online.com"

; Used for EMPA and Yapeal
[Database]
database="' . $_REQUEST['DB_Database'] . '"
driver="mysqli://"
host="' . $_REQUEST['DB_Host'] . '"
suffix="?new"
table_prefix="' . $prefix['yapeal'] . '"
username="' . $_REQUEST['DB_Username'] . '"
password="' . $_REQUEST['DB_Password'] . '"
xmldriver="mysql://"

; Used for Yapeal
[Logging]
error_log="yapeal_error.log"
log_level=E_ERROR|E_WARNING|E_USER_ERROR|E_USER_WARNING
notice_log="yapeal_notice.log"
warning_log="yapeal_warning.log"
trace_active=FALSE
trace_level=0
trace_log="yapeal_trace.log"
trace_output="file"
trace_section=YAPEAL_TRACE_NONE

; Used for EMPA
[Prefix]
empa="' . $prefix['empa'] . '"
yapeal="' . $prefix['yapeal'] . '"
eve="' . $prefix['db_dump'] . '"';
  // Create kks.ini
  $fp = fopen(KKS_CONFIG . 'kks.ini', 'w');
  fwrite($fp, $EMPAinidata);
  fclose($fp);
  if (!(@is_readable(KKS_CONFIG . 'kks.ini') ||
    @is_file(KKS_CONFIG . 'kks.ini') ||
    @parse_ini_file(KKS_CONFIG . 'kks.ini'))) {
    $inis = array(
      'action' => 'Create:',
      'info' => 'kks.ini (Config file)',
      'status' => 'Failed',
      'check' => 0
    );
    $data->append('inis', $inis);
    unset($inis);
    $stop++;
  } else {
    $inis = array(
      'action' => 'Create:',
      'info' => 'kks.ini (Config file)',
      'status' => 'Done',
      'check' => 1
    );
    $data->append('inis', $inis);
    unset($inis);
  }; // if else
};
?>
