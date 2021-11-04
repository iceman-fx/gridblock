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
        
        $hasModules = false;
        foreach ($moduleIDs as $uID => $moduleID):
            if (intval($moduleID) > 0) { $hasModules = true; }
        endforeach;
        
        if (!empty($moduleIDs) && $hasModules):
            foreach ($moduleIDs as $uID => $moduleID):
                $uID = str_replace("'", "", $uID);
                $moduleID = intval($moduleID);
        
                if ($moduleID > 0 && !empty($uID)):
                    $editor = new rex_article_content_gridblock();
                    
                    //Values der Spalte wählen
                    $values = rex_var::toArray($this->values[$colID]);
                    $values = $values[$uID];
                            
                    echo rex_article_content_gridblock::getModuleSelector($colID, $moduleID, $uID);				//Sliceblock + Modulselektor einbinden (ohne schließendes div)
                    
                    $editor->setValues($values, $uID);															//$editor->setValues(rex_var::toArray($this->values[$colID]), $uID);
                    echo $editor->getModuleEdit($moduleID, $colID, $uID, $this->rexVars);
					
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
	new Sortable(el, { 
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