<?php
/**
* generates table for bubleetree
*/
error_reporting(0);

$since = 2004;
$thru = 2010;
setlocale(LC_MONETARY, 'cs_CZ.utf8');

//print_r($_POST);//die();

$data = array();
if (!isset($_POST['idef']) or ($_POST['idef'] == 'undefined')) $idef = '';
else $idef = $_POST['idef'];
$id_ar = explode('-',$idef);
if (count($id_ar > 0)) {
  //try cache
  //if (false) { //disable cache
  if (file_exists('./cache/'.$idef.'.html')) {  //enable cache
  	  $html = file_get_contents('./cache/'.$idef.'.html');
  	  //echo "cache:<br/>".$html;
  	  echo $html;
  } else {

	  //get data
	  for ($year = $since; $year <= $thru; $year ++) {
		  //form url
		  $letter = 97; //~a
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
		  $url = "http://cz.cecyf.megivps.pl/api/json/dataset/0/view/0/issue/{$year}/{$url_bit}".chr($letter).'?fields=hodnota,idef,name';
		  if ($file = file_get_contents($url)) {
			$object = json_decode($file);
			$data[$year] = $object->data;
		  }
	  }
	  //print_r($data);
	  //get parent info
	  if ($idef == '') $parent = 'Státní rozpočet';
	  else {
		$url = "http://cz.cecyf.megivps.pl/api/json/dataset/0/view/0/issue/{$thru}/a/{$idef}?fields=name,idef";
		$object = json_decode(file_get_contents($url));
		$parent = $object->data[0]->name;
	  }
	  
	  //reorder data
	  $rodata = array();
	  $sum = array();
	  $names = array();
	  foreach ($data as $year => $d) {
		foreach ($d as $key => $item) {
			$rodata[$item->idef][$year] = $item->hodnota;
			$names[$item->idef] = $item->name;
			$ids_ar = explode('-',$item->idef);
			$ids[$item->idef] = end($ids_ar);
			if (isset($sum[$year])) $sum[$year] += $item->hodnota;
			else $sum[$year] = $item->hodnota;
			//$sort[$item->idef] = $item->hodnota;
		}
	  }
	  
	  //sort array by thru
	  //print_r($sort);//die();
	  //array_multisort($sort, SORT_DESC, $rodata);
	  uasort($rodata, "custom_sort");
	  //print_r($rodata);//die();
	  	  
	  //get tokens
	  if (isset($_POST['token']) and ($_POST['token'] != '')) {
	    $token = array();
	    $tok = explode('|',$_POST['token']);
	    array_pop($tok);
	    foreach ($tok as $t) {
	      $t_ar = explode(':',$t);
	      $token[$t_ar[0]] = $t_ar[1];
	    }
	  }
	  //print_r($token);
	  
	  //create table
		//thead
	  $table = "<table id='bs-table-table' class='bs-table tablesorter'>\n<thead>\n<th>Jméno</th>";
	  for ($year = $since; $year <= $thru; $year ++) $table .= "<th>{$year}</th>";
	  $table .=  "<th></th>\n";
	  $table .=  "</tr>\n";
		//first row
	  $id = end($id_ar);
	  $table .= "<tr id='{$idef}' class='bs-table-first'><td>" . ($id != '' ? $id . ' - ' : '') . "{$parent}</td>";
	  for ($year = $since; $year <= $thru; $year ++) $table .= "<td>".money_format("%!.0n",$sum[$year])."</td>";
	  $table .=  "<td class='bs-table-small-chart' id='bs-small-chart-{$idef}'>".small_chart($sum,$since,$thru)."</td>\n";
	  $table .=  "</tr>\n";
	  $table .= "</thead>\n";
	  $dialogs = "<div class='bs-big-chart' id='bs-big-chart-{$idef}' title='{$parent}'>".big_chart($sum,$since,$thru)."</div>";

	  $js = 'function bsDialog(){
	  			$( "#bs-big-chart-'.$idef.'" ).dialog({autoOpen:false,modal:true,minWidth:350});
				$("#bs-small-chart-'.$idef.'").click(function() {
					$( "#bs-big-chart-'.$idef.'" ).dialog("open");
			  	});';
		//body
	  $table .= "<tbody>\n";
	  //echo $table;
	  
		//other rows
	  $zebra = 'odd';
	  foreach ($rodata as $key => $row) {
		$table .= "<tr id='{$key}' class='{$zebra}'><td class='bs-table-first'>" . (isset($token[$key]) ? "<a href='#{$_POST['path']}/{$token[$key]}'>" : '') . $ids[$key] . ' - ' . $names[$key] . (isset($token[$key]) ? "</a>" : '') . "</td>";
		for ($year = $since; $year <= $thru; $year ++)
			$table .= (isset($row[$year]) ? "<td>".money_format("%!.0n",$row[$year])."</td>" : "<td></td>");
		$table .= "<td class='bs-table-small-chart' id='bs-small-chart-{$key}'>".small_chart($row,$since,$thru)."</td>";
		$table .= "</tr>\n";
		if ($zebra == 'odd') $zebra = 'even';
		else $zebra = 'odd';
		//dialogs
		$dialogs .= "<div class='bs-big-chart' id='bs-big-chart-{$key}' title='{$names[$key]}'>".big_chart($row,$since,$thru)."</div>";
		$js .= '$( "#bs-big-chart-'.$key.'" ).dialog({autoOpen:false,modal:true});
				$("#bs-small-chart-'.$key.'").click(function() {
					$( "#bs-big-chart-'.$key.'" ).dialog("open");
			  	});';
	  }
	  $js .= '}';
	  $table .= "</tbody>\n</table>\n";
	  //echo $table;
	  
	  //save into cache
	  $file = fopen('./cache/'.$idef.'.html',"w+");
	  $out = "<script type='text/javascript'>".$js.$js2.'</script>'.$table.$dialogs;
	  fwrite($file,$out);
	  fclose($file);
	  
	  echo $out;

  }
} else {
 echo '-';
}
/**
* small bar chart 50x20
*/
function small_chart($row,$since,$thru) {
  //
  //print_r($row);die();
  $width = 50;
  $height = 20;
  $bar_width = floor($width/($thru-$since+1))-1;
  $url = "https://chart.googleapis.com/chart?cht=bvs&chbh={$bar_width},1,1&chs={$width}x{$height}&chds=0,";
  $max = 0;
  for ($year = $since; $year <= $thru; $year ++) {
    if ($row[$year] > $max) $max = $row[$year];
    if (!isset($row[$year])) $row[$year] = 0;
    $row[$year] = number_format($row[$year],0,'.',''); //see jetlogs.org/2008/02/05/php-problems-with-big-integers-and-scientific-notation/
  }
  ksort($row);
  $max = round(1.1*$max);
  $url .= number_format($max,0,'.','') . "&chd=t:" . implode(',',$row);
  return "<img src='{$url}' alt='chart' width='{$width}' height='{$height}'/>";
}

/**
* big bar chart 300x150
*/
function big_chart($row,$since,$thru) {
  //
  $width = 280;
  $height = 150;
  $bar_width = floor(($width-50)/($thru-$since+1))-1;
  $url = "https://chart.googleapis.com/chart?chxt=x,y&cht=bvs&chbh={$bar_width},1,1&chs={$width}x{$height}&chds=0,";
  $chxl = '0:|';
  $years = array();
  $max = 0;
  for ($year = $since; $year <= $thru; $year ++) {
    if ($row[$year] > $max) $max = $row[$year];
    if (!isset($row[$year])) $row[$year] = 0;
    $years[] = $year;
    $row[$year] = number_format($row[$year],0,'.',''); //see jetlogs.org/2008/02/05/php-problems-with-big-integers-and-scientific-notation/
  }
  ksort($row);
  $max = round(1.1*$max);
  $chxl1 = "|1:|0|" . n($max/2) . "|" . n($max);
  $url .= number_format($max,0,'.','') . "&chd=t:" . implode(',',$row) . "&chxl=0:|" . implode('|',$years) . $chxl1 . "&chxr=0,0," . number_format($max,0,'.','');
  return "<img src='{$url}' alt='chart' width='{$width}' height='{$height}'/>";
}
//chxt=x,y
//chxl=0:|Jan|Feb|Mar|Apr|May

/*function big_chart2($row,$since,$thru,$div,$name) {
   $out ='var data_'.str_replace('-','_',$div).' = new google.visualization.DataTable();
        data.addColumn("string", "Rok");
        data.addColumn("number", "Výdaje");
        data.addRows([';
   $chunk = '';
   for ($year = $since; $year <= $thru; $year ++) {
   		if (!isset($row[$year])) $row[$year] = 0;
   		$chunk .= "['{$year}', {$row[$year]}],\n";
   }
   $out .= rtrim(trim($chunk),',');
   $out .= "
    	]);
    	
    	var options_".str_replace('-','_',$div)." = {
          width: 400, height: 240,
          title: '{$name}',
          hAxis: {title: 'Rok', titleTextStyle: {color: 'red'}}
          vAxis: {baseline : 0}
        };
 		var chart_".str_replace('-','_',$div)." = new google.visualization.ColumnChart(document.getElementById('chart_div_".str_replace('-','_',$div)."'));
        chart_".str_replace('-','_',$div).".draw(data_".str_replace('-','_',$div).", options_".str_replace('-','_',$div).");
	";
	return $out;
}*/
/**
*
*/
function n($number) {
  if ($number > 1000000000000) return round($number/1000000000000,1) . 'bil';
  if ($number > 1000000000) return round($number/1000000000,1) . 'mld';
  if ($number > 1000000) return round($number/1000000,1) . 'mil';
  if ($number > 1000) return round($number/1000,1) . 'tis';
  return $number;
}
/**
* http://php.net/manual/en/function.array-multisort.php
*/
function custom_sort($a,$b) {
          return $a['2010']<$b['2010'];
     }
?>
