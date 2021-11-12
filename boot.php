<?php
/*
	Redaxo-Addon Gridblock
	Boot (weitere Konfigurationen & Einbindung)
	v1.0
	by Falko Müller @ 2021 (based on 0.1.0-dev von bloep)
*/

//Variablen deklarieren
$mypage = $this->getProperty('package');


//Userrechte prüfen
$isAdmin = ( is_object(rex::getUser()) AND (rex::getUser()->hasPerm($mypage.'[admin]') OR rex::getUser()->isAdmin()) ) ? true : false;


//Addon Einstellungen
$config = rex_addon::get($mypage)->getConfig('config');			//Addon-Konfig einladen


//Funktionen einladen/definieren
//Global für Backend+Frontend
global $a1620_mypage;
$a1620_mypage = $mypage;

global $a1620_darkmode;
$a1620_darkmode = (rex_string::versionCompare(rex::getVersion(), '5.13.0-dev', '>=')) ? true : false;


require_once(rex_path::addon($mypage)."/functions/functions.inc.php");


//Backend
if (rex::isBackend()):
	require_once(rex_path::addon($mypage)."/functions/functions_be.inc.php");
	
	if (rex_be_controller::getCurrentPage() === 'content/edit'):
		rex_sql::setFactoryClass('rex_sql_gridblock');
		
		$values = isset($_POST['REX_INPUT_VALUE']) ? $_POST['REX_INPUT_VALUE'] : null;
		
		if ($values):
		
			foreach ($values as $colID => $slices):
				//erste Ebene (columnID) durchlaufen	
				foreach ($slices as $uID => $data):
					//zweite Ebene (uniqeID = Slices) durchlaufen
					if (isset($data['VALUE'])):
					
						echo "\nSlice: $uID\n";
						print_r($data);					
					
						foreach ($data['VALUE'] as $i => $value):
							//Eingabedaten durchlaufen und _MBLOCK korrigieren	
							if (strrpos($i, '_MBLOCK') !== false):
								$id = str_replace('_MBLOCK', '', $i);
								
								$values[$colID][$uID]['VALUE'][$id] = (isset($values[$colID][$uID]['VALUE'][$id]) ? $values[$colID][$uID]['VALUE'][$id] + $value : $value);
								unset($values[$colID][$uID]['VALUE'][$i]);
							endif;
						endforeach;
						
					endif;
				endforeach;
			endforeach;
			
			//korrigierte Vars zurückgeben
			$_POST['REX_INPUT_VALUE'] = $values;
			$_REQUEST['REX_INPUT_VALUE'] = $values;
			
			/*
			echo "\n\$_POST: $uID\n";
			print_r($_POST);
			*/	
			
		endif;
		
	endif;
	
	if (rex::getUser()):
		//AJAX anbinden
		$ajaxPages = array('load-defaultlist');
			if (rex_be_controller::getCurrentPagePart(1) == $mypage && in_array(rex_request('subpage', 'string'), $ajaxPages)):
				rex_extension::register('OUTPUT_FILTER', 'aFM_bindAjax');
			endif;	
	
	endif;
endif;



// Assets im Backend einbinden (z.B. style.css) - es wird eine Versionsangabe angehängt, damit nach einem neuen Release des Addons die Datei nicht aus dem Browsercache verwendet wird
rex_view::addCssFile($this->getAssetsUrl('style.css?v=' . $this->getVersion()));
if ($a1620_darkmode) { rex_view::addCssFile($this->getAssetsUrl('style-darkmode.css')); }

rex_view::addJsFile($this->getAssetsUrl('sortable.min.js?v=' . $this->getVersion()));
rex_view::addJsFile($this->getAssetsUrl('script.js?v=' . $this->getVersion()));
?>