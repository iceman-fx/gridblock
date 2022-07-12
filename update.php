<?php
/*
	Redaxo-Addon Gridblock
	Updateprozeduren
	v1.0
	by Falko Müller @ 2021 (based on 0.1.0-dev von bloep)
*/

//Variablen deklarieren
$mypage = $this->getProperty('package');
$error = "";


//Datenbank-Spalten anlegen, sofern noch nicht verfügbar
rex_sql_table::get(rex::getTable('1620_gridtemplates'))
	->ensureColumn(new rex_sql_column('status', 'varchar(10)'))
	->alter();

// save gridblock module ID to config
$db = rex_sql::factory();
$db->setQuery('SELECT id FROM ' . rex::getTable('module') . ' WHERE input LIKE "%/* GRID_MODULE_IDENTIFIER | DONT REMOVE */%"');
if ($db->getRows()) {
	$aConfig = $this->getConfig('config');
	$aConfig['gridblockmoduleid'] = $db->getValue("id");
	$this->setConfig('config', $aConfig);
}
