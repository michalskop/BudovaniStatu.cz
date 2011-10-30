<?php

/**
* This class updates/inserts data in the database for budovanistatu.cz
*/
class Updater_temp {
	/// API client reference used for all API calls
	private $api;

	/// time and time zone used when storing dates into 'timestamp with time zone' fields
	//const TIME_ZONE = 'Europe/Prague';
	//const NOON = ' 12:00 Europe/Prague';
	
	/**
	 * Creates API client reference to use during the whole update process.
	 */
	public function __construct()
	{
		$this->api = new ApiDirect('bs');
		$this->log = new Log(API_LOGS_DIR . '/update/bs/' . strftime('%Y-%m-%d %H-%M-%S') . '.log', 'w');
		$this->log->setMinLogLevel(Log::DEBUG);
	}
	/**
	 * Main method called by API resource Updater - it scrapes data and updates the database.
	 *
	 */
	public function update($params)	{
		$this->log->write('Started with parameters: ' . print_r($params, true));
		
		$lines = file ('/home/michal/aris/ufis50.csv');
		$header = explode(',',array_shift($lines));
		array_pop($header);
		//$this->log->write('Started with parameters: ' . print_r($header, true));die();

		foreach ($lines as $line) {
		  $ar = explode(",",$line);
		  $value_pkey = $this->api->create('Value',array('value' => $ar[6]));
		  $value_id = $value_pkey['id'];
		  
		  
		  foreach ($header as $k => $h) {
		    //label exists?
		    $label_db = $this->api->readOne('Label',array('label_kind_code' => $h, 'name' => $ar[$k]));
		    if ($label_db) 
		      $label_id = $label_db['id'];
		    else {
		      $label_pkey = $this->api->create('Label',array('label_kind_code' => $h, 'name' => $ar[$k]));
		      $label_id = $label_pkey['id'];
		    }
		    $this->api->create('LabelForValue',array('label_id' => $label_id, 'value_id' => $value_id));
		  }
		}
		
		$this->log->write('i: ' . $i);
	}
	
}
