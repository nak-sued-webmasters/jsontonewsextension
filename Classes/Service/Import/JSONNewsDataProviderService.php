<?php
namespace NakSued\NewsJsonimport\Service\Import;

/***************************************************************
*  Copyright notice
*
*  (c) 2011 Nikolas Hagelstein <nikolas.hagelstein@gmail.com>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

use \TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * tt_news ImportService
 *
 * @package TYPO3
 * @subpackage news_ttnewsimport
 */
class JSONNewsDataProviderService implements \Tx_News_Service_Import_DataProviderServiceInterface, \TYPO3\CMS\Core\SingletonInterface  {


	// Filename of JSON File
	private $importFile;
	
    public function setImportFile($importFile) { 
        $this->importFile = $importFile; 
    }
	
    public function getImportFile() { 
        return $this->importFile; 
    }
	
	/**
	 * Get total record count
	 *
	 * @return integer
	 */
	public function getTotalRecordCount() {
		$filecontent = file_get_contents($this->importFile);
		$content = json_decode($filecontent, true);
		return (int)count($content["articles"]);
	}

	/**
	 * Get the partial import data, based on offset + limit
	 *
	 * @param integer $offset offset
	 * @param integer $limit limit
	 * @return array
	 */
	public function getImportData($offset = 0, $limit = 50) {

		// TODO offset implementieren oder übprüfen, ob dieser notwendig ist
		$importData = array();
		$filecontent = file_get_contents($this->importFile);
		$content = json_decode($filecontent, true);
		
		
		// TODO mapping Table
		$destinationpage = 0;
		switch ($content["destrict"]) {
			case "Heidelberg":
				$destinationpage = 37;
				break;
		}		
		
		$numberOfArticles = (int)count($content["articles"]);
		
		for ($i = 0; $i < $numberOfArticles; $i++) {
			$importData[] = array(
				'pid' => 				$destinationpage,
				'hidden' => 			$content["articles"][$i]["hidden"],
				'tstamp' => 			$this->getUnixTimestamp($content["articles"][$i]["tstamp"]),
				'crdate' => 			$this->getUnixTimestamp($content["articles"][$i]["crdate"]),
				'cruser_id' => 			$content["articles"][$i]["cruser_id"],
				'l10n_parent' => 		$content["articles"][$i]["l10n_parent"],
				'sys_language_uid' => 	$content["articles"][$i]["sys_language_uid"],
				'starttime' => 			$this->getUnixTimestamp($content["articles"][$i]["starttime"]),
				'endtime'  => 			$content["articles"][$i]["endtime"],
				'title' => 				$content["articles"][$i]["title"],
				'teaser' => 			$content["articles"][$i]["teaser"],
				'bodytext' => 			$content["articles"][$i]["bodytext"],
				'datetime' => 			$this->getUnixTimestamp($content["articles"][$i]["datetime"]),
				'archive' => 			$this->getUnixTimestamp($content["articles"][$i]["archive"]),
				'author' => 			$content["articles"][$i]["author"],
				'author_email' => 		$content["articles"][$i]["author_email"],
				'type' => 				$content["articles"][$i]["type"],
				'keywords' => 			$content["articles"][$i]["keywords"],
				'externalurl' => 		$content["articles"][$i]["externalurl"],
				'internalurl' => 		$content["articles"][$i]["internalurl"],
				'categories' => 		$this->getCategories($content["articles"][$i]["categories"]),
				'media' => 				$this->getMedia($content["articles"][$i]["media"],$content["articles"][$i]["media_descripton"]),
				'related_files' => 		$content["articles"][$i]["related_files"],
				'related_links' => 		$content["articles"][$i]["related_links"],
				'import_id' => 			$content["articles"][$i]["import_id"],
				'import_source' => 		$content["articles"][$i]["import_source"]
			);
		}
		return $importData;
	}
	
	protected function getUnixTimestamp($dateString) {
		$timestamp = strtotime($dateString);
		return $timestamp;
	}
	
	/**
	 * Get correct categories from string
	 *
	 * @param integer $newsUid news uid
	 * @return array
	 */
	protected function getCategories($categories) {
	
		$categoryids = array();
		// TODO Robustheit bei mehreren Kategorien - behandlung von , und " die nicht gewollt sind
		// TODO Source Page
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',
			'sys_category',
			'title IN ("'.implode('","', $categories).'")');
			
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$categoryids[] = $row['uid'];
		}
		
		$GLOBALS['TYPO3_DB']->sql_free_result($res);
		return $categoryids;
	}
	

	/**
	 * Get correct media elements to be imported
	 *
	 * @param array $row news record
	 * @return array
	 */
	protected function getMedia($images, $descriptions) {
		$media = array();
		
		// TODO check dass beide Arrays gleich groß sind
		
		$i = 0;		
		foreach ($images as $image) {
			$media[] = array(
				'title' => $descriptions[$i],
				'alt' => $descriptions[$i],
				'caption' => $descriptions[$i],
				'image' => 'fileadmin/news_import/' . $image,
				'type' => 0,
				'showinpreview' => (int)$count == 0
			);
			$i ++;
		}
		
		return $media;
	}	
}