<?php
/*
	Redaxo-Addon Gridblock
	Ein-/Ausgabesteuerung der Inhaltsmodule
	v1.0
	by Falko Müller @ 2021 (based on 0.1.0-dev von bloep)
*/

class rex_article_content_gridblock extends rex_article_content_editor {

    private $values = [];					//array


    public function getModuleEdit($addModuleID, $colID, $uID, $rexVars = array())
    {	$config = rex_addon::get('gridblock')->getConfig('config');
		
        $this->setEval(true);
        $this->setMode('edit');
        $this->setFunction('add');

        $MOD = rex_sql::factory();
        $MOD->setQuery('SELECT `key`, `input` FROM '.rex::getTablePrefix().'module WHERE id = "'.$addModuleID.'"');

        if ($MOD->getRows() != 1):
			$slice_content = rex_view::warning(rex_i18n::msg('module_doesnt_exist').' (ID: '.$addModuleID.')');
        else:
            $values = $this->values;
            foreach ($values as $key => $val):
                if (is_array($val)):
                    $values[$key] = json_encode($val);
                endif;
            endforeach;
			
			$rexVars = (empty($rexVars) && isset($_SESSION['gridRexVars'])) ? $_SESSION['gridRexVars'] : $rexVars;
			//dump($rexVars);
						
			//REX-MODULE-VARS erweitern
			$rexVars['moduleID'] = $addModuleID;
			$rexVars['moduleKEY'] = $MOD->getValue('key');
			
			
			//VALUE-Ersetzungen vorbereiten
            $initDataSql = rex_sql::factory();
            $initDataSql
                ->setValues($values, $uID)
                ->setValue('module_id', $addModuleID)
                ->setValue('ctype_id', $this->ctype);


            //Wird benötigt, um die Mblock-SQL-Anfrage abzufangen und die Antwort zu fälschen
            rex::setProperty('sql_fake_result', $values);
				$moduleInput = rex_gridblock_var_replacer::replaceModuleVars($MOD->getValue('input'), $rexVars);								//zuerst die RexVars ersetzen

				//$moduleInput = $this->replaceVars($initDataSql, $MOD->getValue('input'));
				$moduleInput = $this->replaceVars($initDataSql, $moduleInput);																	//jetzt die restlichen Vars/Values ersetzen
				
				$slice_content = $this->getStreamOutput('module/' . $addModuleID . '/input', $moduleInput);
            rex::removeProperty('sql_fake_result');

            $slice_content = rex_gridblock_var_replacer::replaceVars($colID, $slice_content, $uID);
		endif;
		
		
		//Disabled auswerten
		$disabled = ($addModuleID > 0 && !rex::getUser()->getComplexPerm('modules')->hasPerm($addModuleID)) ? true : false;
		
		$op = ($disabled) ? '<div class="gridblock-module-disabled">Keine Editiererlaubnis</div>' : '';
		
		//Modul-Edit ausgeben
		$op .= '<div id="gridblockModuleContent'.$uID.'" class="column-input">';
			$op .= $slice_content;
			
			if (@$config['plusbuttonfornewblock'] != 'checked'):
				$op .= '<div class="clearfix"></div>';
				$op .= '<div class="addmodule"><a class="btn btn-default btn-block btn-addgridmodule" title="'.rex_i18n::msg('a1620_mod_add_modul').'" data-colid="'.$colID.'" data-uid="'.$uID.'"><i class="fa fa-plus"></i>'.rex_i18n::msg('a1620_mod_add_modul').'</a></div>';
			endif;
		$op .= '</div>';
		
        return $op;
    }
	
	
    public static function addModuleSelector($colID, $uID = "")
	{	$cnt = "";
		$colID = intval($colID);


		if (isset($_SESSION['gridAllowedModules']) && $colID > 0 && !empty($uID)):
			//Moduleselector anzeigen
			$cnt .= '<div class="dropdown btn-block">';
				$cnt .= '<a class="btn btn-default btn-block btn-choosegridmodul dropdown-toggle" data-toggle="dropdown" title="'.rex_i18n::msg('a1620_mod_choose_modul').'"><i class="fa fa-plus"></i>'.rex_i18n::msg('a1620_mod_choose_modul').' <span class="caret"></span></a>';
				
				$cnt .= '<ul class="dropdown-menu btn-block gridblock-moduleselector" role="menu" data-colid="'.$colID.'" data-uid="'.$uID.'">';
					foreach ($_SESSION['gridAllowedModules'] as $id => $module):
						if (!rex::getUser()->getComplexPerm('modules')->hasPerm($id)) { continue; }					//Modulrechte prüfen
						
						$modName = aFM_maskChar($module['name']);
						$cnt .= '<li><a data-modid="'.$id.'" data-modname="'.$modName.'">'.$modName.'</a></li>';
					endforeach;
				$cnt .= '</ul>';
				
			$cnt .= '</div>';
		endif;
		
		//ExtensionPoint zur nachträglichen Änderung des Modulselektors
		$cnt = rex_extension::registerPoint(new rex_extension_point('GRIDBLOCK_MODULESELECTOR_ADD', $cnt, [
			'colid' => $colID,
			'uid' => $uID,
			'allowedmodules' => $_SESSION['gridAllowedModules'],
		]));
		
		return $cnt;
	}	
	
	
    public static function getModuleSelector($colID, $selectedModuleID = 0, $uID = "")
    {	$config = rex_addon::get('gridblock')->getConfig('config');
	
		$cnt = "";
		$colID = intval($colID);
		$selectedModuleID = intval($selectedModuleID);
		
		$uID = (empty($uID)) ? 'GBS'.sha1(uniqid().str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')) : $uID;					//eine UID mit Buchstaben wird benötigt, damit die MBlock-JS-Ersetzungen nicht ausgehebelt werden
		
		
		if (isset($_SESSION['gridAllowedModules']) && $colID > 0 && !empty($uID)):
			//Modulberechtigung prüfen und Klasse entsprechend setzen
			$disabled = ($selectedModuleID > 0 && !rex::getUser()->getComplexPerm('modules')->hasPerm($selectedModuleID)) ? true : false;
			$disabledCSS = ($disabled) ? 'gridblock-slice-disabled' : '';
		
			//Inhaltsblock-Wrapper setzen
			$cnt .= '<div id="gridblockColumnSlice'.$uID.'" class="column-slice '.$disabledCSS.'">';				//Wrapper-Block (Slice)
			
				$cnt .= '<div class="column-slice-functions">';
				
					$modName = @$_SESSION['gridAllowedModules'][$selectedModuleID]['name'];
						$modName = (empty($modName)) ? '[ID: '.$selectedModuleID.']' : $modName;
						$showModInfo = ($selectedModuleID > 0 || $disabled) ? 'style="display: block;"' : 'style="display: none;"';
					
					$cnt .= '<input type="hidden" name="REX_INPUT_VALUE[19]['.$colID.'][\''.$uID.'\']" id="gridModuleSelect'.$uID.'" value="'.$selectedModuleID.'" data-colid="'.$colID.'" data-uid="'.$uID.'" />';
					$cnt .= '<div class="form-control gridblock-moduleinfo" '.$showModInfo.'>'.$modName.'</div>';
					$cnt .= (!$disabled && $selectedModuleID <= 0) ? self::addModuleSelector($colID, $uID) : '';
				
					$cnt .= '<div class="column-slice-sorter">';
						if (@$config['plusbuttonfornewblock'] == 'checked'):
							$cnt .= '<div class="btn-group btn-group-xs btn-group-add"><a class="btn btn-default btn-addgridmodule" title="'.rex_i18n::msg('a1620_mod_add_modul').'" data-colid="'.$colID.'" data-uid="'.$uID.'"><i class="rex-icon rex-icon-add-module"></i></a></div>';
						endif;
					
						$cnt .= '<div class="btn-group btn-group-xs btn-group-delete"><a class="btn btn-delete" title="'.rex_i18n::msg('a1620_mod_delete_modul').'"><i class="rex-icon rex-icon-delete"></i></a></div>';
						
						$cnt .= '<div class="btn-group btn-group-xs">';
							$cnt .= '<a class="btn btn-move btn-move-up" title="'.rex_i18n::msg('a1620_mod_move_modul_up').'"><i class="rex-icon rex-icon-up"></i></a>';
							$cnt .= '<a class="btn btn-move btn-move-down" title="'.rex_i18n::msg('a1620_mod_move_modul_down').'"><i class="rex-icon rex-icon-down"></i></a>';
						$cnt .= '</div>';
						
						$cnt .= '<div class="btn-group btn-group-xs btn-group-drag"><a class="btn btn-move btn-move-drag" title="'.rex_i18n::msg('a1620_mod_move_modul_drag').'"><i class="rex-icon fa-arrows"></i></a></div>';
					$cnt .= '</div>';
							
				$cnt .= '</div>';
				
			$cnt .= ($selectedModuleID > 0) ? '' : '</div>';														//Wrapper-Block beenden, falls noch kein Modul gewählt wurde
		endif;
		
		return $cnt;
	}	
	

    public function setValues($values, $uID)
    {
        if (empty($values)):
            $this->values = rex_gridblock::getBlankValues();
            return;
        endif;
		
        $this->values = [];
		
        if (isset($values['VALUE'])):
            foreach ($values['VALUE'] as $i => $value):
                $this->values['value'.$i] = $value;
            endforeach;
        endif;
		
        if (isset($values['MEDIA'])):
            foreach ($values['MEDIA'] as $i => $value):
                $this->values['media'.$i] = $value;
            endforeach;
        endif;
		
        if (isset($values['MEDIALIST'])):
            foreach ($values['MEDIALIST'] as $i => $value):
                $this->values['medialist'.$i] = $value;
            endforeach;
        endif;
		
        if (isset($values['LINK'])):
            foreach ($values['LINK'] as $i => $value):
                $this->values['link'.$i] = $value;
            endforeach;
        endif;
		
        if (isset($values['LINKLIST'])):
            foreach ($values['LINKLIST'] as $i => $value):
                $this->values['linklist'.$i] = $value;
            endforeach;
        endif;
		
		/*
		echo "<br>----- setValues : Start -----<br>";
		dump($values);
		dump($this->values);
		echo "----- setValues : Ende -----<br><br>";
		*/
    }


    public function getModuleOutput($moduleID, $uID, $rexVars = array())
	{	$this->setEval(true);
        $this->setMode('edit');
		
		$MOD = rex_sql::factory();
		$MOD->setQuery('SELECT `key`, `output` FROM '.rex::getTablePrefix().'module WHERE id="'.$moduleID.'"');
		
		if ($MOD->getRows() != 1 && rex::isBackend()):
			$slice_content = rex_view::warning(rex_i18n::msg('module_doesnt_exist').' (ID: '.$moduleID.')');
		else:
			foreach($this->values as $i => $value):
				if (is_array($value)):
					$this->values[$i] = json_encode($value);
				endif;
			endforeach;
			
			//dump($rexVars);
						
			//REX-MODULE-VARS erweitern
			$rexVars['moduleID'] = $moduleID;
			$rexVars['moduleKEY'] = $MOD->getValue('key');
			
			
			//VALUE-Ersetzungen vorbereiten
			$initDataSql = rex_sql::factory();
			$initDataSql
				->setValues($this->values, $uID)
				->setValue('module_id', $moduleID)
				->setValue('ctype_id', $this->ctype);

				
			//Variablen ersetzen
			$op = $MOD->getValue('output');
				$op = rex_gridblock_var_replacer::replaceModuleVars($op, $rexVars);																//zuerst die RexVars ersetzen
				
			$op = $this->replaceVars($initDataSql, $op);
			$slice_content = $this->getStreamOutput('module/'.$moduleID.'/output', $op);
		endif;
		
        return $slice_content;
    }
}
?>