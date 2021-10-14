<?php
/*
	Redaxo-Addon Gridblock
	Fragment für Moduleingabe (BE - innerer Spalteninhalt)
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


$colID = $this->id;

$useoptions = (@$config['useoptions'] == 'checked') ? true : false;
?>

<div id="gridblockColumnWrapper<?php echo $colID; ?>">

    <div class="column-settings">
    	<span><?php echo rex_i18n::msg('a1620_mod_columnintro'); ?></span>

        
        <?php if ($useoptions): ?>
        <!-- Optionen -->
        <div class="hiddenOpt" id="grid-coloptions<?php echo $colID; ?>">
            <dl class="rex-form-group form-group">&nbsp;</dl>
        
    
            <dl class="rex-form-group form-group">
                <dt><label for=""><?php echo rex_i18n::msg('a1620_mod_valign'); ?>:</label></dt>
                <dd>
                    <select name="REX_INPUT_VALUE[18][valign][<?php echo $colID; ?>]" class="form-control">
                    <?php
                    $arr = array(	"normal"=>rex_i18n::msg('a1620_mod_valign_normal'), 
                                    "start"=>rex_i18n::msg('a1620_mod_valign_top'), 
                                    "center"=>rex_i18n::msg('a1620_mod_valign_center'), 
                                    "end"=>rex_i18n::msg('a1620_mod_valign_bottom')
                                );
                    
                    foreach ($arr as $key=>$value):
                        $sel = ($key == @$options['valign'][$colID]) ? 'selected="selected"' : '';
                        echo '<option value="'.$key.'" '.$sel.'>'.$value.'</option>';
                    endforeach;
                    ?>
                    </select>
                </dd>
            </dl>
        
    
            <dl class="rex-form-group form-group">
                <dt><label for=""><?php echo rex_i18n::msg('a1620_mod_paddingcss'); ?>:</label></dt>
                <dd>
                    <select name="REX_INPUT_VALUE[18][paddingcss][<?php echo $colID; ?>]" class="form-control">
                    <?php
                    $arr = array(	""=>rex_i18n::msg('a1620_mod_paddingcss_none'), 
                                    "sm"=>rex_i18n::msg('a1620_mod_paddingcss_small'), 
                                    "md"=>rex_i18n::msg('a1620_mod_paddingcss_medium'), 
                                    "lg"=>rex_i18n::msg('a1620_mod_paddingcss_large')
                                );
                    
                    foreach ($arr as $key=>$value):
                        $sel = ($key == @$options['paddingcss'][$colID]) ? 'selected="selected"' : '';
                        echo '<option value="'.$key.'" '.$sel.'>'.$value.'</option>';
                    endforeach;
                    ?>
                    </select>
                </dd>
            </dl>
            
    
            <dl class="rex-form-group form-group">
                <dt><label for=""><?php echo rex_i18n::msg('a1620_mod_bgcolor'); ?>:</label></dt>
                <dd>        
                    <div class="input-group gridblock-colorinput-group"><input data-parsley-excluded="true" type="text" name="REX_INPUT_VALUE[18][bgcol][<?php echo $colID; ?>]" value="<?php echo @$options['bgcol'][$colID]; ?>" maxlength="7" placeholder="Bsp: #11AA99" pattern="^#([A-Fa-f0-9]{6})$" class="form-control"><span class="input-group-addon gridblock-colorinput"><input type="color" value="<?php echo @$options['bgcol'][$colID]; ?>" pattern="^#([A-Fa-f0-9]{6})$" class="form-control"></span></div>
                </dd>
            </dl>
    
        </div>
        
        
        <!-- Optionen-Toggler -->
        <div class="optionstoggler" onclick="jQuery('#grid-coloptions<?php echo $colID; ?>').slideToggle();" title="<?php echo rex_i18n::msg('a1620_mod_column_options'); ?>">.....</div>
        <?php endif; ?>
        
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