<?php
//creates subsets/ entry,paragraph

const API_DIR = '/home/shared/api.kohovolit.eu';
require 'ApiDirect.php';
error_reporting(E_ALL);
set_time_limit(0);
$ac = new ApiDirect('bs');

$src = array('entry','paragraph');

foreach ($src as $type) {
  $ar = $ac->read('Set',array('set_kind_code' => $type));
  foreach ($ar as $row) {
    //has a parent?
    if (($type == 'paragraph') and (floor($row['code']/10) >= 31) and (floor($row['code']/10) <= 32))
      $parent = array('code' => '31 a 32', 'set_kind_code' => 'paragraph');
    else if (($type == 'paragraph') and (floor($row['code']/10) >= 413) and (floor($row['code']/10) <= 414))
      $parent = array('code' => '413 a 414', 'set_kind_code' => 'paragraph');
    else if (($type == 'entry') and (floor($row['code']/10) >= 122) and (floor($row['code']/10) <= 123))
      $parent = array('code' => '122 a 123', 'set_kind_code' => 'entry');
    else if (($type == 'entry') and (floor($row['code']/10) >= 161) and (floor($row['code']/10) <= 162))
      $parent = array('code' => '161 a 162', 'set_kind_code' => 'entry');
    else if (($type == 'paragraph') and (floor($row['code']/10) == 0) )
      $parent = null;
    else
      $parent = $ac->readOne('Set',array('code' => floor($row['code']/10), 'set_kind_code' => $type));
      
    if ($parent) {
      //new?
      $sub = $ac->readOne('SubsetInSet',array('sub_set_code' => $row['code'], 'sub_set_kind_code' => $type, 'sup_set_code' => $parent['code'], 'sup_set_kind_code' => $type));
      if (!$sub)
        $ac->create('SubsetInSet',array('sub_set_code' => $row['code'], 'sub_set_kind_code' => $type, 'sup_set_code' => $parent['code'], 'sup_set_kind_code' => $type));
    } else echo $row['code'] . $row['set_kind_code'] . floor($row['code']/10) . '<br/>';
      
    
  }

}
echo '*';
