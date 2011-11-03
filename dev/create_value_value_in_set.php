<?php

const API_DIR = '/home/shared/api.kohovolit.eu';
require 'ApiDirect.php';
error_reporting(E_ALL);
set_time_limit(0);
$ac = new ApiDirect('bs');

$handle = fopen('/home/michal/aris/central-40-50-3.csv',"r");

//read first row = header
fgetcsv($handle);

while (($line = fgetcsv($handle,0,"\t")) !== false) {
  //print_r($line);die();
  $value = $ac->create('Value',array('value' => $line[6]));
  $ac->create('ValueInSet',array('set_code' => $line[1],'set_kind_code' => 'organization','value_id' => $value['id']));
  $ac->create('ValueInSet',array('set_code' => $line[2],'set_kind_code' => 'year','value_id' => $value['id']));
  $ac->create('ValueInSet',array('set_code' => $line[3],'set_kind_code' => 'paragraph','value_id' => $value['id']));
  $ac->create('ValueInSet',array('set_code' => $line[4],'set_kind_code' => 'entry','value_id' => $value['id']));
  $ac->create('ValueInSet',array('set_code' => $line[5],'set_kind_code' => 'column','value_id' => $value['id']));
}
fclose ($handle);


?>
