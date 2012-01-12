<?php
/**
* configuration options for daily bread
*/

function _get_config() {

	$db_config = array(
	  'slider_min' => 10000,
	  'slider_max' => 100000,
	  'currency_postfix' => ' Kč',
	  'table_numbers' => array(4,8,12,24),	//required
	  'data' => 'dailybread_data.inc',	//required
	  'max_image_size' => 200,	//required
	  'min_image_size' => 2,  //required
	);
	
return $db_config;
}



?>
