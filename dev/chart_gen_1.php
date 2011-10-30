<?php

const API_DIR = '/home/shared/api.kohovolit.eu';
require 'ApiDirect.php';
error_reporting(E_ALL);
set_time_limit(0);
$ac = new ApiDirect('bs');

require '/home/michal/api.kohovolit.eu/classes/Query.php';

$query = new ('bs_user');

$query->execute('select count(*) from value');


?>
