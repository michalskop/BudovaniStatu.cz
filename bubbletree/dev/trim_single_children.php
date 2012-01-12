<?php
/**
* trims single children from data
*/

$url = "./datab7.json";
$source = json_decode(file_get_contents($url));

recursion($source);
$data = $source;


function recursion(&$parent) {
  foreach ($parent->children as $child) {
    //echo $child->label . "<br/>";
    if (property_exists($child, 'children')) {
      recursion($child);
    } else {
      if (count($parent->children) == 1)
        unset($parent->children);
    }
  }
}

$file = fopen('/home/michal/budovanistatu.cz/bubbletree/dev/datab8.json',"w+");
fwrite($file,json_encode($data));
fclose($file);
?>
