<?php
namespace NakSued\NewsJsonimport\Command;

	/*
	 * This file is part of the TYPO3 CMS project.
	 *
	 * It is free software; you can redistribute it and/or modify it under
	 * the terms of the GNU General Public License, either version 2
	 * of the License, or any later version.
	 *
	 * For the full copyright and license information, please read the
	 * LICENSE.txt file that was distributed with this source code.
	 *
	 * The TYPO3 project - inspiring people to share!
	 */

/**
 * A Command Controller which provides help for available commands
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class JsonImportCommandController extends \TYPO3\CMS\Extbase\Mvc\Controller\CommandController {

	// TODO Filename anpassen
	protected $importFile = PATH_site.'fileadmin/article/article_01.json';
	
	protected $storageUid = 1;
	protected $importFolder = 'news_import/';

	/**
	 * import JSON File
	 */
	public function importJSONCommand() {
		print('Start import JSON File');
		$migrate = $this->objectManager->get('NakSued\\NewsJsonimport\\Jobs\\JSONNewsImportJob');
		$migrate->setImportFile($this->importFile);
		$fo = $migrate->run(0);
		$this->outputLine($fo);	
	}
	
	/**
	 * moves picture to district files
	 */
	public function movePicturesCommand(){
		
		print("Move File to final destination\n");
		

		/** @var $storageRepository \TYPO3\CMS\Core\Ressources\StorageRepository */
		$storageRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
			'TYPO3\\CMS\\Core\\Resource\\StorageRepository'
		);
		$storage = $storageRepository->findByUid($this->storageUid);
		
		$filecontent 			= file_get_contents($this->importFile);
		$content 				= json_decode($filecontent, true);
		$resourceDestination 	= $content["resourceDestination"];
		print($resourceDestination);
		$folder = $storage->getFolder($this->importFolder);
		$files 	= $folder->getFiles();
		
		$fileArray = array();
		foreach ($files as $file) {
			$fileArray[] = strtolower($file->getName());
		}
		
		$numberOfArticles = (int)count($content["articles"]);
		
		for ($i = 0; $i < $numberOfArticles; $i++) {
			
			$images = $content["articles"][$i]["media"];
			
			foreach ($images as $image) {
			
				if (in_array(strtolower($image), $fileArray)) {
					print ("File will be moved ".$image."\n");
					
					$someFileIdentifier = $this->importFolder.$image;	
					$file = $storage->getFile($someFileIdentifier);
					
					$folder = $storage->getFolder($resourceDestination);
					$file->moveTo($folder);					
				} else {
					print ("File will be NOT moved ".$image."\n");
				}
			}
		}
		

	}
	
	public function checkImportFolderCommand(){
		/** @var $storageRepository \TYPO3\CMS\Core\Ressources\StorageRepository */
		$storageRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
			'TYPO3\\CMS\\Core\\Resource\\StorageRepository'
		);

		$storage = $storageRepository->findByUid($this->storageUid);
		// $file returns a TYPO3\CMS\Core\Resource\File object
		$folder = $storage->getFolder($this->importFolder);
		$files = $storage->getFilesInFolder($folder);
		foreach ($files as $file) {
			print($file->getName());
		}
	}	
}
