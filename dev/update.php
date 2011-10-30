<?php

const API_DIR = '/home/shared/api.kohovolit.eu';
require 'ApiDirect.php';
error_reporting(E_ALL);
set_time_limit(0);
$ac = new ApiDirect('bs');echo '*';
$params = array(
  //'update_organizations' => true;
  //'update_regions' => true,
  //'update_chapters' => true,
  'update_memberships' => true,
);
echo '<br /> bs: ' . $ac->update('Updater_temp',$params) . 'all';
/*try
{ 
$ac = new ApiDirect('bs');echo '1 ';
echo '<br /> bs: ' . $ac->update('Updater',array());
}
catch (Exception $e)
{
	echo 'ERROR: ' . $e->getMessage();
}*/
?>
