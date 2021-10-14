<?php
/*
	Redaxo-Addon Gridblock
	Verwaltung: default
	v0.8
	by Falko Müller @ 2021 (based on 0.1.0-dev von bloep)
*/

//Variablen deklarieren
$mode = rex_request('mode');
$id = intval(rex_request('id'));
$form_error = 0;

$_SESSION['as_sbeg_gridtemplatelist'] = (!isset($_SESSION['as_sbeg_gridtemplatelist'])) ? "" : $_SESSION['as_sbeg_gridtemplatelist'];


//Formular dieser Seite verarbeiten
if ($func == "save" && (isset($_POST['submit']) || isset($_POST['submit-apply'])) ):
	//Pflichtfelder prüfen
	$fields = array("f_title", "f_columns", "f_template");
		foreach ($fields as $field):
			$tmp = rex_post($field);
			$form_error = (empty($tmp)) ? 1 : $form_error;
		endforeach;
		
		
	if ($form_error):
		//Pflichtfelder fehlen
		echo rex_view::warning($this->i18n('a1620_entry_emptyfields'));
	else:
		//Eintrag speichern
		$db = rex_sql::factory();
		$db->setQuery("SELECT id FROM ".rex::getTable('1620_gridtemplates')); 
		$maxPrio = $db->getRows();
		
		$prio = rex_post('f_prio', 'int');
		if ($prio <= 0 || $prio > $maxPrio):
			$prio = $maxPrio+1;
		endif;
		
		$db = rex_sql::factory();
		$db->setTable(rex::getTable('1620_gridtemplates'));

		$db->setValue("title", 			rex_post('f_title'));
		$db->setValue("prio", 			$prio);
		$db->setValue("description", 	rex_post('f_description'));
		$db->setValue("columns", 		rex_post('f_columns', 'int'));
		$db->setValue("template", 		rex_post('f_template'));
		$db->setValue("preview", 		rex_post('f_preview'));

		if ($id > 0):
			$db->setWhere("id = '".$id."'");
			$dbreturn = $db->update();
			$lastID = $id;
				
			$form_error = (isset($_POST['submit-apply'])) ? 1 : $form_error;
		else:
			$dbreturn = $db->insert();
			$lastID = $db->getLastId();
		endif;

		if ($dbreturn):
			//gespeichert
			echo rex_view::info($this->i18n('a1620_entry_saved'));
			
			//Prioritäten korrigieren
			$dbp = rex_sql::factory();
			$dbp->setQuery("SELECT id FROM ".rex::getTable('1620_gridtemplates')." WHERE prio >= '".$prio."' AND id <> '".$lastID."' ORDER BY prio ASC"); 
			
			for ($i=0; $i < $dbp->getRows(); $i++):
				$prio++;
					$db = rex_sql::factory();
					$db->setQuery("UPDATE ".rex::getTable('1620_gridtemplates')." SET prio = '".$prio."' WHERE id = '".$dbp->getValue('id')."'");
				$dbp->next();
			endfor;

		else:
			//Fehler beim Speichern
			echo rex_view::warning($this->i18n('a1620_error'));
		endif;
	endif;
	
elseif ($func == "delete" && $id > 0):
	//Eintrag löschen - mit möglicher Prüfung auf Zuweisung
	$db = rex_sql::factory();
	$db->setQuery("SELECT id FROM ".rex::getTable('article_slice')." WHERE value17 like '%selectedTemplate\":\"".$id."\"%'");

	if ($db->getRows() <= 0):
		//löschen
		$dbp = rex_sql::factory();
		$dbp->setQuery("SELECT id FROM ".rex::getTable('1620_gridtemplates')); 
		$maxPrio = $dbp->getRows();

		$dbp = rex_sql::factory();
		$dbp->setQuery("SELECT prio FROM ".rex::getTable('1620_gridtemplates')." WHERE id = '".$id."'"); 
		$lastPrio = ($dbp->getRows() > 0) ? $dbp->getValue('prio') : 0;
		
		$db = rex_sql::factory();
		$db->setTable(rex::getTable('1620_gridtemplates'));		
		$db->setWhere("id = '".$id."'");
			
		if ($db->delete()):
			echo rex_view::info($this->i18n('a1620_entry_deleted'));
			
			//Prioritäten korrigieren
			if ($lastPrio > 0):
				$dbp = rex_sql::factory();
				$dbp->setQuery("SELECT id FROM ".rex::getTable('1620_gridtemplates')." WHERE prio >= '".$lastPrio."' ORDER BY prio ASC"); 
				
				for ($i=0; $i < $dbp->getRows(); $i++):
					$db = rex_sql::factory();
					$db->setQuery("UPDATE ".rex::getTable('1620_gridtemplates')." SET prio = '".$lastPrio."' WHERE id = '".$dbp->getValue('id')."'");
					$lastPrio++;
					$dbp->next();
				endfor;
			endif;
		else:
			echo rex_view::warning($this->i18n('a1620_error_deleted'));
		endif;
	else:
		//nicht löschen aufgrund gültiger Zuweisung
		echo rex_view::warning($this->i18n('a1620_entry_used'));
	endif;	
	
elseif ($func == "duplicate" && $id > 0):
	//Eintrag duplizieren
	$db = rex_sql::factory();
	$db->setQuery("SELECT * FROM ".rex::getTable('1620_gridtemplates')." WHERE id = '".$id."'"); 
	
	if ($db->getRows() > 0):
		$dbp = rex_sql::factory();
		$dbp->setQuery("SELECT id FROM ".rex::getTable('1620_gridtemplates')); 
		$maxPrio = $dbp->getRows();
	
		$dbe = $db->getArray();	//mehrdimensionales Array kommt raus
		$db = rex_sql::factory();
		$db->setTable(rex::getTable('1620_gridtemplates'));
		$db->setValue("prio", $maxPrio+1);
		
		foreach ($dbe[0] as $key=>$val):			
			if ($key == 'id') { continue; }
			if ($key == 'prio') { continue; }
			if ($key == 'title') { $val = a1620_duplicateName($val); }
			
			$db->setValue($key, $val);
		endforeach;
		
		$dbreturn = $db->insert();
	endif;

elseif ($func == "insert_default_templates"):
	//Beispieltemplates installieren
	$db = rex_sql::factory();
	$db->setQuery("SELECT id FROM ".rex::getTable('1620_gridtemplates'));
	
	if ($db->getRows() <= 0):
		rex_sql_util::importDump($this->getPath('install/install.sql'));
	endif;

endif;


//Formular oder Liste ausgeben
if ($func == "update" || $func == "insert" || $form_error == 1):
	//Formular ausgeben
	if (($mode == "update" || $func == "update") && $id > 0):
		$db = rex_sql::factory();
		$db->setQuery("SELECT * FROM ".rex::getTable('1620_gridtemplates')." WHERE id = '".$id."' LIMIT 0,1"); 
		$dbe = $db->getArray();	//mehrdimensionales Array kommt raus
	endif;
	
	//Std.vorgaben der Felder setzen
	if (!isset($dbe) || (is_array($dbe) && count($dbe) <= 0)):
		$dbe[0]['title'] = $dbe[0]['description'] = $dbe[0]["columns"] = $dbe[0]['template'] = $dbe[0]['preview'] = '';
	endif;
	//$dbe[0] = array_map('htmlspecialchars', $dbe[0]);
	
	//Insert-Vorgaben
	if ($mode == "insert" || $id <= 0):
		//$dbe[0]["date"] = time();
	endif;
	
	if ($form_error):
		//Formular bei Fehleingaben wieder befüllen
		$dbe[0]['id'] = $id;
		
		$dbe[0]["title"] = 			rex_post('f_title');
		$dbe[0]["description"] = 	rex_post('f_description');
		$dbe[0]["columns"] = 		rex_post('f_columns', 'int');
		$dbe[0]["template"] = 		rex_post('f_template');
		$dbe[0]["preview"] = 		rex_post('f_preview');

		$func = $mode;
	endif;
	

	//Beispiel-JSON holen
	$example = rex_file::get(rex_addon::get('gridblock')->getPath('data/example.json'));

	
	//Ausgabe: Formular (Update / Insert)
	?>

	<script type="text/javascript">jQuery(function() { jQuery('#f_title').focus(); });</script>
    <script id="json-example" type="text/template"><?php echo $example; ?></script>

    
    <form action="index.php?page=<?php echo $page; ?>" method="post" enctype="multipart/form-data">
    <!-- <input type="hidden" name="subpage" value="<?php echo $subpage; ?>" /> -->
    <input type="hidden" name="func" value="save" />
    <input type="hidden" name="id" value="<?php echo $dbe[0]['id']; ?>" />
	<input type="hidden" name="mode" value="<?php echo $func; ?>" />
    
    <section class="rex-page-section">
        <div class="panel panel-edit">
        
            <header class="panel-heading"><div class="panel-title"><?php echo $this->i18n('a1620_head_basics'); ?></div></header>
            
            <div class="panel-body">
            
                <legend><?php echo $this->i18n('a1620_subheader_bas1'); ?></legend>
                
    
                <dl class="rex-form-group form-group">
                    <dt><label for=""><?php echo $this->i18n('a1620_bas_title'); ?> *</label></dt>
                    <dd>
                        <input type="text" size="25" name="f_title" id="f_title" value="<?php echo aFM_maskChar($dbe[0]['title']); ?>" maxlength="100" class="form-control" />
                    </dd>
                </dl>
                
    
                <dl class="rex-form-group form-group">
                    <dt><label for=""><?php echo $this->i18n('a1620_bas_description'); ?></label></dt>
                    <dd>
                    	<input type="text" size="25" name="f_description" id="f_description" value="<?php echo aFM_maskChar($dbe[0]['description']); ?>" maxlength="200" class="form-control" />
                    </dd>
                </dl>

    
                <dl class="rex-form-group form-group">
                    <dt><label for=""><?php echo $this->i18n('a1620_bas_priority'); ?></label></dt>
                    <dd>
                        <input type="number" size="25" name="f_prio" id="f_prio" value="<?php echo intval($dbe[0]['prio']); ?>" maxlength="4" min="1" class="form-control" />
                    </dd>
                </dl>
                
                
                <dl class="rex-form-group form-group"><dt></dt></dl>
                
                
                <legend><?php echo $this->i18n('a1620_subheader_bas2'); ?></legend>
    
    
                <dl class="rex-form-group form-group">
                    <dt><label for=""><?php echo $this->i18n('a1620_bas_columns'); ?> *</label></dt>
                    <dd>
                        <input type="number" size="25" name="f_columns" id="f_columns" value="<?php echo intval($dbe[0]['columns']); ?>" maxlength="2" min="1" max="12" class="form-control" />
                    </dd>
                </dl>    
                
    
                <dl class="rex-form-group form-group">
                    <dt><label for=""><?php echo $this->i18n('a1620_bas_template'); ?> *</label></dt>
                    <dd>
                        <textarea name="f_template" rows="15" class="form-control rex-code" id="f_template"><?php echo aFM_maskChar($dbe[0]['template']); ?></textarea>
                        <span class="infoblock"><?php echo $this->i18n('a1620_bas_template_placeholder'); ?></span>                        
                    </dd>
                </dl>
                
    
                <dl class="rex-form-group form-group">
                    <dt><label for=""><?php echo $this->i18n('a1620_bas_preview'); ?></label></dt>
                    <dd>
                        <textarea name="f_preview" rows="15" class="form-control rex-code" id="f_preview"><?php echo aFM_maskChar($dbe[0]['preview']); ?></textarea>
                        <span class="infoblock"><a href="javascript:$('#f_preview').val($('#json-example').html());"><?php echo $this->i18n('a1620_bas_preview_example'); ?></a></span>
                    </dd>
                </dl>
                                                                  
            </div>
            
            
            <footer class="panel-footer">
                <div class="rex-form-panel-footer">
                    <div class="btn-toolbar">
                        <input class="btn btn-save rex-form-aligned" type="submit" name="submit" title="<?php echo $this->i18n('a1620_save'); ?>" value="<?php echo $this->i18n('a1620_save'); ?>" />
                        <?php if ($func == "update"): ?>
                        <input class="btn btn-save" type="submit" name="submit-apply" title="<?php echo $this->i18n('a1620_apply'); ?>" value="<?php echo $this->i18n('a1620_apply'); ?>" />
                        <?php endif; ?>
                        <input class="btn btn-abort" type="submit" name="submit-abort" title="<?php echo $this->i18n('a1620_abort'); ?>" value="<?php echo $this->i18n('a1620_abort'); ?>" />
                    </div>
                </div>
            </footer>
            
        </div>
    </section>
    
    </form>


<?php
else:
	//Übersichtsliste laden + ausgeben
	// --> wird per AJAX nachgeladen !!!
	
	$addpath = "index.php?page=".$page;
	?>
	
	
	<section class="rex-page-section">
		<div class="panel panel-default">
		
			<header class="panel-heading"><div class="panel-title"><?php echo $this->i18n('a1620_overview').' '.$this->i18n('a1620_default'); ?></div></header>  
			  
			<script type="text/javascript">
			jQuery(function() {
				//Ausblenden - Elemente
				jQuery('.search_options').hide();
				
				//Formfeld fokussieren
				jQuery('#s_sbeg').focus();
			
				//Liste - Filtern
				var params = 'page=<?php echo $page; ?>&subpage=load-defaultlist&sbeg=';
				var dst = '#ajax_jlist';
				
				jQuery('#db-order').click(function() {
					var btn = jQuery(this);
					btn.toggleClass('db-order-desc');
						if (btn.hasClass('db-order-desc')) { btn.attr('data-order', 'desc'); } else { btn.attr('data-order', 'asc'); }
					loadAJAX(params + getSearchParams(), dst, 0);
				});
				
				jQuery('#s_sbeg').keyup(function(){												loadAJAX(params + getSearchParams(), dst, 0);	});
				jQuery('#s_button').click(function(){											loadAJAX(params + getSearchParams(), dst, 0);	});
				jQuery('#s_resetsbeg').click(function(){		jQuery('#s_sbeg').val("");
																loadAJAX(params, dst, 0);	});
																
				jQuery(document).on('click', 'span.ajaxNav', function(){
					var navsite = jQuery(this).attr('data-navsite');
					loadAJAX(params + getSearchParams(), dst, navsite);
					jQuery("body, html").delay(150).animate({scrollTop:0}, 750, 'swing');
				});
				
				function getSearchParams()
				{	var searchparams = tmp = '';
						searchparams += encodeURIComponent(jQuery('#s_sbeg').val());								//Suchbegriff (param-Name wird in "var params" gesetzt
						searchparams += '&order=' + encodeURIComponent(jQuery('#db-order').attr('data-order'));		//Sortierrichtung asc|desc
					return searchparams;
				}
			});
			</script>
	
			<!-- Suchbox -->
			<table class="table table-striped addon_search" cellpadding="0" cellspacing="0">
			<tbody>
			<tr>
				<td class="td1" valign="middle">
				<?php
				$db = rex_sql::factory();
				$db->setQuery("SELECT id FROM ".rex::getTable('1620_gridtemplates'));
				
				if ($db->getRows() <= 0):
					echo '<div class="btn-group btn-group-xs"><a href="index.php?page='.$page.'&amp;func=insert_default_templates" class="btn btn-default">Beispieltemplates erstellen</a></div>';
				else:
					echo '&nbsp;';
				endif;
				?>
                </td>
				<td class="td2"><img src="/assets/addons/<?php echo $mypage; ?>/indicator.gif" width="16" height="16" border="0" id="ajax_loading" style="display:none;" /></td>
				<td class="td3">
				
					<div class="input-group">
						<input class="form-control sbeg" type="text" name="s_sbeg" id="s_sbeg" maxlength="50" value="<?php echo aFM_maskChar($_SESSION['as_sbeg_gridtemplatelist']); ?>" placeholder="<?php echo $this->i18n('a1620_search_keyword'); ?>">
						<span class="input-group-btn">
							<a class="btn btn-popup form-control-btn" title="<?php echo $this->i18n('a1620_search_reset'); ?>" id="s_resetsbeg"><i class="rex-icon fa-close"></i></a>
						</span>
					</div>
					<input name="submit" type="button" value="<?php echo $this->i18n('a1620_search_submit'); ?>" class="button" id="s_button" style="display:none" />
					
				</td>
			</tr>
			</tbody>
			</table>        
	
	
			<!-- Liste -->
			<table class="table table-striped table-hover">
			<thead>
			<tr>
				<th class="rex-table-icon"><a href="<?php echo $addpath; ?>&func=insert" accesskey="a" title="<?php echo $this->i18n('a1620_new'); ?> [a]"><i class="rex-icon rex-icon-add-template"></i></a></th>
				<th class="rex-table-id">ID</th>
				<th><?php echo $this->i18n('a1620_bas_list_name'); ?></th>
				<th><?php echo $this->i18n('a1620_bas_list_columns'); ?></th>
				<th><?php echo $this->i18n('a1620_bas_list_preview'); ?></th>
                <th><?php echo $this->i18n('a1620_bas_list_priority'); ?> <a class="db-order db-order-asc" id="db-order" data-order="asc"><i class="rex-icon fa-sort"></i></a></th>
				<th class="rex-table-action" colspan="3"><?php echo $this->i18n('a1620_statusfunc'); ?></th>
			</tr>
			</thead>
	
			<tbody id="ajax_jlist">
			<script type="text/javascript">jQuery(function(){ jQuery('#s_button').trigger('click'); });</script>
			</tbody>
			</table>
	
	
		</div>
	</section>

<?php
endif;
?>