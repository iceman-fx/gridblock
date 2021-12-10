<?php
/*
	Redaxo-Addon Gridblock
	Fragment für Moduleingabe (BE - innerer Spalteninhalt)
	v1.0
	by Falko Müller @ 2021 (based on 0.1.0-dev von bloep)
	
	
	genutzte VALUES:
	1-16	Variableninhalte der Inhaltsmodule (je Spalte)
	17		Templateauswahl & -optionen
	18		weitere Optionen (ehemals [13])
	19		ausgewählte Inhaltsmodule aller Spalten
	20		reserviert für Plugin contentsettings
*/

//Vorgaben
$config = rex_addon::get('gridblock')->getConfig('config');															//Addon-Eisntellungen holen

$template =	isset($this->values[17]) ? rex_var::toArray($this->values[17]) : array();								//liefert kein Array zurück wenn leer / REX_INPUT_VALUE[17][name]
$options = 	isset($this->values[18]) ? rex_var::toArray($this->values[18]) : array();								//liefert kein Array zurück wenn leer / REX_INPUT_VALUE[18][name]
$modules = 	isset($this->values[19]) ? rex_var::toArray($this->values[19]) : array();								//liefert kein Array zurück wenn leer / REX_INPUT_VALUE[19][name]
$settings = isset($this->values[18]) ? $this->values[18] : "";
	$_SESSION['gridContentSettings'] = $settings;


$colID = $this->id;


$selTemplate = intval(@$template['selectedTemplate']);																//gespeichertes Template einladen
$selColumns = 0; $selPreview = "";
	//restliche Templatedaten aus DB holen
	$db = rex_sql::factory();
	$db->setQuery("SELECT columns, preview FROM ".rex::getTable('1620_gridtemplates')." WHERE id = '".$selTemplate."'");
		
	if ($db->getRows() > 0):
		$selColumns = $db->getValue('columns', 'int');
		$selPreview = $db->getValue('preview');
			$selPreview = (!empty($selPreview)) ? str_replace(array("\n\r", "\n", "\r"), " ", $selPreview) : $selPreview;
			$selPreview = preg_replace("/\s+/", " ", $selPreview);
	endif;


$useSettingPlugin = ( rex_plugin::get('gridblock', 'contentsettings')->isAvailable() ) ? true : false;
?>

<div id="gridblockColumnWrapper<?php echo $colID; ?>">

    <div class="column-settings">
        <?php if (@$config['hideinfotexts'] != 'checked'): ?><span><?php echo rex_i18n::msg('a1620_mod_columnintro'); ?></span><?php endif; ?>

        <?php
        if ($useSettingPlugin):
			//Optionen anzeigen
			echo '<div class="hiddenOpt gridcolumnoptions" id="grid-coloptions'.$colID.'">';

				//Plugin contentsettings
				//wird über Ajax nachgeladen
			
			echo '</div>';
			echo '<div class="optionstoggler" onclick="jQuery(\'#grid-coloptions'.$colID.'\').slideToggle();" title="'.rex_i18n::msg('a1620_mod_column_options').'">.....</div>';
        endif;
		?>
        
    </div>
    
    
    <div id="gridblockColumnSlices<?php echo $colID; ?>" class="column-input column-slices" data-colid="<?php echo $colID; ?>" style="display: block;">
        <?php
        //gespeicherte Module wieder einladen
        $moduleIDs = @$modules[$colID] ?? array();
		
		//dump($moduleIDs);
        
        $hasModules = false;
        foreach ($moduleIDs as $uID => $moduleID):
			//prüfen ob alte Speicherart (1.0-beta) oder neue Art
			if (!is_array($moduleID)):
				//alt (1.0-beta)
				$moduleID = intval($moduleID);
			else:
				//neu
				$moduleID = intval(@$moduleID['id']);
			endif;
		
            if ($moduleID > 0) { $hasModules = true; }
        endforeach;
		
        
        if (!empty($moduleIDs) && $hasModules):
            foreach ($moduleIDs as $uID => $moduleID):
                $uID = str_replace("'", "", $uID);
				$moduleStatus = 1;
				
				//prüfen ob alte Speicherart (1.0-beta) oder neue Art
				if (!is_array($moduleID)):
					//alt (1.0-beta)
					$moduleID = intval($moduleID);
				else:
					//neu
					$moduleStatus = intval(@$moduleID['status']);
					$moduleID = intval(@$moduleID['id']);
				endif;
				
				
				//Inhaltsmodul laden und ausgeben        
                if ($moduleID > 0 && !empty($uID)):
                    $editor = new rex_article_content_gridblock();
                    
                    //Values der Spalte wählen
                    $values = rex_var::toArray($this->values[$colID]);
                    $values = (isset($values[$uID])) ? $values[$uID] : $values;
                            
                    echo rex_article_content_gridblock::getModuleSelector($colID, $moduleID, $uID, $moduleStatus);				//Sliceblock + Modulselektor einbinden (ohne schließendes div)

					//REX-MODULE-VARS erweitern					
					$rexVars = $this->rexVars;
					
					/*
					//INFO: die GRID-Vars machen im Inout keinen Sinn, da praktisch alle Settings per Javascript eingestellt werden und die Vars damit erst nach dem Speichern vorliegen würden !!!
					
					$rexVars['grid_tmplID'] 	= $selTemplate;							//GRID: Template ID
					$rexVars['grid_tmplPREV'] 	= $selPreview;							//GRID: Template Preview-JSON als array()
					$rexVars['grid_tmplCOLS']	= $selColumns;							//GRID: Template Spaltenanzahl
					$rexVars['grid_colNR'] 		= $colID;								//GRID: Spaltennummer
					
					//Modul-Settingsvariable setzen & bereitstellen
					$gridSettings = array(
						"template" => array(
							"id"		=> $selTemplate,
							"preview"	=> json_decode($selPreview, true),
							"columns"	=> $selColumns
						)
					);
					$gridSettingsMod = array(
						"column" => array(
							"number"	=> $colID
						)
					);
					$gridSettingsMod = array_merge($gridSettings, $gridSettingsMod);
					*/


					//Eingaben des Moduls holen
					//rex_addon::get('gridblock')->setProperty('REX_GRID_SETTINGS', $gridSettingsMod);
                    $editor->setValues($values, $uID);															//$editor->setValues(rex_var::toArray($this->values[$colID]), $uID);
                    echo $editor->getModuleEdit($moduleID, $colID, $uID, $rexVars);
					//rex_addon::get('gridblock')->removeProperty('REX_GRID_SETTINGS');
					
					echo '</div>';
                endif;
                
            endforeach;
            
        else:
            //Moduleselektor ausgeben
            echo rex_article_content_gridblock::getModuleSelector($colID);
        endif;
        ?>        
    </div>
    
    <script>
	//DRAG Button
	var el = document.getElementById('gridblockColumnSlices<?php echo $colID; ?>');
	new GridblockSortable(el, { 
		handle: '.btn-move-drag', 
		animation: 150, 
		onEnd: function(e){
			var obj = $(e.item);
			obj.trigger('rex:change', [obj]);
			gridblock_setGridSortedSlices(<?php echo $colID; ?>);
		}
	});
	</script>
</div>
  
    
<!-- Sortierung Inhaltsblöcke dieser Spalte -->
<input type="hidden" name="REX_INPUT_VALUE[18][columnslices][<?php echo $colID; ?>]" id="gridblockSortedSlices<?php echo $colID; ?>" value="<?php echo @$options['columnslices'][$colID]; ?>" />


<?php if (@$moduleId > 0): ?>
<!-- gespeicherte Inhaltsblöcke beim Start zeigen -->
<script>$(document).on('rex:ready', function(e) { $("#gridblockColumnSlices<?php echo $colID; ?>").show(); });</script>
<?php endif; ?>