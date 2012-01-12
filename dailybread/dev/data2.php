<?php
/**
* get all the data for daily bread from the rawsalad server
*/

//define server, dataset, etc.
$connection = array(
	'host' => 'http://cz.cecyf.megivps.pl/api/',
	'format' => 'json',
	'dataset' => 0,
	'view' => 0,
);
//year
$year = 2010;
//level(s)
$levels = 'a';

$url = $connection['host'].$connection['format'].'/dataset/'.$connection['dataset'].'/view/'.$connection['view'].'/issue/'.$year.'/'.$levels;

$result = json_decode(file_get_contents($url));
if ($result->response == 'OK') {
  foreach ($result->data as $row) {
    //print_r($row);
    echo implode("\t",array($row->name,$row->idef,$row->type,$row->hodnota)) . "<br/>\n";
    $url = $connection['host'].$connection['format'].'/dataset/'.$connection['dataset'].'/view/'.$connection['view'].'/issue/'.$year.'/'.$levels.'/'.$row->idef.'/b';
    $resultb = json_decode(file_get_contents($url));
    foreach ($resultb->data as $rowb) {
      echo implode("\t",array($rowb->name,$rowb->idef,$rowb->type,$rowb->hodnota)) . "<br/>\n";
    }
  }
}
	



?>
