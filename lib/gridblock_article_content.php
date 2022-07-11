<?php
/*
	Redaxo-Addon Gridblock
	Ein-/Ausgabesteuerung der Inhaltsmodule
	v1.1.3
	by Falko Müller @ 2021-2022 (based on 0.1.0-dev von bloep)
*/

class rex_article_content_gridblock extends rex_article_content_editor {

    private $values = [];					//array


	public function __construct()
	{	if (!rex::getUser()) { self::deleteCookie(); }
	}
	

    //public function getModuleEdit($addModuleID = 0, $colID = 0, $uID, $rexVars = array(), $copyID = "", $copyCOL = 0, $copyCOL = 0, $copySLID = 0)
	public function getModuleEdit($addModuleID = 0, $colID = 0, $uID, $rexVars = array(), $action = "")
    {	$config = rex_addon::get('gridblock')->getConfig('config');
	
		rex_gridblock::isBackend(true);				//set isBackend=true in module edit
	
        $this->setEval(true);
        $this->setMode('edit');
        $this->setFunction('add');

        $MOD = rex_sql::factory();
        $MOD->setQuery('SELECT `key`, `input` FROM '.rex::getTablePrefix().'module WHERE id = "'.$addModuleID.'"');

        if ($MOD->getRows() != 1):
			$slice_content = rex_view::warning(rex_i18n::msg('module_doesnt_exist').' (ID: '.$addModuleID.')');
        else:
			//REX-MODULE-VARS holen
			$rexVars = (empty($rexVars) && isset($_SESSION['gridRexVars'])) ? $_SESSION['gridRexVars'] : $rexVars;
				//REX-MODULE-VARS erweitern
				$rexVars['moduleID'] = $addModuleID;
				$rexVars['moduleKEY'] = $MOD->getValue('key');
			//dump($rexVars);
		
		
			//VALUES aus kopiertem Gridblock-Slice auslesen
			$cook = rex_var::toArray(rex_article_content_gridblock::getCookie());
				$copUID = @$cook['uid'];
				$copCOLID = @intval($cook['colid']);
				$copSLID = @intval($cook['sliceid']);
			//dump($cook);
			

			$useCopy = false;
			if ($action == 'copy' && self::checkCopyAvailable($copUID, $copCOLID, $copSLID)):
				$useCopy = true;
				
				rex_sql::setFactoryClass('rex_sql_gridblock');
				
				$db = rex_sql::factory();
				$db->setQuery("SELECT value".$copCOLID." FROM ".rex::getTable("article_slice")." WHERE id = '".$copSLID."' LIMIT 0,1");
			
				$copValues = rex_var::toArray($db->getValue('value'.$copCOLID));
				$copValues = $copValues[$copUID];
				$this->setValues($copValues, $uID);
			endif;
			
		
			//VALUES aufbereiten (entweder alle aus Redaxo-Slice oder nur aus dem kopierten GB-Slice)
            $values = $this->values;
            foreach ($values as $key => $val):
                if (is_array($val)):
                    $values[$key] = json_encode($val);
                endif;
            endforeach;

			
			//VALUE-Ersetzungen vorbereiten
            $initDataSql = rex_sql::factory();
            $initDataSql
                ->setValues($values, $uID)
                ->setValue('module_id', $addModuleID)
                ->setValue('ctype_id', $this->ctype);


            //MBlock-SQL-Anfrage abzufangen und Antwort fälschen (MBlock lädt sich normalerweise alle Inhalte aus article_slice selbst)
            rex::setProperty('sql_fake_result', $values);
				$moduleInput = rex_gridblock_var_replacer::replaceModuleVars($MOD->getValue('input'), $rexVars);								//zuerst die RexVars ersetzen
				$moduleInput = $this->replaceVars($initDataSql, $moduleInput);																	//jetzt die restlichen Vars/Values ersetzen (über article_content_base)
				
				$slice_content = $this->getStreamOutput('module/' . $addModuleID . '/input', $moduleInput);
            rex::removeProperty('sql_fake_result');

			
			//Inhalte ersetzen
			$slice_content = rex_gridblock_var_replacer::replaceVars($colID, $slice_content, $uID, $rexVars);									//alle Inputs mit UID + COLID erweitern
		endif;
		
		
		//Plus-Button vorbereiten
		$plusBtn = (@$config['plusbuttonfornewblock'] != 'checked') ? '<div class="addmodule"><a class="btn btn-default btn-block btn-addgridmodule" title="'.rex_i18n::msg('a1620_mod_add_modul').'" data-colid="'.$colID.'" data-uid="'.$uID.'"><i class="fa fa-plus"></i>'.rex_i18n::msg('a1620_mod_add_modul').'</a></div>' : '';
		
		
		//Disabled auswerten																													//Hinweis: Modulcontent muss wegen Speicherung immer vorhanden sein (display:none) !!!
		$disabled = ($addModuleID > 0 && !rex::getUser()->getComplexPerm('modules')->hasPerm($addModuleID)) ? true : false;
		
		$op = ($disabled) ? '<div class="gridblock-module-disabled">'.rex_i18n::msg('a1620_mod_noeditpermission').$plusBtn.'</div>' : '';
		
		//Modul-Edit ausgeben
		$op .= '<div id="gridblockModuleContent'.$uID.'" class="column-input">';
			$op .= $slice_content;
			$op .= (@$config['plusbuttonfornewblock'] != 'checked') ? '<div class="clearfix"></div>'.$plusBtn : '';
		$op .= '</div>';
		
        return $op;
    }
	
	
    public static function addModuleSelector($colID, $uID = "")
	{	$cnt = $cook = "";
		$colID = intval($colID);
		$rexVars = (isset($_SESSION['gridRexVars'])) ? $_SESSION['gridRexVars'] : array();
		

		if (isset($_SESSION['gridAllowedModules']) && $colID > 0 && !empty($uID)):
			//Moduleselector anzeigen
			$cnt .= '<div class="dropdown btn-block">';
				$cnt .= '<a class="btn btn-default btn-block btn-choosegridmodul dropdown-toggle" data-toggle="dropdown" title="'.rex_i18n::msg('a1620_mod_choose_modul').'"><i class="fa fa-plus"></i>'.rex_i18n::msg('a1620_mod_choose_modul').' <span class="caret"></span></a>';
				
				$cnt .= '<ul class="dropdown-menu btn-block gridblock-moduleselector" role="menu" data-colid="'.$colID.'" data-uid="'.$uID.'">';
				
					//kopiertes Element prüfen und der Auswahl hinzufügen
					$cook = rex_var::toArray(rex_article_content_gridblock::getCookie());
						$copUID = @$cook['uid'];
						$copCOLID = @intval($cook['colid']);
						$copSLID = @intval($cook['sliceid']);
						$copMODID = @intval($cook['modid']);
					
					if (self::checkCopyAvailable($copUID, $copCOLID, $copSLID) && $copMODID > 0 && rex::getUser()->getComplexPerm('modules')->hasPerm($copMODID)):
						$module = @$_SESSION['gridAllowedModules'][$copMODID];
						
						$modName = aFM_maskChar($module['name']);
						$cnt .= '<li class="gridblock-cutncopy-insert"><a data-copyid="'.$copUID.'" data-modid="'.$copMODID.'" data-modname="'.$modName.'">'.str_replace(array("###modname###", "###modid###"), array($modName, $copMODID), rex_i18n::rawmsg('a1620_mod_copy_insertmodul')).'</a></li>';
					endif;
				
				
					//alle zulässigen Module der Auswahl hinzufügen
					foreach ($_SESSION['gridAllowedModules'] as $id => $module):
						if (!rex::getUser()->getComplexPerm('modules')->hasPerm($id)) { continue; }											//Modulrechte prüfen
						
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
			'copiedmodule' => $cook,
			'rexvars' => $rexVars,
		]));
		
		return $cnt;
	}	
	
	
    public static function getModuleSelector($colID, $selectedModuleID = 0, $uID = "", $selectedModuleSTATUS = 1)
    {	$config = rex_addon::get('gridblock')->getConfig('config');
	
		$cnt = "";
		$colID = intval($colID);
		$selectedModuleID = intval($selectedModuleID);
		$selectedModuleSTATUS = intval($selectedModuleSTATUS);
			$moduleStatusClassOFF = (!$selectedModuleSTATUS) ? 'rex-offline' : '';
			$moduleStatusIconOFF = (!$selectedModuleSTATUS) ? 'fa-eye-slash' : '';
		
		$uID = (empty($uID)) ? self::createUID() : $uID;					//eine UID mit Buchstaben wird benötigt, damit die MBlock-JS-Ersetzungen nicht ausgehebelt werden
		
		
		if (isset($_SESSION['gridAllowedModules']) && $colID > 0 && !empty($uID)):
			//Modulberechtigung prüfen und Klasse entsprechend setzen
			$disabled = ($selectedModuleID > 0 && !rex::getUser()->getComplexPerm('modules')->hasPerm($selectedModuleID)) ? true : false;
			$disabledCSS = ($disabled) ? 'gridblock-slice-disabled' : '';
			

			//Inhaltsblock-Wrapper setzen
			$cnt .= '<div id="gridblockColumnSlice'.$uID.'" class="column-slice '.$disabledCSS.'" data-uid="'.$uID.'">';				//Wrapper-Block (GB-Slice)
			
				$cnt .= '<div class="column-slice-functions">';
				
					$modName = @$_SESSION['gridAllowedModules'][$selectedModuleID]['name'];
						$modName = (empty($modName)) ? '[ID: '.$selectedModuleID.']' : $modName;
						$showModInfo = ($selectedModuleID > 0 || $disabled) ? 'style="display: block;"' : 'style="display: none;"';
					
					$cnt .= '<input type="hidden" name="REX_INPUT_VALUE[19]['.$colID.'][\''.$uID.'\'][id]" id="gridModuleSelect'.$uID.'" value="'.$selectedModuleID.'" data-colid="'.$colID.'" data-uid="'.$uID.'" />';
					$cnt .= '<input type="hidden" name="REX_INPUT_VALUE[19]['.$colID.'][\''.$uID.'\'][status]" id="gridModuleStatus'.$uID.'" value="'.$selectedModuleSTATUS.'" />';
					
					$cnt .= '<div class="form-control gridblock-moduleinfo" '.$showModInfo.'>'.$modName.'</div>';
					$cnt .= (!$disabled && $selectedModuleID <= 0) ? self::addModuleSelector($colID, $uID) : '';						//nur ausgeben, wenn noch nicht gespeichert
				
					$cnt .= '<div class="column-slice-sorter">';
						//ADD-Button
						if (@$config['plusbuttonfornewblock'] == 'checked'):
							$cnt .= '<div class="btn-group btn-group-xs btn-group-add"><a class="btn btn-default btn-addgridmodule" title="'.rex_i18n::msg('a1620_mod_add_modul').'" data-colid="'.$colID.'" data-uid="'.$uID.'"><i class="rex-icon rex-icon-add-module"></i></a></div>';
						endif;
											
						if (!$disabled):
							//Editierbuttons anzeigen, sofern Modul bearbeitbar
							//DELETE-Button
							$cnt .= '<div class="btn-group btn-group-xs btn-group-delete"><a class="btn btn-delete" title="'.rex_i18n::msg('a1620_mod_delete_modul').'"><i class="rex-icon rex-icon-delete"></i></a></div>';
							
							//STATUS-Button
							$cnt .= '<div class="btn-group btn-group-xs btn-group-status"><a class="btn btn-default btn-status rex-online '.$moduleStatusClassOFF.'" title="'.rex_i18n::msg('a1620_mod_status_modul').'"><i class="rex-icon fa-eye '.$moduleStatusIconOFF.'"></i></a></div>';
							
							//COPY-Button (nur anzeigen, wenn Inhaltsblock bereits einmal gepsiechert wurde)
							$db = rex_sql::factory();
							$db->setQuery('SELECT id FROM '.rex::getTablePrefix().'article_slice WHERE value'.$colID.' like "%'.$uID.'%"');
							
							$iscopied = (rex_article_content_gridblock::getCookie('uid') == $uID) ? 'gridblock-iscopied' : '';
							$cnt .= ($db->getRows() > 0) ? '<div class="btn-group btn-group-xs btn-group-copy"><a class="btn btn-default btn-copy btn-status '.$iscopied.'" title="'.rex_i18n::msg('a1620_mod_copy_modul').'" data-colid="'.$colID.'" data-uid="'.$uID.'" data-modid="'.$selectedModuleID.'" data-modstatus="'.$selectedModuleSTATUS.'"><i class="rex-icon fa-copy"></i></a></div>' : '';
						endif;
							
						//MOVE-Buttons
						$cnt .= '<div class="btn-group btn-group-xs">';
							$cnt .= '<a class="btn btn-move btn-move-up" title="'.rex_i18n::msg('a1620_mod_move_modul_up').'"><i class="rex-icon rex-icon-up"></i></a>';
							$cnt .= '<a class="btn btn-move btn-move-down" title="'.rex_i18n::msg('a1620_mod_move_modul_down').'"><i class="rex-icon rex-icon-down"></i></a>';
						$cnt .= '</div>';
						
						$cnt .= '<div class="btn-group btn-group-xs btn-group-drag"><a class="btn btn-move btn-move-drag" title="'.rex_i18n::msg('a1620_mod_move_modul_drag').'"><i class="rex-icon fa-arrows"></i></a></div>';
						
					$cnt .= '</div>';
							
				$cnt .= '</div>';
				
			$cnt .= ($selectedModuleID > 0) ? '' : '</div>';																	//Wrapper-Block (GB-Slice) beenden, falls kein Modul gewählt wurde
		endif;
		
		return $cnt;
	}
	
	
    public static function createUID()
	{	$uid = 'GBS'.sha1(uniqid().str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'));				//GBS-Kürzel ist wichtig für Suche/Ersetzen
	
		return $uid;
	}	
	

    public function setValues($values, $uID = "")
    {	if (empty($values)):
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
		
		
		if ($MOD->getRows() != 1):
			$slice_content = (rex::isBackend()) ? rex_view::warning(rex_i18n::msg('module_doesnt_exist').' (ID: '.$moduleID.')') : '';
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
		
		unset($MOD);
		
        return $slice_content;
    }


	public static function checkCopyAvailable($copUID = "", $copCOLID = 0, $copSLID = 0)
	{	$return = false;
	
		if (!empty($copUID) && $copCOLID > 0 && $copSLID > 0):
			$db = rex_sql::factory();
			$db->setQuery("SELECT value".$copCOLID." FROM ".rex::getTable("article_slice")." WHERE id = '".$copSLID."' LIMIT 0,1");
			
			if ($db->getRows() > 0):
				$copValues = rex_var::toArray($db->getValue('value'.$copCOLID));
				$return = (isset($copValues[$copUID])) ? true : false;
			endif;
		endif;
		
		return $return;
	}
	
	
	public static function getCookieName()
	{	global $a1620_mypage;
		return 'rex_'.$a1620_mypage.'_cutncopy';
	}


	public static function deleteCookie()
	{	setcookie(self::getCookieName(), '', time()-3600);
	}


	public static function setCookie($value)
	{	$value = (!is_array($value)) ? array('value' => $value) : $value;
        setcookie(self::getCookieName(), json_encode($value), time()+60*60*24);
    }


	public static function getCookie($key = "")
	{	$cookie = @json_decode(rex_request::cookie(self::getCookieName(), 'string', ''), true);
		
		if (!empty($key) && is_string($key)):
			if (isset($cookie[$key])):
				return $cookie[$key];
			endif;
			
			return "";
		endif;
		
		return json_encode($cookie);
	}
	
}
?>