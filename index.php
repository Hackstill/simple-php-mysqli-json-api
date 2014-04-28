<?
if ($_REQUEST['hash'] !== '123')
  die('Error: Access error');
include_once('oracle_war_sett.php');
include_once('functions.php');
include_once('oracle_magic.php');
define('DEBUG', 1);
define('NOLOG', 1);

if (!sizeof($functions))
  show_error('Function library not loaded!');
$func = $functions[$_REQUEST['func_name']];
if (!is_array($func))
  die('Error: function called does not exist in API!');
$sql = 'select ' . $func['fields'] . ' from ' . $func['tables'];
$where = array();
if ($func['where'])
  $where[] = $func['where'];
if ($func['args'])
  foreach ($func['args'] as $arg)
    if ($_REQUEST[$arg])
      $where[] = $arg . '=:' . $arg;
$sql .= ' where ' . implode(' and ', $where);
if ($func['order'])
  $sql .= ' order by ' . $func['order'];
if ($func['order'] && $func['desc'])
  $sql .= ' desc';
elseif ($func['order'] && $func['asc'])
  $sql .= ' asc';

$c = $CONNECTS[$func['connect_name']];
if (!$c)
  show_error('connect for the function does not exist in settings file!');

if ($c['tns'])
  $conn = oci_connect($c['scheme'], $c['pass'], $c['tns'], $c['enc'], $c['mode']);
else
  $conn = oci_connect($c['scheme'], $c['pass'], $c['connect'], $c['enc'], $c['mode']);
if ($conn == false)
{
  $e = oci_error();
  show_error('connect failed (' . htmlentities($e['message'], ENT_QUOTES) . ')');
}
$stid = oci_parse($conn, $sql);
if ($stid == false)
{
  $e = oci_error();
  show_error('Query incorrect (' . htmlentities($e['message'], ENT_QUOTES) . ')');
}

foreach ($func['args'] as $arg)
  oci_bind_by_name($stid, ':' . $arg, $_REQUEST[$arg]);
oci_execute($stid);
$arr = array();
while ($row = oci_fetch_array($stid, OCI_ASSOC))
{
  foreach ($row as $name => $val)
    if (is_object($val))
      $row[$name] == $row[$name]->load();
  $arr[] = $row;
}
oci_free_statement($stid);
oci_close($conn);
$arr = array('data' => $arr);
echo json_encode($arr);