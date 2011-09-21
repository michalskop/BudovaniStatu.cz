<?php
 //generates json file from array for openspending specification
 // http://wiki.openspending.org/Model_Format
 // http://wiki.openspending.org/Mapping_Format
 $data = array(
   'dataset' => array (
     'name' => 'aris-trial',
     'label' => 'ARIS trial',
     'description' => 'My ARIS trial',
     'currency' => 'CZK',
     'unique_keys' => array('Entry','Paragraph','Chapter','Year'),
     'temporal_granularity' => 'year',
     
   ),
   'mapping' => array (
     'from' => array (
       'fields' => array (
         'column' => 'Chapter',
         'datatype' => 'string',
         'default_value' => '',
         'constant' => '',
         'name' => 'Chapter',
       ),
       'type' => 'entity',
       'description' => 'Chapter',
       'label' => 'Chapter',
     ),
     'to' => array (
       'fields' => array (
         'column' => 'To',
         'datatype' => 'string',
         'default_value' => '',
         'constant' => '',
         'name' => 'To',
       ),
       'type' => 'entity',
       'description' => 'Public',
       'label' => 'Public',
     ),
     'date' => array (
       'default_value' => '',
       'description' => 'Year',
       'column' => 'Year',
       'label' => 'Year',
       'datatype' => 'date',
       'type' => 'value',
     ),
     'amount' => array (
       'default_value' => '',
       'description' => 'Amount',
       'column' => 'Value',
       'datatype' => 'float',
       'type' => 'value',
     ),
     'entry' => array(
       'default_value' => '',
       'description' => 'Entry',
       'column' => 'Entry',
       'datatype' => 'string',
       'type' => 'classifier',     
     ),
     'paragraph' => array(
       'default_value' => '',
       'description' => 'Paragraph',
       'column' => 'Paragraph',
       'datatype' => 'string',
       'type' => 'classifier', 
     ),
   ),
 );
 
 $file = fopen ('mapping.json',"w+");
 $json = json_encode($data);
 fwrite($file,$json);
 fclose($file);
 echo $json;
?>
