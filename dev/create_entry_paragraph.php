<?php

//inserts all entries and paragraphs

const API_DIR = '/home/shared/api.kohovolit.eu';
require 'ApiDirect.php';
error_reporting(E_ALL);
set_time_limit(0);
$ac = new ApiDirect('bs');

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
	  //check if already exists
	  $label_db = $ac->readOne($one['big'],array('code' => $src_label['value']));
	  
	  if (!$label_db)
	    $label_db = $ac->create($one['big'],array('code' => $src_label['value'], 'name' => $row->name, 'description' => $row->description));
	    

	}
}
	    
//add missing
//extracted using 
//SELECT distinct(paragraph_code),max(o.code),count(o.*) from value_50 as v
//LEFT JOIN paragraph as o
//on v.paragraph_code = o.code
//group by (v.paragraph_code)
//order by max
$missing = array(
  array('Paragraph','0'),
  array('Entry','5330'),
  array('Entry','6350'),
  array('Entry','5121'),
  array('Entry','5111'),
  array('Paragraph','2180'),
  array('Paragraph','5440'),
  array('Entry','5250'),
  array('Entry','5122'),
  array('Entry','2210'),
  array('Paragraph','4316'),
  array('Entry','1616'),
  array('Entry','2323'),
  array('Entry','5112'),
  array('Entry','5113'),
  array('Entry','5114'),
 array('Entry', "2332"),
array('Entry',"5028"),
array('Entry',"5051"),
array('Entry',"5113"),
array('Entry',"5114"),
array('Entry',"5115"),
array('Entry',"5116"),
array('Entry',"5117"),
array('Entry',"5119"),
array('Entry',"5128"),
array('Entry',"5129"),
array('Entry',"5174"),
array('Entry',"5198"),
array('Entry',"5530"),
array('Entry',"6126"),
array('Entry',"6141"),
array('Entry',"6142"),
array('Entry',"6144"),
array('Entry',"6145"),
array('Entry',"6149"),
array('Entry',"6450"),
array('Paragraph',"1092"),
array('Paragraph',"2140"),
array('Paragraph',"3115"),
array('Paragraph',"3116"),
array('Paragraph',"3127"),
array('Paragraph',"3281"),
array('Paragraph',"3289"),
array('Paragraph',"3660"),
array('Paragraph',"3752"),
array('Paragraph',"4135"),
array('Paragraph',"4143"),
array('Paragraph',"4220"),
array('Paragraph',"4224"),
array('Paragraph',"4313"),
array('Paragraph',"4314"),
array('Paragraph',"4323"),
array('Paragraph',"4346"),
array('Paragraph',"4347"),
array('Paragraph',"5110"),
array('Paragraph',"5192"),
array('Paragraph',"5229"),
 
);
foreach ($missing as $m) {
//check if already exists
	  $label_db = $ac->readOne($m[0],array('code' => $m[1])); 
	  if (!$label_db)
	    $label_db = $ac->create($m[0],array('code' => $m[1], 'name' => $m[1], 'description' => ''));
}

echo '*';


function get_label($row) {
  for ($i = 0; $i <=3; $i++) {
    if ($row->$i != '')
    	$label = array('key' => $i, 'value' => $row->$i);
    else return $label;
  }
  return $label;
}
