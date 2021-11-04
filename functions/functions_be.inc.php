<?php
/*
	Redaxo-Addon Gridblock
	Backend-Funktionen (Addon + Modul)
	v1.0
	by Falko Müller @ 2021 (based on 0.1.0-dev von bloep)
*/

//aktive Session prüfen


//globale Variablen


//Funktionen
//prüft die Bezeichnung auf Vorhandensein
function a1620_duplicateName($str)
{	if (!empty($str)):
		$db = rex_sql::factory();
		$db->setQuery("SELECT id, title FROM ".rex::getTable('1620_gridtemplates')." WHERE title = '".aFM_maskSql($str)."'"); 
		
		if ($db->getRows() > 0):
			$str = $str.rex_i18n::msg('a1620_copiedname');
			$str = a1620_duplicateName($str);
		endif;
	endif;

	return $str;
}


//erstellt die Layoutvorschau
function a1620_generatePreview($preview, $id = 0, $columns = 0, $title = "", $desc = "", $selected = false, $setid = false)
{	global $a1620_mypage;

	//Vorgaben einlesen/setzen
	$id = intval($id);
	$columns = intval($columns);
	
	$config = rex_config::get($a1620_mypage, 'config');						//Addon-Konfig einladen
	
	
	$op = '';
	if ($id > 0 && $columns > 0):
		$selected = ($selected) ? 'selected' : '';
		$setid = ($setid) ? 'id="gridblock-template'.$id.'"' : '';
		
		$data = $setid.' data-id="'.$id.'" data-columns="'.$columns.'"';

		if (!empty($preview) && is_array($preview)):
			$count = intval($preview['totalcolumns']);
			$cols = $preview['columns'];
			
			$preview = ''; $colnames = array();
			for ($p=0; $p < $count; $p++):
				$w = intval($cols[$p]['width']);
				$t = $cols[$p]['title'];
					array_push($colnames, $t);
					
				$t = (@$config['hidepreviewcoltitles'] != 'checked') ? $t : '';
				$preview .= '<div style="width: '.$w.'%"><span>'.$t.'</span></div>';
			endfor;
			$op = (!empty($preview)) ? '<div class="gridblock-preview '.$selected.'" title="'.$desc.'" '.$data.' data-colnames="'.urlencode(json_encode($colnames)).'">'.$preview.'</div>' : '';
			
		elseif (!empty($title)):
			$op = '<div class="gridblock-preview gridblock-previewtext '.$selected.'" title="'.$desc.'" '.$data.' data-colnames=""><span>'.$title.'</span></div>';
		endif;
	endif;
	
	return $op;
}
?>