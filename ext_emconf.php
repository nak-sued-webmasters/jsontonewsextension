<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "news_ttnewsimport".
 *
 * Auto generated 24-06-2015 22:47
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array (
	'title' => 'json importer',
	'description' => 'Importer of JSON items to ext:news',
	'category' => 'be',
	'version' => '0.0.1',
	'state' => 'alpha',
	'uploadfolder' => false,
	'createDirs' => '',
	'clearcacheonload' => true,
	'author' => 'David Schäfer',
	'author_email' => 'schaeferdavid@web.de',
	'author_company' => NULL,
	'constraints' => 
	array (
		'depends' => 
		array (
			'typo3' => '6.2.4-7.99.99',
			'php' => '5.3.0-0.0.0',
			'news' => '3.0.0',
		),
		'conflicts' => 
		array (
		),
		'suggests' => 
		array (
		),
	),
);

