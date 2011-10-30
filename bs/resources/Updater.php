<?php

/**
* This class updates/inserts data in the database for budovanistatu.cz
*/
class Updater {
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
		
		$this->update_date = 'now';
		
		//update organizations
		if (isset($params['update_organizations']))
		  $this->updateCentralOrganizations();
		
		//update regions
		if (isset($params['update_regions']))
		  $this->updateRegions();
		
		//update chapters
		if (isset($params['update_chapters']))
		  $this->updateChapters();
		
		//update memberships
		if (isset($params['update_memberships']))
		  $this->updateOrganizationInGroup();
	}
	
	/**
	* update/insert chapters
	*/
	private function updateChapters() {
	  $src_chapters = $this->api->read('Scraper',array('remote_resource' => 'chapter_list'));
	  
	  //find keys
	  $keys = $src_chapters['chapter']->keys;
	  $key = array(
	    'id' => array_search('id',$keys),
	    'name' => array_search('name',$keys),
	  );
	  
	  foreach ($src_chapters['chapter']->data as $src_chapter) {
	    // if region does not exist yet, insert it, otherwise update
	    $chapter = $this->api->readOne('Group', array('name' => $src_chapter[$key['name']], 'group_kind_code' => 'chapter'));
	    
	    if (!$chapter) { //insert
	      $this->log->write('Inserting new group/chapter: ' . print_r($src_chapter[$key['name']], true));
		  $this->api->create('Group',array(
		    'name' => $src_chapter[$key['name']],
		    'short_name' =>  $src_chapter[$key['id']],
		    'group_kind_code' => 'chapter',
		    'last_updated_on' => 'now',
		  ));
		} else { //update
		  $this->log->write('Updating group/chapter: ' . print_r($src_chapter[$key['name']], true));
		  
		  $this->api->update('Group', 
		    array(
		  	  'name' => $src_chapter[$key['name']],
		  	  'group_kind_code' => 'chapter',
		    ),
		    array(
		      'short_name' => $src_chapter[$key['id']],
		      'last_updated_on' => 'now',
		    ));
	    }	    
	  }
	  
	}
	
	/**
	* update/insert regions
	*/
	private function updateRegions() {
	  // 1) kraj
	  $src_regions = $this->api->read('Scraper',array('remote_resource' => 'region_list','type'=>'kraj'));
	  //find keys
	  $keys = $src_regions['region']->keys;
	  $key = array(
	    'code' => array_search('code',$keys),
	    'name' => array_search('name',$keys),
	  );
	  //find id of 'Czech republic'
	  $czechia = $this->api->readOne('Group', array('name'=>'Czech Republic', 'group_kind_code' => 'region'));
	  
	  foreach ($src_regions['region']->data as $src_region) {
	    // if region does not exist yet, insert it, otherwise update
	    $region = $this->api->readOne('Group', array('name' => $src_region[$key['name']], 'group_kind_code' => 'region'));
	    
	    if (!$region) { //insert
	      $this->log->write('Inserting new group/region: ' . print_r($src_region[$key['name']], true));
		  $group_pkey = $this->api->create('Group',array(
		    'name' => $src_region[$key['name']],
		    'short_name' => trim(str_replace('kraj','',str_replace('Kraj','',str_replace('Hlavní město','',$src_region[$key['name']])))),
		    'group_kind_code' => 'region',
		    'subgroup_of' => $czechia['id'],
		    'last_updated_on' => 'now',
		  ));
		  $group_id = $group_pkey['id'];
		} else { //update
		  $this->log->write('Updating group/region: ' . print_r($src_region[$key['name']], true));
		  
		  $group_pkey = $this->api->update('Group', 
		    array(
		  	  'name' => $src_region[$key['name']],
		  	  'group_kind_code' => 'region',
		    ),
		    array(
		      'short_name' => trim(str_replace('kraj','',str_replace('Kraj','',str_replace('Hlavní město','',$src_region[$key['name']])))),
		      'subgroup_of' => $czechia['id'],
		      'last_updated_on' => 'now',
		    ));
		    $group_id = $group_pkey[0]['id'];
	    }
	    
	    //attributes
	    $src_group = array('czso_code' => $src_region[$key['code']]);
	    self::updateGroupAttribute($src_group, $group_id, 'czso_code');   
	  }
	  
	  // 2) okres
	  $src_regions = $this->api->read('Scraper',array('remote_resource' => 'region_list','type'=>'okres'));
	  //find keys
	  $keys = $src_regions['region']->keys;
	  $key = array(
	    'code' => array_search('code',$keys),
	    'name' => array_search('name',$keys),
	    'sup_code' => array_search('sup_code',$keys),
	    'sup_name' => array_search('sup_name',$keys),
	  );
	  foreach ($src_regions['region']->data as $src_region) {
		//get sup region
		$sup_region = $this->api->readOne('Group', array('name' => $src_region[$key['sup_name']], 'group_kind_code' => 'region'));

	    // if region does not exist yet, insert it, otherwise update
	    $region = $this->api->readOne('Group', array('name' => $src_region[$key['name']], 'group_kind_code' => 'region'));
	    
	    if (!$region) { //insert
	      $this->log->write('Inserting new group/region: ' . print_r($src_region[$key['name']], true));
		  $group_pkey = $this->api->create('Group',array(
		    'name' => $src_region[$key['name']],
		    'short_name' => trim(str_replace('kraj','',str_replace('Kraj','',str_replace('Hlavní město','',$src_region[$key['name']])))),
		    'group_kind_code' => 'region',
		    'subgroup_of' => $sup_region['id'],
		    'last_updated_on' => 'now',
		  ));
		  $group_id = $group_pkey['id'];
		} else { //update
		  $this->log->write('Updating group/region: ' . print_r($src_region[$key['name']], true));
		  
		  $group_pkey = $this->api->update('Group', 
		    array(
		  	  'name' => $src_region[$key['name']],
		  	  'group_kind_code' => 'region',
		    ),
		    array(
		      'short_name' => $src_region[$key['name']],
		      'subgroup_of' => $sup_region['id'],
		      'last_updated_on' => 'now',
		    ));
		    $group_id = $group_pkey[0]['id'];
	    }
	    
	    //attributes
	    $src_group = array('czso_code' => $src_region[$key['code']]);
	    self::updateGroupAttribute($src_group, $group_id, 'czso_code');	    
	  }
	}
	
		
	/**
	* update/insert central organizations
	*/
	private function updateCentralOrganizations() {
	  $src_organizations = $this->api->read('Scraper',array('remote_resource' => 'central_organization_list'));
	  //find keys
	  $keys = $src_organizations['central_organization']->keys;
	  $key['name'] = array_search('Nezkrácený název organizace dle ČSÚ',$keys);
	  $key['short_name'] = array_search('NAO',$keys);
	  $key['disambiguation'] = array_search('org_id',$keys);
	  
	  foreach ( $src_organizations['central_organization']->data as $src_organization) {
	    //skip empty organizations
	    if (trim($src_organization[$key['name']] == '')) continue;
	    
	    // if organization does not exist yet, insert it	    
		$organization = $this->api->readOne('Organization', array('name' => $src_organization[$key['name']], 'disambiguation' => intval($src_organization[$key['disambiguation']])));
		
		if (!$organization) { //insert new organization
		  $this->log->write('Inserting new organization: ' . print_r($src_organization[$key['name']], true));
		  $organization_pkey = $this->api->create('Organization',array(
		    'name' => $src_organization[$key['name']],
		    'short_name' => $src_organization[$key['short_name']],
		    'disambiguation' => intval($src_organization[$key['disambiguation']]),
		    'last_updated_on' => 'now',
		  ));
		  $organization_id = $organization_pkey['id'];
		  
		} else { //update organization
		  $this->log->write('Updating organization: ' . print_r($src_organization[$key['name']], true));
		  $organization_pkey = $this->api->update('Organization', array('name' => $src_organization[$key['name']],'disambiguation' => intval($src_organization[$key['disambiguation']])), array('short_name' => $src_organization[$key['short_name']], 'disambiguation' => intval($src_organization[$key['disambiguation']]),'last_updated_on' => 'now'));
		  $organization_id = $organization_pkey['id'];
		}
		
		
	  }
	}
	
	/**
	* updates memberships: organizations in groups (region, chapter)
	*/
	private function updateOrganizationInGroup() {
	  $src_organizations = $this->api->read('Scraper',array('remote_resource' => 'central_organization_list'));
	  //find keys
	  $keys = $src_organizations['central_organization']->keys;
	  $key['czso_code'] = array_search('NUTS',$keys);
	  $key['name'] = array_search('Nezkrácený název organizace dle ČSÚ',$keys);
	  $key['disambiguation'] = array_search('org_id',$keys);
	  $key['chapter'] = array_search('chapter',$keys);
	  
	  //insert or update membership
	  foreach ( $src_organizations['central_organization']->data as $src_organization) {
	    //skip empty organizations
	    if (trim($src_organization[$key['name']] == '')) continue;
	    
	    $organization = $this->api->readOne('Organization',array('name' => $src_organization[$key['name']], 'disambiguation' => intval($src_organization[$key['disambiguation']])));
	    
	    //region:
	    $group_attr = $this->api->readOne('GroupAttribute',array('name' => 'czso_code','value' => $src_organization[$key['czso_code']], '_datetime' => $this->update_date));
	    
	    $data = array(
		  'group_id' => $group_attr['group_id'],
		  'organization_id' => $organization['id'],
		  'role_code' => 'member',
	    );
	    self::updateMembership($data);
	    
	    //chapter:
	    $group = $this->api->readOne('Group',array('short_name' => $src_organization[$key['chapter']],'group_kind_code' => 'chapter'));
	    $this->log->write('XX: ' . print_r($src_organization, true));
	    $data = array(
		  'group_id' => $group['id'],
		  'organization_id' => $organization['id'],
		  'role_code' => 'member',
	    );
	    self::updateMembership($data);
	  }  
	}
	
	
	/**
	* updates membership in group
	*
	* if since/until is not set, set it to (-)infinity
	*
	* @param data array of membership values
	*/
	private function updateMembership($data)
	{

		if (!empty($data['since']) && $data['since'] != '-infinity')
			$data['since'] .= self::NOON;
		if (!empty($data['until']) && $data['until'] != 'infinity')
			$data['until'] .= self::NOON;

		// if no dates given, set (-)infinity
		if (empty($data['since'])) {
			$data['since'] = '-infinity';
		}
		if (empty($data['until'])) {
			$data['until'] = 'infinity';
		}

		// if the membership exists today, update it
		// if not -> if the membership exists with equal 'since', update it, otherwise insert it
		// (should catch some changes in 'since')
		$membership = $this->api->read('OrganizationInGroup', array('organization_id' => $data['organization_id'], 'group_id' => $data['group_id'], 'role_code' => $data['role_code'], '_datetime' => $this->update_date));
		if ($membership) {
			// update
			$this->api->update('OrganizationInGroup', array('organization_id' => $data['organization_id'], 'group_id' => $data['group_id'], 'role_code' => $data['role_code'], '_datetime' => $this->update_date), $data);
		} else {
			$membership = $this->api->read('OrganizationInGroup', array('organization_id' => $data['organization_id'], 'group_id' => $data['group_id'], 'role_code' => $data['role_code'], 'since' => $data['since']));
			if ($membership) {
				// update
				$this->api->update('OrganizationInGroup', array('organization_id' => $data['organization_id'], 'group_id' => $data['group_id'], 'role_code' => $data['role_code'], 'since' =>$data['since']), $data);
			} else {
				// insert
				$this->api->create('OrganizationInGroup', $data);
				$this->log->write("Inserting new membership (organization_id='{$data['organization_id']}', group_id='{$data['group_id']}')", Log::DEBUG);
			}
		}
	}

	/**
	 * Update value of an attribute of an organization. If its value has changed, close the current record and insert a new one.
	 *
	 * \param $src_organization array of key => value pairs with properties of a scraped organization
	 * \param $organization_id \e id of that organization in database
	 * \param $attr_name name of the attribute
	 * \param $iorganizationlode_separator in case that <em>$src_organization[$attr_name]</em> is an array, use this parameter to set a string used for implosion of the array to a string value.
	 */
	private function updateOrganizationAttribute($src_organization, $organization_id, $attr_name, $implode_separator = null)
	{
		$this->log->write("Updating organization's attribute '$attr_name'.", Log::DEBUG);

		$src_value = !empty($src_organization[$attr_name]) ? (is_null($implode_separator) ? $src_organization[$attr_name] : implode($implode_separator, $src_organization[$attr_name])) : null;
		$value_in_db = $this->api->readOne('OrganizationAttribute', array('organization_id' => $organization_id, 'name' => $attr_name, '_datetime' => $this->update_date));
		if ($value_in_db)
			$db_value = $value_in_db['value'];

		if (!isset($src_value) && !isset($db_value) || isset($src_value) && isset($db_value) && $src_value == $db_value) return;

		// close the current record
		if (isset($db_value))
			$this->api->update('OrganizationAttribute', array('organization_id' => $organization_id, 'name' => $attr_name, 'since' =>  $value_in_db['since']), array('until' => $this->update_date));

		// and insert a new one
		if (isset($src_value))
			$this->api->create('OrganizationAttribute', array('organization_id' => $organization_id, 'name' => $attr_name, 'value' => $src_value, 'since' => $this->update_date, 'until' => 'infinity'));
	}
	
	/**
	 * Update value of an attribute of a group. If its value has changed, close the current record and insert a new one.
	 *
	 * \param $src_group array of key => value pairs with properties of a scraped group
	 * \param $group_id \e id of that group in database
	 * \param $attr_name name of the attribute
	 * \param $implode_separator in case that <em>$src_group[$attr_name]</em> is an array, use this parameter to set a string used for implosion of the array to a string value.
	 */
	private function updateGroupAttribute($src_group, $group_id, $attr_name, $implode_separator = null)
	{
		$this->log->write("Updating group's attribute '$attr_name'.", Log::DEBUG);

		$src_value = !empty($src_group[$attr_name]) ? (is_null($implode_separator) ? $src_group[$attr_name] : implode($implode_separator, $src_group[$attr_name])) : null;
		$value_in_db = $this->api->readOne('GroupAttribute', array('group_id' => $group_id, 'name' => $attr_name, '_datetime' => $this->update_date));
		if ($value_in_db)
			$db_value = $value_in_db['value'];

		if (!isset($src_value) && !isset($db_value) || isset($src_value) && isset($db_value) && $src_value == $db_value) return;

		// close the current record
		if (isset($db_value))
			$this->api->update('GroupAttribute', array('group_id' => $group_id, 'name' => $attr_name, 'since' =>  $value_in_db['since']), array('until' => $this->update_date));

		// and insert a new one
		if (isset($src_value))
			$this->api->create('GroupAttribute', array('group_id' => $group_id, 'name' => $attr_name, 'value' => $src_value, 'since' => $this->update_date, 'until' => 'infinity'));
	}


}
?>
