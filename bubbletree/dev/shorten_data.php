<?php
$file = fopen('/home/michal/budovanistatu.cz/bubbletree/dev/datab9.json',"w+");
$source = file_get_contents('/home/michal/budovanistatu.cz/bubbletree/dev/datab8.json');
$out = str_replace(array('idef','amount','label','children'),array('i','a','l','c'),$source);
echo strlen($source) . "::" . strlen($out);
fwrite($file,$out);
fclose($file);
?>
