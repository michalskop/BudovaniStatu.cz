<?php
/**
* get all the data for bubbletree from the rawsalad server
*/

set_time_limit(0);

//global $max_recursion_level;
$max_recursion_level = 0;
//define server, dataset, etc.
$connection = array(
	'host' => 'http://cz.cecyf.megivps.pl/api/',
	'format' => 'json',
	'dataset' => 0,
	'view' => 0,
);
//year
$year = 2010;
$data->label = 'Rozpočet';
$data->urlLabel = 'rozpocet';
$data->amount = 1279434643200;

//first level
$level = 'a';
/*$url = $connection['host'].$connection['format'].'/dataset/'.$connection['dataset'].'/view/'.$connection['view'].'/issue/'.$year.'/'.$level;
$result = json_decode(file_get_contents($url));*/
$url = 'http://cz.cecyf.megivps.pl/api/json/dataset/0/view/0/issue/2010/a/';
$result = json_decode(file_get_contents($url));
$i = 0;
$data = new stdClass;
$recursion_level = 0;
if ($result->response == 'OK') {
  //recursion
  $data->children = recursion($result,$recursion_level);
}

$file = fopen('/home/michal/budovanistatu.cz/bubbletree/dev/datab5.json',"w+");
fwrite($file,json_encode($data));
fclose($file);

function recursion($result,$recursion_level) {
  global $i, $max_recursion_level;
  foreach ($result->data as $row) {
    //echo $i.':'.$row->name."<br/>";
    $d = new stdClass;
    //add names for bubbletree
    //print_r($row);die();
    $d->label = $row->name;
    $d->amount = $row->hodnota;
    $d->idef = $row->idef;
    //$d->srcParent = $row->parent;
    //echo $d->urlLabel;die();
    if ((!$row->leaf) and ($recursion_level<$max_recursion_level)) {
      $next = ord($row->level) + 1;
      $url = $result->uri . $row->idef . '/' . chr($next) . '/';
      $r = json_decode(file_get_contents($url));
      //$data->children = null;
      //$data = new stdClass;
      //$data->children->$tmp = new stdClass;
      //if (ord($row->level) <= ord('b'))
        $d->children = recursion($r,$recursion_level+1);
    }
    $data[] = $d;
  }
  //echo $row->level;//if($row->level == 'a') 
  //print_r($data); $i++; if ($i>10) die();
  $i++;
  return $data;
}
echo $i;


	



?>
