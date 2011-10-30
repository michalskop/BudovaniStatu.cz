<?php

/**
 * \file Scraper.php for budovanistatu.cz
 *
 * Scrapes usually the data from scraperwiki.com
 */
 
/**
* class Scraper
*/
 class Scraper {
   /**
   * Downloads and parses data from the scraperwiki.com
   * 
   * \param $params array of parameters
   * \return scrape($params)
   */
   public function read($params) {
     return self::scrape($params);
   }

   /**
   * Main function of the scraper
   * 
   * \param $params array of parameters
   * - remote_resource: account or account_list
   * - format: output format (raw, html, php, json, xml)
   *
   * all other parameters are described in scrapeAccount($params)
   * \return scraped data
   */
   public function scrape($params) {
     $remote_resource = $params['remote_resource'];
     switch ($remote_resource) {
		case 'central_organization_list': return self::scrapeCentralOrganizationList();
		case 'chapter_list': return self::scrapeChapterList();
		case 'region_list': return self::scrapeRegionList($params);
		default:
			throw new Exception("Scraping of the remote resource <em>$remote_resource</em> is not implemented.", 400);
	 }
   }

   /**
   * Scrapes list of budget's chapters
   *
   * \return array of objects of budget's chapters
   */
   public function scrapeChapterList() {
      $json = ScraperUtils::grabber("https://api.scraperwiki.com/api/1.0/datastore/sqlite?format=jsonlist&name=cz_budget_chapters&query=select%20*%20from%20swdata");
	  $array = json_decode($json);
	  return array('chapter' => $array);
   }

   /**
   * Scrapes list of regions
   *
   * \param type type (level) of regions ('kraj' or 'okres')
   * \return array of objects of regions
   */
   public function scrapeRegionList($params) {
      $json = ScraperUtils::grabber("https://api.scraperwiki.com/api/1.0/datastore/sqlite?format=jsonlist&name=cz_regions_list&query=select%20*%20from%20".$params['type']);
	  $array = json_decode($json);
	  return array('region' => $array);
   }
   
   /**
   * Scrapes list of central organizations
   *
   * \return array of objects of central organizations
   */
   public function scrapeCentralOrganizationList() {
      $json = ScraperUtils::grabber("https://api.scraperwiki.com/api/1.0/datastore/sqlite?format=jsonlist&name=cz_public_organizations_2_details&query=select%20*%20from%20swdata");
	  $array = json_decode($json);
	  return array('central_organization' => $array);
   }
   
   
}
?>  
