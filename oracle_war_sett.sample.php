<?
##########  DEFINITIONS
set_time_limit(0);
$CURR_SESSION = '';
$CURR_LOG = '';
if (!$CURR_SESSION)
  $CURR_SESSION = 'SMA';
$CONNECT_POOL = array();
$normal_errors = array(24344, 955, 1917, 942);
$normal_errors[] = 20102;
$CONNECTS = array(
  'SMA' => array('scheme' => 'scheme', 'pass' => 'password', 'connect' => '//192.168.120.141:1521/SMA3', 'enc' => 'AL32UTF8', 'mode' => OCI_DEFAULT,),
);
define('VERBOSE', 0);
?>