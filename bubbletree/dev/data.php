<?php
/**
* get all the data for bubbletree from the rawsalad server
*/

set_time_limit(0);

//global $max_recursion_level;
$max_recursion_level = 3;
//exclude idefs on level 1 from recursion limit
$exclude_recursion_level = array(313,335); 
//define server, dataset, etc.
$connection = array(
	'host' => 'http://cz.cecyf.megivps.pl/api/',
	'format' => 'json',
	'dataset' => 0,
	'view' => 0,
);
//year
$year = 2010;
$data = new StdClass;
$data->label = 'Rozpočet';
$data->urlLabel = 'rozpocet';
$data->amount = 1156857957990;

//first level
$level = 'a';
/*$url = $connection['host'].$connection['format'].'/dataset/'.$connection['dataset'].'/view/'.$connection['view'].'/issue/'.$year.'/'.$level;
$result = json_decode(file_get_contents($url));*/
$url = 'http://cz.cecyf.megivps.pl/api/json/dataset/0/view/0/issue/2010/a/';
$result = json_decode(file_get_contents($url));
$i = 0;

$file = fopen('/home/michal/budovanistatu.cz/bubbletree/dev/datab10.json',"w+");

$recursion_level = 0;
if ($result->response == 'OK') {
  //recursion
  $data->children = recursion($result,$recursion_level);
}

fwrite($file,json_encode($data));
fclose($file);

function recursion($result,$recursion_level) {
  global $i, $max_recursion_level;
  foreach ($result->data as $row) {
    //echo $i.':'.$row->name."<br/>";
    $d = new stdClass;
    //add names for bubbletree
    //print_r($row);die();
    $d->l = $row->name;
    $d->a = $row->hodnota;
    $d->i = $row->idef;
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
