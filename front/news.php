<?php
/**
* get 3 new articles from NP
*/


$limit = 3;

$url = "http://www.nasipolitici.cz/budovani-statu/new-listing?limit=". $limit;
$ar = json_decode(file_get_contents($url));


if (count($ar) > 0)
  echo create_news_table($ar);

function create_news_table($ar) {
  $html = '';
  $i = 0;
  foreach($ar as $item) {
    $html .= "<div class='article".($i>0 ? " delimiter":'')."'><h3>{$item->title}</h3>";
    $html .="<p>{$item->text}";
    $html .= "<a class='read' href='news/{$item->id}'>Více</a></p></div><div class='cleaner'></div>";
    $i++;
  }
  return $html;
}

?>
