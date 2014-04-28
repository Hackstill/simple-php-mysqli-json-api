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

?>