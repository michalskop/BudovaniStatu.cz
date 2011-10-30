<?php

const API_DIR = '/home/shared/api.kohovolit.eu';
require 'ApiDirect.php';
error_reporting(E_ALL);
set_time_limit(0);
$ac = new ApiDirect('bs');echo '*';

$two = array(
  array (
    'name' => 'entry',
    'big' => 'Entry'
  ),
  array(
    'name' => 'paragraph',
    'big' => 'Paragraph'
  )
);

foreach ($two as $one) {

	$url = "https://api.scraperwiki.com/api/1.0/datastore/sqlite?format=jsondict&name=cz_budget_accounting_hierarchy&query=select%20*%20from%20%60{$one['name']}%60";
	$ar = json_decode(file_get_contents($url));

	foreach ($ar as $row) {
	  $src_label = get_label($row);
	  //check if label already exists
	  $label_db = $ac->readOne('Label',array('label_kind_code' => $one['name'].'_id', 'name' => $src_label['value']));
	  
	  if (!$label_db)
		$label_db = $ac->create('Label',array('label_kind_code' => $one['name'].'_id', 'name' => $src_label['value']));

	  //get label_id
	  $label_id = $label_db['id'];
	  
	  //get tree_id
	  $tree = $ac->readOne('Tree',array('name' => $one['big']));
	  
	  //a new item in hierarchy?
	  unset($suplabel_db);
	  $suplabel = $src_label['key'] - 1;

	  if (isset($row->$suplabel))
	  //get id of suplabel
		$suplabel_db = $ac->readOne('Label',array('label_kind_code' => $one['name'].'_id', 'name' => $row->$suplabel));
		
	  if (isset($suplabel_db) and $suplabel_db) {
		//hierarchy already exists?
		$hierarchy_db = $ac->readOne('Hierarchy',array('sup_label_id' => $suplabel_db['id'], 'sub_label_id' => $label_id, 'tree_id' => $tree['id']));
		//insert new hierarchy, if does not exist
		if (!$hierarchy_db)
		  $ac->create('Hierarchy',array('sup_label_id' => $suplabel_db['id'], 'sub_label_id' => $label_id, 'tree_id' => $tree['id']));
	  }
	  

	}

}

function get_label($row) {
  for ($i = 0; $i <=3; $i++) {
    if ($row->$i != '')
    	$label = array('key' => $i, 'value' => $row->$i);
    else return $label;
  }
  return $label;
}

?>
