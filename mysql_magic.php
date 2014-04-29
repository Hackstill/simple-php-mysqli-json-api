<?
function add_log($log, $show = 0)
{
  global $CURR_LOG;
  if (!$log)
    return;
  if ($CURR_LOG && (@constant('NOLOG') != 1))
  {
    $dir = './logs/' . date('Y-m-d');
    @mkdir($dir, 0777, true);
    file_put_contents($dir . '/' . $CURR_LOG, "\n<br>" . $log, FILE_APPEND);
  }
  if ($show)
    echo "\n<br>" . $log;
}

function show_error($text, $fatal = 1)
{
  $text = array('Error' => $text);
  echo json_encode($text);
  if ($fatal)
    die();
}
function stmt_bind_assoc (&$stmt, &$out) {
  $data = mysqli_stmt_result_metadata($stmt);
  $fields = array();
  $out = array();

  $fields[0] = $stmt;
  $count = 1;

  while($field = mysqli_fetch_field($data)) {
    $fields[$count] = &$out[$field->name];
    $count++;
  }
  call_user_func_array(mysqli_stmt_bind_result, $fields);
}

function refValues($arr){
  if (strnatcmp(phpversion(),'5.3') >= 0) //Reference is required for PHP 5.3+
  {
    $refs = array();
    foreach($arr as $key => $value)
      $refs[$key] = &$arr[$key];
    return $refs;
  }
  return $arr;
}
?>