<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\Tx_News_Utility_ImportJob::register(
	'BeechIt\\NewsTtnewsimport\\Jobs\\JSONNewsImportJob',
	'LLL:EXT:news_ttnewsimport/Resources/Private/Language/locallang_be.xml:json_importer_title',
	'');	
