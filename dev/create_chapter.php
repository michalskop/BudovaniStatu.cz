<?php

//insert all chapters

const API_DIR = '/home/shared/api.kohovolit.eu';
require 'ApiDirect.php';
error_reporting(E_ALL);
set_time_limit(0);
$ac = new ApiDirect('bs');

$url = "https://api.scraperwiki.com/api/1.0/datastore/sqlite?format=jsondict&name=cz_budget_chapters&query=select%20*%20from%20%60swdata%60";
$ar = json_decode(file_get_contents($url));

foreach ($ar as $row) {
  //check if already exists
  $label_db = $ac->readOne('Chapter',array('code' => $row->id));
  
  if (!$label_db) 
    $ac->create('Chapter',array('code' => $row->id, 'name' => $row->name));
}  
 
?>
