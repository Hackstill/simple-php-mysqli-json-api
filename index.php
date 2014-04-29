<?
include_once('mysql_war_sett.php');
include_once('mysql_magic.php');
include_once('functions.php');
define('DEBUG', 1);
define('NOLOG', 1);

if (!sizeof($functions))
  show_error('Function library not loaded!');
$func = $functions[$_REQUEST['func_name']];
if (!is_array($func))
  show_error('Error: function called does not exist in API!');
$sql = 'select ' . $func['fields'] . ' from ' . $func['tables'];
$where = array();
if ($func['where'])
  $where[] = $func['where'];
$type = $param = array();
if ($func['args'])
  foreach ($func['args'] as $key => $arg)
    if ($_REQUEST[$arg])
    {
      $cmp = $func['arg_cmp'][$key];
      if (!$cmp)
        $cmp = '=';
      $where[] = $arg . $cmp . '?';
      $type[] = $func['arg_types'][$key];
      $param[] = $_REQUEST[$arg];
    }
$sql .= ' where ' . implode(' and ', $where);
if ($func['order'])
  $sql .= ' order by ' . $func['order'];
if ($func['order'] && $func['desc'])
  $sql .= ' desc';
elseif ($func['order'] && $func['asc'])
  $sql .= ' asc';
$c = $CONNECTS[$func['connect_name']];
if (!$c)
  show_error('Connect for the function does not exist in settings file!');

$link = @ new mysqli($c['host'], $c['user'], $c['password'], $c['database']);

if ($link->connect_errno)
  show_error('Connect failed (' . $link->connect_error . ')');
$link->query('SET NAMES UTF8');
$stmt = @$link->prepare($sql);
if ($stmt === false)
  show_error('Query parsing incorrect (' . $link->error . ')');
$params = array_merge($type, $param);
call_user_func_array(array($stmt, 'bind_param'), refValues($params));
$res = $stmt->execute();
if ($res === false)
  show_error('Query execution failed (' . $link->error . ')');
$row = array();
$arr = array();
$stmt->store_result();
stmt_bind_assoc($stmt, $row);
while ($stmt->fetch())
{
  $arr[] = $row;
  stmt_bind_assoc($stmt, $row);
}

$stmt->free_result();
$stmt->close();
$link->close();
$arr = array('data' => $arr);
echo json_encode($arr);