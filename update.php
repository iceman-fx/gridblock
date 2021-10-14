<?php
/*
	Redaxo-Addon Gridblock
	Update Installation
	v0.8
	by Falko Müller @ 2021 (based on 0.1.0-dev von bloep)
*/

//Variablen deklarieren
$mypage = $this->getProperty('package');
$error = "";


//Vorgaben vornehmen
/*
$config = $this->getConfig('config');
$config = array_merge($config, [
		'newitem'						=> 'value',
	]);

if (!$this->hasConfig('newitem')):
	$this->setConfig('newitem', 'value');
endif;
*/



//Datenbank-Einträge vornehmen
/*
rex_sql_table::get(rex::getTable('1620_gridtemplates'))
	->ensureColumn(new rex_sql_column('id', 'int(100)', false, null, 'auto_increment'))
	->ensureColumn(new rex_sql_column('title', 'varchar(255)'))
	->ensureColumn(new rex_sql_column('description', 'text'))
	->ensureColumn(new rex_sql_column('columns', 'int(2)'))
	->ensureColumn(new rex_sql_column('template', 'text'))
	->ensureColumn(new rex_sql_column('preview', 'text'))
	->setPrimaryKey('id')
	->ensure();
*/
?>