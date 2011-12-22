<?php
/**
* generates table for bubleetree
*/

$since = 2004;
$thru = 2010;
setlocale(LC_MONETARY, 'cs_CZ.utf8');

$data = array();
if ($_GET['idef'] == 'undefined') $idef = '';
else $idef = $_GET['idef'];
$id_ar = explode('-',$idef);
if (count($id_ar > 0)) {
  //get data
  for ($year = $since; $year <= $thru; $year ++) {
      //form url
	  $letter = 97;
	  $chunk = '';
	  $url_bit = '';
	  if ($idef != '') {
		  foreach ($id_ar as $item) {
			if ($chunk == '')
			  $chunk = $item;
			else
			  $chunk = implode('-',array($chunk,$item));
			$url_bit .= chr($letter) . '/' . $chunk . '/';
			$letter++;
		  }
	  }
	  //download data
	  $url = "http://cz.cecyf.megivps.pl/api/json/dataset/1/view/0/issue/{$year}/{$url_bit}".chr($letter).'?fields=hodnota,idef,name';
	  if ($file = file_get_contents($url)) {
    	$object = json_decode($file);
	    $data[$year] = $object->data;
	  }
  }
  //get parent info
  if ($idef == '') $parent = 'Státní rozpočet';
  else {
    $url = "http://cz.cecyf.megivps.pl/api/json/dataset/1/view/0/issue/{$thru}/a/{$idef}?fields=name";
    $object = json_decode(file_get_contents($url));
    $parent = $object->data[0]->name;
  }
  
  //reorder data
  $rodata = array();
  $sum = array();
  $names = array();
  //print_r($data);die();
  foreach ($data as $key => $d) {
    foreach ($d as $item) {
		$rodata[$item->idef][$key] = $item->hodnota;
		$names[$item->idef] = $item->name;
		if (isset($sum[$key])) $sum[$key] += $item->hodnota;
		else $sum[$key] = $item->hodnota;
    }
  }
  
  //create table
    //thead
  $table = "<table class='bs-table'>\n<thead>\n<td>Jméno</td>";
  for ($year = $since; $year <= $thru; $year ++) $table .= "<td>{$year}</td>";
  $table .=  "</tr>\n";
  $table .= "<tr id='{$_GET['idef']}'><td>{$parent}</td>";
  for ($year = $since; $year <= $thru; $year ++) $table .= "<td>".money_format("%!.0n",$sum[$year])."</td>";
  $table .= "</thead>\n";
    //body
  $table .= "<tbody>\n";
  $zebra = 'odd';
  foreach ($rodata as $key => $row) {
    $table .= "<tr id='{$key}' class='{$zebra}'><td>{$names[$key]}</td>";
    for ($year = $since; $year <= $thru; $year ++)
        $table .= (isset($row[$year]) ? "<td>".money_format("%!.0n",$row[$year])."</td>" : "<td></td>");
    $table .= "</tr>\n";
    if ($zebra == 'odd') $zebra = 'even';
    else $zebra = 'odd';
  }
  $table .= "</tbody>\n</table>\n";
  
  echo $table;
} else {
 echo '-';
}


?>
