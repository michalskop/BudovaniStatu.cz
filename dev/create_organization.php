<?php

//insert all chapters

const API_DIR = '/home/shared/api.kohovolit.eu';
require 'ApiDirect.php';
error_reporting(E_ALL);
set_time_limit(0);
$ac = new ApiDirect('bs');

$url = "https://api.scraperwiki.com/api/1.0/datastore/sqlite?format=jsonlist&name=cz_public_organizations_2_details&query=select%20*%20from%20%60swdata%60";
$ar = json_decode(file_get_contents($url));
//find keys
$keys = $ar->keys;
$key['name'] = array_search('Nezkrácený název organizace dle ČSÚ',$keys);
$key['short_name'] = array_search('NAO',$keys);
$key['org_id'] = array_search('org_id',$keys);

foreach ($ar->data as $row) {
  if (!$row[$key['org_id']]) continue; //empty row
  
  $org_id = $row[$key['org_id']] +1-1; //get rid of leading 0s
  
  //check if already exists
  $label_db = $ac->readOne('Organization',array('code' => $org_id));
  
  if (!$label_db) 
    $ac->create('Organization',array('code' => $org_id, 'name' => $row[$key['name']], 'short_name' => $row[$key['short_name']]));
} 
//missing values
$missing = array(
  array('564222','Úřad práce hl.m. Prahy','ÚP hl.m.Prahy'),
  array('48137430','Ministerstvo financí České republiky Generální ředitelství cel','Gen.ředitelství cel'),
  array('19470','INSTITUT VÝCHOVY A VZDĚLÁVÁNÍ MINISTERSTVA ZEMĚDĚLSTVÍ ČR','Inst.vých.a vzděl. MZČR'),
  array('209554','ÚŘAD PRÁCE BRNO - MĚSTO','ÚP Brno-měs.'),
  array('6921','FINANČNÍ ŘEDITELSTVÍ V BRNĚ','FŘ Brno'),
  array('560871','Úřad práce v Ostravě','ÚP Ostrava'),
);
foreach ($missing as $m) {
//check if already exists
  $label_db = $ac->readOne('Organization',array('code' => $m[0]));
  
  if (!$label_db) 
    $ac->create('Organization',array('code' => $m[0], 'name' => $m[1], 'short_name' => $m[2]));
}  

//add missing


echo '*';
 
?>
