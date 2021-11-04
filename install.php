<?php
/*
	Redaxo-Addon Gridblock
	Installation
	v1.0
	by Falko Müller @ 2021 (based on 0.1.0-dev von bloep)
*/

//Variablen deklarieren
$mypage = $this->getProperty('package');
$error = "";


//Vorgaben vornehmen
if (!$this->hasConfig()):
	$this->setConfig('config', [
		'modulesmode'				=> 'allow',
		'modules'					=> '',
		'previewtabnames'			=> '',
		'showtemplatetitles'		=> '',
		'hidepreviewcoltitles'		=> '',
	]);
endif;



//Datenbank-Einträge vornehmen
rex_sql_table::get(rex::getTable('1620_gridtemplates'))
	->ensureColumn(new rex_sql_column('id', 'int(100)', false, null, 'auto_increment'))
	->ensureColumn(new rex_sql_column('prio', 'int(11)'))
	->ensureColumn(new rex_sql_column('cat', 'int(100)'))
	->ensureColumn(new rex_sql_column('title', 'varchar(255)'))
	->ensureColumn(new rex_sql_column('description', 'text'))
	->ensureColumn(new rex_sql_column('columns', 'int(2)'))
	->ensureColumn(new rex_sql_column('template', 'text'))
	->ensureColumn(new rex_sql_column('preview', 'text'))
	->ensureGlobalColumns()
	->setPrimaryKey('id')
	->ensure();



//Module anlegen
$identifier = '/* GRID_MODULE_IDENTIFIER | DONT REMOVE */';
$db = rex_sql::factory();
$db->setQuery('SELECT id FROM '.rex::getTable('module').' WHERE input LIKE "%/* GRID_MODULE_IDENTIFIER | DONT REMOVE */%" AND output LIKE "%/* GRID_MODULE_IDENTIFIER | DONT REMOVE */%"');

$input 	= rex_file::get($this->getPath('install/input.php'));
$output = rex_file::get($this->getPath('install/output.php'));

$db2 = rex_sql::factory();
$db2->setValue('input', $input);
$db2->setValue('output', $output);
$db2->setTable(rex::getTable('module'));

if ($db->hasNext()):
	$db2->addGlobalUpdateFields();
	$db2->setWhere(['id' => $db->getValue('id')]);
	$db2->update();
else:
	$db2->addGlobalCreateFields();
	$db2->setValue('name', '01 - Gridblock');
	$db2->insert();
endif;
?>