<?php
/**
* generates view of people for bubbletree
*/

$since = 2004;
$thru = 2010;
//if none -> government
if (!isset($_GET['idef']) or ($_GET['idef'] == 'undefined')) $idef = '304';
else $idef = $_GET['idef'];

//get info
$json = array();
for ($year = $thru; $year >= $since; $year--) {
  $url = "http://nasipolitici.cz/budovani-statu-data?";
  $url .= 'year='.$year.'&id='.$idef;
  $json[$year] = json_decode(file_get_contents($url));
}

//reorder
$pols = array();
foreach($json as $year => $year_ar) {
  if(count($year_ar) > 0) {
    foreach($year_ar as $pol) {
      if (isset($pols[$pol->id])) 
        $pols[$pol->id]['years'][] = $year;
      else
        $pols[$pol->id] = array(
          'np_id' => $pol->id,
          'np_link' => "http://www.nasipolitici.cz/cs/politik/{$pol->id}-" . friendly_url($pol->name . '-' . $pol->surname),
          'name' => $pol->name . ' ' . $pol->surname,
          'text' => $pol->text,
          'image' => $pol->image_url,
          'years' => array($year),
        );
      
    }
  }
}

if (count($pols) == 0) echo 'Nemáme informace o lidech na této úrovni.';
else {
  $table = "<table id='bs-people-table' class='bs-table'>\n<thead>\n<th>Roky</th><th>Jméno</th><th>Základní info</th>\n</thead>\n";
  $table .= "<tbody>\n";
  foreach ($pols as $pol) {
    $y = format_years($pol['years']);
    $table .= "<tr><td>{$y}</td><td>";
    if (isset($pol['image']) and ($pol['image'] != '')) $table .= "<img src='{$pol['image']}' alt='{$pol['name']}' />";
    $table .= $pol['name'] . "</td><td>";
    $table .= html_entity_decode($pol['text']) . "<a href='{$pol['np_link']}'>Více ...</a></td></tr>";
  }
}
$table .= "</tbody>\n</table>\n";
echo $table;
/**
* format years
*/
function format_years($years) {
  sort($years);
  $ar = array();
  $n = array($years[0]);
  foreach ($years as $key => $year) {
    if (!isset($n[0])) $n[0] = $year;
    if (isset($years[$key+1])) {
      if ($years[$key+1] == ($year + 1)) {
        $n[1] = $years[$key+1];
      } else {
        $ar[] = $n;
        $n = array();
      }
    }
  }
  $ar[] = $n;
  //format
  $tmp = array();
  foreach($ar as $a) {
    if (count($a) == 2) $tmp[] = implode('-',$a);
    else $tmp[] = $a[0];
  }
  return implode(',',$tmp);
}

/**
* creates "friendly url" version of text, translits string (gets rid of diacritics) and substitutes ' ' for '-', etc.
* @return friendly url version of text
* example:
* friendly_url('klub ČSSD')
*     returns 'klub-cssd'
*/
function friendly_url($text,$locale = 'cs_CZ.utf-8') {
    $old_locale = setlocale(LC_ALL, "0");
	setlocale(LC_ALL,$locale);
	$url = $text;
	$url = preg_replace('~[^\\pL0-9_]+~u', '-', $url);
	$url = trim($url, "-");
	$url = iconv("utf-8", "us-ascii//TRANSLIT", $url);
	$url = strtolower($url);
	$url = preg_replace('~[^-a-z0-9_]+~', '', $url);
	setlocale(LC_ALL,$old_locale);
	return $url;
}

?>
