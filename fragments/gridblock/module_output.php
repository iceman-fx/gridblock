<?php
/*
	Redaxo-Addon Gridblock
	Fragment für Modulausgabe (FE/BE)
	v0.8
	by Falko Müller @ 2021 (based on 0.1.0-dev von bloep)

	
	genutzte VALUES:
	1-16	Variableninhalte der Inhaltsmodule (je Spalte)
	17		Templateauswahl & -optionen
	18		weitere Optionen (ehemals [13])
	19		ausgewählte Inhaltsmodule aller Spalten
	20		reserviert für Blocksettings
*/

//Vorgaben
$config = rex_addon::get('gridblock')->getConfig('config');

$template =	isset($this->values[17]) ? rex_var::toArray($this->values[17]) : array();								//liefert kein Array zurück wenn leer / REX_INPUT_VALUE[17][name]
$options = 	isset($this->values[18]) ? rex_var::toArray($this->values[18]) : array();								//liefert kein Array zurück wenn leer / REX_INPUT_VALUE[18][name]
$modules = 	isset($this->values[19]) ? rex_var::toArray($this->values[19]) : array();								//liefert kein Array zurück wenn leer / REX_INPUT_VALUE[19][name]


$selTemplate = intval(@$template['selectedTemplate']);																//gespeichertes Template einladen
$selColumns = 0;
	//restliche Templatedaten aus DB holen
	$db = rex_sql::factory();
	$db->setQuery("SELECT columns, preview FROM ".rex::getTable('1620_gridtemplates')." WHERE id = '".$selTemplate."'");
		
	if ($db->getRows() > 0):
		$selColumns = $db->getValue('columns', 'int');				
	endif;

$useoptions = (@$config['useoptions'] == 'checked') ? true : false;


if (false):
	echo "<br>options:<br>";
	dump($options);
	echo "<br>template:<br>";
	dump($template);
	echo "<br>modules:<br>";
	dump($modules);
	
	echo "<br>selTemplate: $selTemplate";
	echo "<br>selColumns: $selColumns";
	
	echo "<br><br><hr><br>";
endif;


//Template holen und Ausgaben aufbereiten
$db = rex_sql::factory();
$db->setQuery("SELECT template FROM ".rex::getTable('1620_gridtemplates')." WHERE id = '".$selTemplate."'");

if ($db->getRows() > 0):
	$rowClasses = $rowStyles = "";
	$op = $db->getValue('template');
	
	//zus. Optionen des Blockes (ROW) setzen
	$gapv = intval(@$options['gap_v']);
	$gaph = intval(@$options['gap_h']);
	
	$classes = 'PETER';
	
	$styles = 'FALKO';
		$styles .= ($gapv > 0) ? 'grid-row-gap: '.$gapv.'px; ' : '';
		$styles .= ($gaph > 0) ? 'grid-column-gap: '.$gaph.'px; ' : '';


	//alle Salten durchlaufen und Inhalte holen/setzen
	for ($i = 1; $i <= $selColumns; ++$i):
		$colClasses = $colStyles = "";
	
		//zus. Optionen der Spalte setzen
		$valign = @$options['valign'][$i];
		$colClasses .= (preg_match("/(start|center|end)/i", $valign)) ? 'gridcol-va'.$valign : '';
			
		$paddingcss = @$options['paddingcss'][$i];
		$colClasses .= (!empty($paddingcss)) ? 'gridcol-padding'.$paddingcss : '';
			
		$bgcol = @$options['bgcol'][$i];
		$colStyles .= (!empty($bgcol)) ? 'background-color: '.$bgcol.';' : '';

			//alle Inhalte der Spalte holen
			$modOP = '';
			$moduleIDs = @$modules[$i] ?? null;
			
			if (!empty($moduleIDs)):
				foreach ($moduleIDs as $uID => $moduleID):
					$uID = str_replace("'", "", $uID);
					$moduleID = intval($moduleID);
			
					if ($moduleID && $uID):
						$editor = new rex_article_content_gridblock();
						
						//Values der Spalte wählen
						$values = rex_var::toArray($this->values[$i]);
						$values = $values[$uID];
						
						unset($modcol);
						$modcol['COLUMN']['ID'] = $i;
						$modcol['COLUMN']['VALIGN'] = $valign;
							/*
							$modcol['COLUMN']['PADDING'] = array( "TOP"=>@$options['padding'][$i]['top'], "RIGHT"=>@$options['padding'][$i]['right'], "BOTTOM"=>@$options['padding'][$i]['bottom'], "LEFT"=>@$options['padding'][$i]['left'] );
							$modcol['COLUMN']['PADDINGCSS'] = $paddingcss;
							*/

						$values = array_merge($values, $modcol);
						$values ?? null;
						
						
						//Ausgaben des Moduls holen
						$editor->setValues($values, $uID);
						$modOP .= $editor->getModuleOutput($moduleID, $uID, $this->rexVars);
					endif;
			
				endforeach;
			endif;

		//GRID-Platzhalter (column) ersetzen
		$op = preg_replace("/REX_GRID\[(\s)*(id=)?".$i."(\s)*output=class(\s)*\]/", $colClasses, $op);			//CSS-Klassen
		$op = preg_replace("/REX_GRID\[(\s)*(id=)?".$i."(\s)*output=style(\s)*\]/", $colStyles, $op);			//CSS-Inlinestile
		$op = preg_replace("/REX_GRID\[(\s)*(id=)?".$i."(\s)*\]/", $modOP, $op);								//Spaltencontent		REX_GRID\[(\s)*(id=)?".$i."(\s)*(.*)\]
		
	endfor;
	
	
	//GRID-Platzhalter (global) ersetzen
	$op = preg_replace("/REX_GRID\[(\s)*output=class(\s)*\]/", $rowClasses, $op);								//CSS-Klassen
	$op = preg_replace("/REX_GRID\[(\s)*output=style(\s)*\]/", $rowStyles, $op);								//CSS-Inlinestile
	
	
	//PHP-Code des Template ausführen und Rückgabe verwerten
	ob_start();
	try {
		ob_implicit_flush(0);
		$sandbox = function() use ($op, $classes, $styles, $selTemplate) {
			/*@eval('?>'.$op.'<?php;');*/
			require rex_stream::factory('rex_gridblock/template/'.$selTemplate, $op);								//führt PHP-Code aus und gibt Rückgabe zurück (1. Parameter ist nur für Fehlerhinweis = virtueller Pfad)
		};
		$sandbox();
	} finally {
		$CONTENT = ob_get_clean();
	}
	$op = $CONTENT;
	
	
	//alles ausgeben
	echo $op;
	unset($op);
	
else:
	echo (rex::isBackend()) ? 'Bisher wurde keine Layoutvorlage ausgewählt.' : '';
endif;
?>