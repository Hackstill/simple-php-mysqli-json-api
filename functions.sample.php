<?
/*for arg types:

i corresponding variable has type integer
d corresponding variable has type double
s corresponding variable has type string
b corresponding variable is a blob and will be sent in packets

*/
$functions = array(
  'sample_func_1' => array(
    'args' => array('node_id'),
    'arg_types' => 'i',#write arg types in string, for example 'iddsd'
    'arg_cmp'=>'>',
    'fields' => '*',
    'tables' => 'tree',
    'where' => '',
    'connect_name' => 'HOME',),
);


#sample authorization
if ($_REQUEST['token'] !== '123')
  show_error('Error: Access error');

#http://localhost/simple_api/index.php?func_name=sample_func_1&node_id=17&token=123
?>