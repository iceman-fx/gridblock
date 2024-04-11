<?php
/*
	Redaxo-Addon Gridblock
	Fragment für Moduleingabe (BE)
	v1.0.11
	by Falko Müller @ 2021-2022 (based on 0.1.0-dev von bloep)
	
	
	genutzte VALUES:
	1-16	Variableninhalte der Inhaltsmodule (je Spalte)
	17		Templateauswahl & -optionen
	18		weitere Optionen (ehemals [13])
	19		ausgewählte Inhaltsmodule aller Spalten
	20		reserviert für Plugin contentsettings
*/

//Vorgaben
$config = rex_addon::get('gridblock')->getConfig('config');

$template =	isset($this->values[17]) ? rex_var::toArray($this->values[17]) : array();								//liefert kein Array zurück wenn leer / REX_INPUT_VALUE[17][name]
$options = 	isset($this->values[18]) ? rex_var::toArray($this->values[18]) : array();								//liefert kein Array zurück wenn leer / REX_INPUT_VALUE[18][name]
$settings = isset($this->values[20]) ? $this->values[20] : "";
	$_SESSION['gridContentSettings'] = $settings;


$selTemplate = intval(@$template['selectedTemplate']);																//gespeichertes Template einladen
$selColumns = $selColumnNames = 0;
	//restliche Templatedaten aus DB holen
	$db = rex_sql::factory();
	$db->setQuery("SELECT columns, preview FROM ".rex::getTable('1620_gridtemplates')." WHERE id = '".$selTemplate."'");
		
	if ($db->getRows() > 0):
		$selColumns = $db->getValue('columns', 'int');				
		$selPreview = $db->getValue('preview');
			$selPreview = (!empty($selPreview)) ? json_decode($selPreview, TRUE) : '';
		
		if (!empty($selPreview) && is_array($selPreview)):
			$colnames = array();
			for ($p=0; $p < intval($selPreview['totalcolumns']); $p++):
				$t = @$selPreview['columns'][$p]['title'];
				array_push($colnames, $t);
			endfor;
			$selColumnNames = urlencode(json_encode($colnames));
		endif;
	endif;


$useSettingPlugin = ( rex_plugin::get('gridblock', 'contentsettings')->isAvailable() ) ? true : false;


/*
if (false):
	echo "<br>options:<br>";
	dump($options);
	echo "<br>template:<br>";
	dump($template);
	
	echo "<br>selTemplate: $selTemplate";
	echo "<br>selColumns: $selColumns";
	
	echo "<br><hr><br>";
endif;
*/
?>


<div class="gridblock <?php echo ($selTemplate <= 0) ? 'noGridTemplate' : ''; ?>">

    <!-- Modalfenster Templates -->
    <div class="modal fade bd-example-modal-lg gridblock-modal gridblock-modal-large" id="gridblockModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo rex_i18n::msg('a1620_mod_templatelist'); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>                
                </div>
                <div class="modal-body">
					<?php
					$db = rex_sql::factory();
                    $db->setQuery("SELECT id, title, description, columns, preview FROM ".rex::getTable('1620_gridtemplates')." WHERE status = 'checked' ORDER BY prio ASC, columns ASC, title ASC, id ASC");
                    
                    if ($db->getRows() > 0):
                        echo '<div class="gridblock-previewlist">';
                        
                        for ($i=0; $i < $db->getRows(); $i++):
                            $eid = intval($db->getValue('id'));
                            
                            $title = aFM_maskChar(aFM_textOnly($db->getValue('title'), true));
                            $desc = aFM_maskChar(aFM_textOnly($db->getValue('description'), true));
								$desc = (empty($desc)) ? $title : $desc;
                            $columns = $db->getValue('columns', 'int');				
                            $preview = $db->getValue('preview');
                                $preview = (!empty($preview)) ? json_decode($preview, TRUE) : '';
                                
                            $sel = ($selTemplate == $eid) ? true : false;
                            
							echo '<div>';
								echo (@$config['showtemplatetitles'] == 'checked') ? '<div class="gridblock-preview-caption">'.$title.'</div>' : '';
								echo a1620_generatePreview($preview, $eid, $columns, $title, $desc, $sel, true);
							echo '</div>';
							
                            $db->next();
                        endfor;
                        
                        echo '</div>';
						
					else:
						//keine Templates gefunden
						echo rex_i18n::rawmsg('a1620_mod_templatelist_noentrys');
                    endif;
                    ?>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal"><?php echo rex_i18n::msg('a1620_close'); ?></button></div>
            </div>
        </div>
    </div>
        

	<fieldset class="gridblock-top">
    
		<dl class="rex-form-group form-group">
        	<dt><label for=""><?php echo rex_i18n::msg('a1620_mod_template'); ?>:</label></dt>
            <dd>


                <div class="rex-js-widget rex-js-widget-gridblock">
                	<div class="input-group">
                    	<div class="gridblock-template" id="gridblock-template"><?php echo rex_i18n::msg('a1620_mod_template_empty'); ?></div>
                        <span class="input-group-btn">
							<a data-toggle="modal" data-target="#gridblockModal" class="btn btn-popup" title="<?php echo rex_i18n::msg('a1620_mod_choose_template'); ?>"><i class="rex-icon fa-th-large"></i></a>
                            <?php if ($useSettingPlugin): ?><a class="btn btn-popup btn-options" title="<?php echo rex_i18n::msg('a1620_mod_template_options'); ?>" onclick="$('#gridblockoptions').slideToggle();"><i class="rex-icon fa-cog"></i></a><?php endif; ?>
						</span>
					</div>
				</div>
            
                <input type="hidden" name="REX_INPUT_VALUE[17][selectedTemplate]" id="gridblock-selectedTemplate" value="<?php echo $selTemplate; ?>" />
                <input type="hidden" name="REX_INPUT_VALUE[17][selectedColumns]" id="gridblock-selectedColumns" value="<?php echo $selColumns; ?>" />
                <input type="hidden" name="REX_INPUT_VALUE[17][selectedColumnNames]" id="gridblock-selectedColumnNames" value="<?php echo $selColumnNames; ?>" />
			</dd>
		</dl>
        
    
		<?php
        if ($useSettingPlugin):
            //Optionen anzeigen
            echo '<div class="hiddenOpt gridblockoptions" id="gridblockoptions">';
        
                //Plugin contentsettings
                //wird über Ajax nachgeladen
            
            echo '</div>';
        endif;
        ?>
        
    </fieldset>
    
    
    <div class="gridblock-bottom">
    
        <ul class="nav nav-tabs tab-nav" role="tablist" id="gridblockColNav">
            <?php
            $first = true;
            foreach ($this->cols as $i => $col):
                ?>
                <li role="presentation" id="gridblockTab<?php echo $i; ?>" >
                    <a href="#gridblockCol<?php echo $i; ?>" aria-controls="gridblockCol<?php echo $i; ?>" role="tab" data-toggle="tab"><?php echo $i; ?>. <?php echo rex_i18n::msg('a1620_mod_column'); ?></a>
                </li>
                <?php
                $first = false;
            endforeach;
            ?>
        </ul>
        
        <div class="tab-content">
            <?php
            $first = true;
            foreach ($this->cols as $i => $col):
                ?>
                <div role="tabpanel" class="tab-pane fade" id="gridblockCol<?php echo $i; ?>">
                    <?php echo $col['content']; ?>
                </div>
                <?php
                $first = false;
            endforeach;
            ?>
        </div>
        
        <input type="hidden" name="REX_INPUT_VALUE[18][selectedtab]" id="gridblockSelectedTab" value="<?php echo (isset($_POST['btn_update'])) ? @$options['selectedtab'] : ''; ?>" />

    </div>
</div>


<script>
var gridblock_alwaysallowdelete = (<?php echo (@$config['alwaysallowdelete'] == 'checked') ? 'true' : 'false'; ?> ? true : false);
var gridblock_sliceid = <?php echo intval($_SESSION['gridRexVars']['sliceID']); ?>;

	
$(function(){
	//Spalten + Templateauswahl beim Start setzen, sofern bereits gespeichert wurde
	gridblock_showGridTemplate();
	gridblock_showGridColumns();

	
	//Spaltenauswahl (active)
	$('#gridblockColNav a').off('click').on('click', function(e) {
		e.preventDefault();
		$(this).tab('show');
		
		$('#gridblockSelectedTab').val($(this).parent('li').index());					//aktiven Tab zwischenspeichern für "Block übernehmen"
	});
	
	
	//Template-Modalfenster ansteuern
	$('#gridblockModal').on('show.bs.modal', function (e) { $('html').addClass('modal-opened'); })
	$('#gridblockModal').on('hide.bs.modal', function (e) { $('html').removeClass('modal-opened'); });
	
	$('#gridblockModal .gridblock-preview').on('click', function() {
		id = parseInt($(this).data('id'));
		cols = parseInt($(this).data('columns'));
		colnames = $(this).data('colnames');
		html = $(this).prop("outerHTML");
		
		if (id > 0 && cols > 0 && html != undefined && html.length > 10) {
			$(this).parent('div').find('div.selected').removeClass('selected');
			$(this).addClass('selected');
			
			$('input#gridblock-selectedTemplate').val(id);
			$('input#gridblock-selectedColumns').val(cols);
			$('input#gridblock-selectedColumnNames').val(colnames);
			$('div.gridblock').removeClass('noGridTemplate');
			
			gridblock_showGridTemplate();
			gridblock_showGridColumns();
		}
		
		$('#gridblockModal').modal('hide');
	});	
			
	
	//Inhaltsmodul bei Auswahl über Modulselektor laden
	$(document).on('click', '.gridblock-moduleselector a', function(e){
		e.preventDefault();
		e.stopPropagation();
		e.stopImmediatePropagation();
		
		var par = $(this).parents('ul');
		var moduleID = parseInt($(this).data('modid'));
		var moduleName = $(this).data('modname');
		var uID = par.data('uid');
		var colID = parseInt(par.data('colid'));
		var copyID = $(this).data('copyid');
		
		if (moduleID > 0 && colID > 0) {
			if (copyID != undefined) {
				gridblock_loadModule(moduleID, colID, uID, moduleName, 'copy');
			} else {
				gridblock_loadModule(moduleID, colID, uID, moduleName);
			}
		} else {
			$('#gridblockModuleContent'+uID).remove();
		}
		
		gridblock_setGridSortedSlices(colID);
	});	
	
	
	//neuen Modulblock (select/dropdown) einbinden
	$(document).on('click', '.gridblock a.btn-addgridmodule', function(e){
		e.preventDefault();
		e.stopPropagation();
		e.stopImmediatePropagation();

		var colID = parseInt($(this).data('colid'));
		var uID = $(this).data('uid');
		
		if (colID > 0 && uID != undefined) {
			gridblock_loadModuleSelector(colID, '#gridblockColumnSlice'+uID, 'after');
		}
	});	
	
	
	//Move-UP/DOWN Buttons
	$(document).on('click', '.gridblock .column-slice-sorter a.btn-move-up', function(e){
		e.preventDefault();
		e.stopPropagation();
		e.stopImmediatePropagation();
		
		var cur = $(this).closest('div.column-slice');
		var par = cur.parent();
		var colID = parseInt(par.data('colid'));
		
		var obj = cur.prev();
			cur.prev().before(cur);
   		obj.trigger('rex:change', [obj]);
		gridblock_setGridSortedSlices(colID);
	});
	$(document).on('click', '.gridblock .column-slice-sorter a.btn-move-down', function(e){
		e.preventDefault();
		e.stopPropagation();
		e.stopImmediatePropagation();
		
		var cur = $(this).closest('div.column-slice');
		var par = cur.parent();
		var colID = parseInt(par.data('colid'));

		var obj = cur.next();
			cur.next().after(cur);
		obj.trigger('rex:change', [obj]);
		gridblock_setGridSortedSlices(colID);
	});
	
	
	//Delete Button
	$(document).on('click', '.gridblock .column-slice-sorter a.btn-delete', function(e){
		e.preventDefault();
		e.stopPropagation();
		e.stopImmediatePropagation();
		
		var cur = $(this).closest('div.column-slice');
		var par = cur.parent();
		var colID = parseInt(par.data('colid'));
		
		if (gridblock_checkDeleteButtons(colID) > 1 || gridblock_alwaysallowdelete) {
			var resp = confirm('<?php echo rex_i18n::msg('a1620_mod_confirm_delete'); ?>');
			if (resp == true) {
				cur.remove();
				
				dst = '#gridblockColumnSlices'+colID;
				if (gridblock_alwaysallowdelete && $(dst).children().length == 0) { gridblock_loadModuleSelector(colID, dst); }				//Standard wiederherstellen, wenn letzter Block gelöscht uwrde
				
				gridblock_checkDeleteButtons(colID);
				gridblock_setGridSortedSlices(colID);
			}
		}
	});
	
	
	//Copy Button
	$(document).on('click', '.gridblock .column-slice-sorter a.btn-copy', function(e){
		e.preventDefault();
		e.stopPropagation();
		e.stopImmediatePropagation();
		
		var cClass = 'gridblock-iscopied';
		var colID = parseInt($(this).data('colid'));
		var uID = $(this).data('uid');
		var modID = parseInt($(this).data('modid'));
		var modStatus = $(this).data('modstatus');
		
		if (colID > 0 && uID != undefined) {
			if (!$(this).hasClass(cClass)) {
				//kopierten Block im Cookie zwischenspeichern
				$.ajax({
					url: 'index.php?page=structure&rex-api-call=gridblock_setCookie&sliceid=' +gridblock_sliceid+ '&uid=' +uID+ '&colid=' +colID+ '&modid=' +modID+ '&modstatus=' +modStatus+ '&action=copy',
					success: function(data) {},
					async: false
				});
				
				//Button-Status setzen
				$('.gridblock .column-slice-sorter a.btn-copy').removeClass(cClass);
				$(this).addClass(cClass);
			} else {
				//Cookie löschen und Button-Status zurücksetzen (wird nur ausgeführt, wenn der selbe Button erneut geklickt wird)
				gridblock_deleteCookie();
				$(this).removeClass(cClass);
			}
			
			//alle aktiven Moduleselektoren refreshen
			gridblock_refreshModuleSelectors();
		}
	});
	
	
	//Status Button (on/off)
	$(document).on('click', '.gridblock .column-slice-sorter a.btn-status', function(e){
		e.preventDefault();
		e.stopPropagation();
		e.stopImmediatePropagation();
		
		var dst = $(this);
		var cur = dst.closest('div.column-slice');
		var uID = cur.data('uid');

		if (uID != undefined) {
			if (dst.hasClass('rex-offline')) {
				//online
				dst.removeClass('rex-offline').find('i').removeClass('fa-eye-slash');
				$('#gridModuleStatus'+uID).val("1");
			} else {
				//offline
				dst.addClass('rex-offline').find('i').addClass('fa-eye-slash');
				$('#gridModuleStatus'+uID).val("0");
			}
		}
	});

});


//Inhaltsmodul nachladen
function gridblock_loadModule(moduleID, colID, uID, moduleName, action = "") {
	moduleID = parseInt(moduleID);
	colID = parseInt(colID);

	if (moduleID > 0 && colID > 0 && uID != undefined) {
		$('#rex-js-ajax-loader').addClass('rex-visible');
		
		$.ajax({
			url: 'index.php?page=structure&rex-api-call=gridblock_loadModule&moduleid=' +moduleID+ '&colid=' +colID+ '&uid=' +uID+ '&action=' +action+ '&function=add&slice_id=' +gridblock_sliceid,
		}).done(function(data) {
			//Einfügeposition vorbereiten
			dst = $('#gridblockColumnSlice'+uID);
			dst.children('.column-input').remove();
			
			//Modulselector ausblenden
			dstSF = dst.children('.column-slice-functions');
			dstSF.children('input#gridModuleSelect'+uID).val(moduleID);
			dstSF.children('div.dropdown').remove();
				moduleName = (moduleName != undefined && moduleName.length > 0) ? moduleName : '[ID: '+moduleID+']';
			dstSF.children('div.gridblock-moduleinfo').text(moduleName).show();
			
			//Modul-Input in DOM einbetten
			dst.append(data).show();
			
			//kopierten Status setzen
			if (action == 'copy' && gridblock_getCookie('action') == 'copy' && gridblock_getCookie('modstatus') != 1) { dst.find('.column-slice-sorter a.btn-status').trigger('click'); }
			
			//Vorgang mit ready abschließen
			$('body').trigger('rex:ready', [$('body')]);					//macht Probleme -> setzt die Spalten-Navigation zurück
			$(document).trigger('ready');
			$(document).trigger('pjax:success');
		}).always(function() {
			$('#rex-js-ajax-loader').removeClass('rex-visible');
		}).fail(function() {
			$('#gridModuleSlices'+colID).html('<div class="alert alert-danger"><?php echo rex_i18n::msg('a1620_mod_error_loadmodule'); ?></div>');
		})
	}
}


//Reihenfolge der Inhaltsmodule zwischenspeichern
function gridblock_setGridSortedSlices(colID) {
	colID = parseInt(colID);
	
	if (colID > 0) {
		var slices = [];
		$('#gridblockColumnWrapper'+ colID +' .column-slice-functions input[type=hidden]').each(function(){
			uID = $(this).data('uid');
			if (uID != undefined && uID != "" && parseInt($(this).val()) > 0) { slices.push(uID); }
		}).promise().done(function(){
			slices = slices.join(',');
			$('#gridblockSortedSlices'+colID).val(slices);
		});
	}
}


//Spalten je nach Template setzen/anzeigen
function gridblock_showGridColumns(cols) {
	var maxcols = <?php echo $this->maxCols; ?>;
	var templateID = parseInt($('#gridblock-selectedTemplate').val());
	var cols = parseInt($('#gridblock-selectedColumns').val());
	var colnames = $('#gridblock-selectedColumnNames').val();
		colnames = (colnames.length > 5) ? JSON.parse( decodeURIComponent( colnames.replace(/\+/g, '%20') ) ) : '';
	
	var colsset = 0;
	for (var i=1; i <= maxcols; ++i) {
		var colID = i;
		
		if (i <= cols) {
			$('#gridblockTab'+colID).show();
			
			<?php if ($config['previewtabnames'] == 'checked'): ?>
			//Spaltenname aus Layoutvorschau nutzen
			tabname = colnames[colID-1];
			if (tabname != undefined && tabname != "") { $('a[href="#gridblockCol'+colID+'"]').text(tabname); }
			<?php endif; ?>
			
			gridblock_loadContentSettings(templateID, colID);
			colsset+=1;
		} else {
			$('#gridblockTab'+colID).hide();
			$('#gridblockColumnSlices'+colID).children().remove();
			
			gridblock_loadModuleSelector(colID, '#gridblockColumnSlices'+colID);
		}
		
		gridblock_checkDeleteButtons(colID);
	}
	
	if (colsset > 0) {
		$('a[href="#gridblockCol1"]').tab("show");						//immer den ersten Tab aktivieren
		
		<?php if (isset($_POST['btn_update'])): ?>
		//zuletzt gewählten Tab wieder aktivieren, wenn "Block übernehmen" genutzt wurde		
		lastTab = parseInt($('#gridblockSelectedTab').val());
		if (lastTab > 0) { $('a[href="#gridblockCol'+(lastTab+1)+'"]').trigger("click"); }
		<?php endif; ?>
	}
}


//Templatevorschau setzen
function gridblock_showGridTemplate(id) {
	id = parseInt($('#gridblock-selectedTemplate').val());

	if (id > 0) {
		html = $('#gridblock-template'+id).prop("outerHTML");
		capt = $('#gridblock-template'+id).prev('.gridblock-preview-caption').prop("outerHTML");
			capt = (capt != undefined) ? capt : '';
		
		if (html != undefined && html.length > 10) { $('div#gridblock-template').html(capt+html); }
		gridblock_loadContentSettings(id);
	}
}


//moduleSelector per Ajax nachladen
function gridblock_loadModuleSelector(colID = 0, dst = "", mode = "html") {
	var colID = parseInt(colID);
	
	if (colID > 0 && dst != undefined && dst != "") {
		var dst = $(dst);
		
		$.ajax({
			url: 'index.php?page=structure&rex-api-call=gridblock_getModuleSelector&colid=' +colID,
		}).done(function(data) {
			switch (mode) {
				case 'prepend':	$(dst).prepend(data).show();
				break;
				
				case 'append':	$(dst).append(data).show();
				break;
				
				case 'before':	$(dst).before(data).show();
								gridblock_scrollToNewBlock($(dst).prev());
				break;
				
				case 'after':	$(dst).after(data).show();
								gridblock_scrollToNewBlock($(dst).next());
				break;
				
				default:		$(dst).html(data).show();
				break;
			}
			
			gridblock_checkDeleteButtons(colID);
			
		}).fail(function() {
			dst.html('<div class="alert alert-danger"><?php echo rex_i18n::msg('a1620_mod_error_loadmoduleselector'); ?></div>');
		})
	}
}


//Modulselectoren refreshen (Copy-Status)
function gridblock_refreshModuleSelectors() {
	$('.gridblock .column-slice-functions .gridblock-moduleselector').each(function(){
		var dst = $(this).parent('div.dropdown');
		colID = parseInt($(this).data('colid'));
		uID = $(this).data('uid');
		
		if (colID > 0 && uID != undefined) {
			$.ajax({
				url: 'index.php?page=structure&rex-api-call=gridblock_addModuleSelector&uid=' +uID+ '&colid=' +colID,
				success: function(data) { dst.replaceWith(data); }
			});
		}
	});
}


//contentSettings per Ajax nachladen
function gridblock_loadContentSettings(templateID, colID = 0) {
	var templateID = parseInt(templateID);
	var colID = parseInt(colID);
	
	<?php if ($useSettingPlugin): ?>
	if (templateID > 0) {
		var dst = (colID <= 0) ? $('#gridblockoptions') : $('#grid-coloptions'+colID);
		
		$.ajax({
			url: 'index.php?page=structure&rex-api-call=gridblock_loadContentSettings&templateid=' +templateID+ '&colid=' +colID,
		}).done(function(data) {
			//contentOptions ausgeben
			dst.html(data);
			gridblock_showHideContentSettings(data.length, templateID, colID);
			
			$('body').trigger('rex:ready', [$('body')]);					//macht Probleme -> setzt die Spalten-Navigation zurück
			$(document).trigger('ready');
			$(document).trigger('pjax:success');
		}).fail(function() {
			dst.html('<div class="alert alert-danger"><?php echo rex_i18n::msg('a1620_mod_error_loadcontentoptions'); ?></div>');
		})
	}
	<?php endif; ?>
}


//Settings anzeigen/ausblenden
function gridblock_showHideContentSettings(datalen, templateID, colID = 0) {
	var datalen = parseInt(datalen);
	var templateID = parseInt(templateID);
	var colID = parseInt(colID);

	if (datalen > 0 && templateID > 0) {
		if (colID == 0) { $('.gridblock-top a.btn-options').css('display', 'inline-flex'); }
		if (colID > 0) { $('#gridblockColumnWrapper'+colID+' div.optionstoggler').show(); }
	} else {
		if (colID == 0) { $('.gridblock-top a.btn-options, .gridblock-top .gridblockoptions').hide(); }
		if (colID > 0) { $('#gridblockColumnWrapper'+colID+' div.optionstoggler, #gridblockColumnWrapper'+colID+' .gridcolumnoptions').hide(); }
	}
}


//Löschbutton-Status prüfen
function gridblock_checkDeleteButtons(colID) {
	colID = parseInt(colID);
	count = 0;
	
	if (colID > 0) {
		dst = $('#gridblockColumnSlices'+colID).children('.column-slice:not(".gridblock-slice-disabled")');    
			if (dst.length < 2 && !gridblock_alwaysallowdelete) { dst.addClass('column-slice-nodelete'); }
			else { dst.removeClass('column-slice-nodelete'); }	
		count = dst.length;
	}
	
	return count;
}


//Backend zum neuen Element scrollen bei "Add"
function gridblock_scrollToNewBlock(dst) {
	if (dst && dst.length > 0) {
		pos = dst.offset();
		posTop = parseFloat(pos.top);
		
		if (posTop > 0) { $("body, html").animate({scrollTop: posTop-50}, 1000); }
	}
}


//Copy: Cookiename holen
function gridblock_getCookieName() {
	var name = "";
		$.ajax({
			url: 'index.php?page=structure&rex-api-call=gridblock_getCookieName',
			success: function(data) { name = data; },
			async: false
		});
	return name;
}

//Copy: Cookiedaten holen
function gridblock_getCookie(key = "") {
	var value = "";
		$.ajax({
			url: 'index.php?page=structure&rex-api-call=gridblock_getCookie&key=' +key,
			success: function(data) { value = data; },
			async: false
		});
	return value;
}

//Copy: Cookie löschen/leeren
function gridblock_deleteCookie() {
	$.ajax({
		url: 'index.php?page=structure&rex-api-call=gridblock_deleteCookie',
		success: function(data) {},
		async: false
	});
}
</script>
