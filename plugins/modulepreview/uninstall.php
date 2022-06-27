<?php


// Tabelle Module um zwei Felder erleichtern
rex_sql_table::get(rex::getTable('module'))
    ->removeColumn('gridblock_modulepreview_thumbnail')
    ->removeColumn('gridblock_modulepreview_description')
    ->alter();

// Medien-Effekt löschen
$sql = rex_sql::factory();
$sql->setTable(rex::getTablePrefix().'media_manager_type');
$sql->setWhere(['name'=>'gridblock_modulepreview_thumbnail']);
$sql->delete();

$sql->setTable(rex::getTablePrefix().'media_manager_type_effect');
$sql->setWhere(['createuser'=>'gridblock_modulepreview']);
$sql->delete();