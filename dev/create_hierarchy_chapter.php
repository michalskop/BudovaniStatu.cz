<?php
//creates hierarchy for tree : 'Test_20111031'

const API_DIR = '/home/shared/api.kohovolit.eu';
require 'ApiDirect.php';
error_reporting(E_ALL);
set_time_limit(0);
$ac = new ApiDirect('bs');

$url = "https://api.scraperwiki.com/api/1.0/datastore/sqlite?format=jsondict&name=cz_public_organizations_2_retrieval&query=select%20*%20from%20%60swdata%60";
$ar = json_decode(file_get_contents($url));

//tree_id
$tree = $ac->readOne('Tree',array('name' => 'Test_20111031'));

$chapters = array();

foreach ($ar as $row) {
  //get rid of 0s
  $org_id = $row->org_id +1-1;
  //check if hierarchy already exists
  $h = $ac->readOne('Hierarchy',array('sub_code' => $org_id, 'sub_table' => 'organization', 'sup_code' => $row->chapter, 'sup_table' => 'chapter', 'tree_id' => $tree['id']));

  if (!$h)
    $ac->create('Hierarchy',array('sub_code' => $org_id, 'sub_table' => 'organization', 'sup_code' => $row->chapter, 'sup_table' => 'chapter', 'tree_id' => $tree['id']));
   
  $chapters[$row->chapter] = true;
}
//add chapters below chapte '0'
foreach ($chapters as $key => $c) {
  $h = $ac->readOne('Hierarchy',array('sub_code' => $key, 'sub_table' => 'chapter', 'sup_code' => '0', 'sup_table' => 'chapter', 'tree_id' => $tree['id']));
  
  if (!$h)
    $ac->create('Hierarchy',array('sub_code' => $key, 'sub_table' => 'chapter', 'sup_code' => '0', 'sup_table' => 'chapter', 'tree_id' => $tree['id']));
}

echo '#';
?>
