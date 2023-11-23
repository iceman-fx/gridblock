<?php
/*
	Redaxo-Addon Gridblock
	Updateprozeduren
	v1.1.12
	by Falko Müller @ 2021 (based on 0.1.0-dev von bloep)
*/

/** RexStan: Vars vom Check ausschließen */
/** @var rex_addon $this */


//Variablen deklarieren
$mypage = $this->getProperty('package');
$error = "";


//Datenbank-Spalten anlegen, sofern noch nicht verfügbar
rex_sql_table::get(rex::getTable('1620_gridtemplates'))
	->ensureColumn(new rex_sql_column('status', 'varchar(10)'))
	->alter();
?>