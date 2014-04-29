<?
if ($_REQUEST['hash'] !== '123')
  die('Error: Access error');
include_once('mysql_war_sett.php');
include_once('functions.php');
include_once('mysql_magic.php');
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
      $where[] = $arg . '=?';
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
$type = $param = array();
for ($i = 0; $i < mb_strlen($func['arg_types']); $i++)
{
  $type[] = $func['arg_types'][$i];
  $param[] = $_REQUEST[$func['args'][$i]];
}

$params = array_merge($type, $param);
call_user_func_array(array($stmt, 'bind_param'), refValues($params));
$res = $stmt->execute();
if ($res === false)
  show_error('Query execution failed (' . $link->error . ')');
$arr = array();
stmt_bind_assoc($stmt, $row);

// loop through all result rows
while ($stmt->fetch())
{
  $arr[] = $row;
}

@$stmt->close();
@$link->close();
$arr = array('data' => $arr);
echo json_encode($arr);