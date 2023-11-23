<?php
/*
	Redaxo-Addon Gridblock
	DeInstallation
	v1.1.12
	by Falko Müller @ 2021 (based on 0.1.0-dev von bloep)
*/

/** RexStan: Vars vom Check ausschließen */
/** @var rex_addon $this */


//Variablen deklarieren
$mypage = $this->getProperty('package');
$error = ""; $notice = "";


//Datenbank-Einträge löschen
$db = rex_sql::factory();
$db->setQuery("DROP TABLE IF EXISTS ".rex::getTable('1620_gridtemplates'));
?>