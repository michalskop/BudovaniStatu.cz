<?php
/**
* configuration options for daily bread
*/

function _get_config() {

	$db_config = array(
	  'slider_min' => 10000,
	  'slider_max' => 100000,
	  'slider_step' => 1000,
	  'currency_postfix' => ' Kč',
	  'table_numbers' => array(6,6,6),	//required
	  'table_rest_number' => 3,
	  //'div_numbers' => 18, //required
	  'data' => 'dailybread_data.inc',	//required
	  'max_image_size' => 200,	//required
	  'min_image_size' => 2,  //required
	);
	
return $db_config;
}



?>
