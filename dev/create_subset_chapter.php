<?php
//creates subsets/ chapter-organization

const API_DIR = '/home/shared/api.kohovolit.eu';
require 'ApiDirect.php';
error_reporting(E_ALL);
set_time_limit(0);
$ac = new ApiDirect('bs');

$url = "https://api.scraperwiki.com/api/1.0/datastore/sqlite?format=jsondict&name=cz_public_organizations_2_retrieval&query=select%20*%20from%20%60swdata%60";
$ar = json_decode(file_get_contents($url));

$chapters = array();

foreach ($ar as $row) {
  //get rid of 0s
  $org_id = $row->org_id +1-1;
  //check if subset already exists
  $h = $ac->readOne('SubsetInSet',array('sub_set_code' => $org_id, 'sub_set_kind_code' => 'organization', 'sup_set_code' => $row->chapter, 'sup_set_kind_code' => 'chapter'));

  if (!$h)
    $ac->create('SubsetInSet',array('sub_set_code' => $org_id, 'sub_set_kind_code' => 'organization', 'sup_set_code' => $row->chapter, 'sup_set_kind_code' => 'chapter'));
   
  $chapters[$row->chapter] = true;
}


echo '#';
?>
