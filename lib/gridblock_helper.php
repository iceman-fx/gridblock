<?php
/*
	Redaxo-Addon Gridblock
	Helper-Funktionen für contentsettings
	v1.0.6
	by Falko Müller @ 2021-2022 (based on 0.1.0-dev von bloep)
*/

class rex_gridblock_helper
{	
    public static function getTemplateClasses($cs, $options = array(), $with = false)
	{	/*
		Liest die Werte der definierten Template-Settings aus und gibt diese als CSS-Klassen-Aufzählung aus.
		
		Aufruf:
		echo rex_gridblock_helper::getTemplateClasses($cs, array('gutter', 'padding', 'width', 'class'));
		echo rex_gridblock_helper::getTemplateClasses($cs, array('gutter', 'padding', 'width', 'class'), true|false);
		
		$cs 			= $contentsettings
		array() 		= Array der gewünschten Optionsfelder aus $cs (Definition: 'Feldname')
		true|false		= Ausgabe mit oder ohne umschließendes class="" (default: false)
		*/
		
        return self::getClasses($cs, $col=0, $options, 'template', $with);
    }
	
	
    public static function getColumnClasses($cs, $col, $options = array(), $with = false)
	{	/*
		Liest die Werte der definierten Spalten-Settings aus und gibt diese als CSS-Klassen-Aufzählung aus.
		
		Aufruf:
		echo rex_gridblock_helper::getColumnClasses($cs, 1, array('gutter', 'padding', 'width', 'class'));
		echo rex_gridblock_helper::getColumnClasses($cs, 2, array('gutter', 'padding', 'width', 'class'), true|false);
		
		$cs 			= $contentsettings
		1...12			= Spaltennummer 1...12
		array() 		= Array der gewünschten Optionsfelder aus $cs (Definition: 'Feldname')
		true|false		= Ausgabe mit oder ohne umschließendes class="" (default: false)
		*/
		
        return self::getClasses($cs, $col, $options, 'column', $with);
    }	
	

    private static function getClasses($cs, $col = 0, $options = array(), $type = "", $with = false)
	{
		$op = "";
		$type = ($type == 'column') ? 'column_'.intval($col) : 'template';
		
		if (!empty($options) && !empty($cs)):
			foreach ($options as $option):
				$cso = @$cs->$type->$option;
				$op .= (!empty($cso)) ? $cso.' ' : '';
			endforeach;
			
			$op = ($with && !empty($op)) ? 'class="'.trim($op).'"' : $op;
		endif;
		
        return trim($op);
    }
	
	
    public static function getTemplateStyles($cs, $options = array(), $with = false)
	{	/*
		Liest die Werte der definierten Template-Settings aus und gibt diese als Style-Aufzählung aus.

		Aufruf:
		echo rex_gridblock_helper::getTemplateStyles($cs, array('background-image'=>'bgimage', 'background-color'=>'bgcolor'));
		echo rex_gridblock_helper::getTemplateStyles($cs, array('background-image'=>'bgimage', 'background-color'=>'bgcolor'), true|false);
		echo rex_gridblock_helper::getTemplateStyles($cs, array('min-height|px'=>'minheight'));
		echo rex_gridblock_helper::getTemplateStyles($cs, array('min-height|px'=>'minheight|empty'));
		
		$cs 			= $contentsettings
		array() 		= Array der gewünschten Optionsfelder aus $cs (Definition: 'CSS-Attribut'=>'Feldname' oder 'CSS-Attribut|Einheit'=>'Feldname|Prüfroutine')
		true|false		= Ausgabe mit oder ohne umschließendes style="" (default: false)
		
		Mögliche Prüfroutinen = empty
		*/
		
        return self::getStyles($cs, $col=0, $options, 'template', $with);
    }
	
	
    public static function getColumnStyles($cs, $col, $options = array(), $with = false)
	{	/*
		Liest die Werte der definierten Spalten-Settings aus und gibt diese als Style-Aufzählung aus.
		
		Aufruf:
		echo rex_gridblock_helper::getColumnStyles($cs, 1, array('background-image'=>'bgimage', 'background-color'=>'bgcolor'));
		echo rex_gridblock_helper::getColumnStyles($cs, 2, array('background-image'=>'bgimage', 'background-color'=>'bgcolor'), true|false);
		echo rex_gridblock_helper::getColumnStyles($cs, 1, array('min-height|px'=>'minheight'));
		echo rex_gridblock_helper::getColumnStyles($cs, 2, array('min-height|px'=>'minheight|empty'));
		echo rex_gridblock_helper::getColumnStyles($cs, 2, array('nokey'=>'cssstyle|empty'));
		
		$cs 			= $contentsettings
		1...12			= Spaltennummer 1...12
		array() 		= Array der gewünschten Optionsfelder aus $cs (Definition: 'CSS-Attribut'=>'Feldname' oder 'CSS-Attribut|Einheit'=>'Feldname|Prüfroutine')
						  Mit dem 'nokey'-Schlüsselwort wird nur der Feldname (optional mit der Einheit) zurückgegeben.
		true|false		= Ausgabe mit oder ohne umschließendes style="" (default: false)
		
		Mögliche Prüfroutinen = empty
		*/
		
        return self::getStyles($cs, $col, $options, 'column', $with);
    }
	

    private static function getStyles($cs, $col = 0, $options = array(), $type = "", $with = false)
	{					
		$op = "";
		$type = ($type == 'column') ? 'column_'.intval($col) : 'template';
		
		if (!empty($options) && !empty($cs)):
			foreach ($options as $key=>$val):
			
				//Prüfroutine extrahieren
				$check = "";
				if (!empty($val) && strpos($val, "|") !== false):
					$tmp = explode("|", $val);
					$val = $tmp[0];
					$check = $tmp[1];
				endif;
			
				//gespeicherten Wert aus $cs holen
				$val = @$cs->$type->$val;
								
				//Prüfroutinen ausführen				
				if ($check == 'empty'):
					if (empty($val)) { continue; }
				endif;
				
				//Style-Definition erstellen
				if (!empty($key) && trim($val) != ""):
					//Einheit extrahieren
					$unit = '';
						if (strpos($key, "|") !== false):
							$tmp = explode("|", $key);
							$key = $tmp[0];
							$unit = $tmp[1];
						endif;
				
					$val = (strtolower($key) == 'background-image') ? 'url(/media/'.$val.')' : $val;
					$val = str_replace('"', "'", $val);			
					$op .= ($key != 'nokey') ? $key.': '.$val.$unit.'; ' : $val.$unit;
				endif;
				
			endforeach;
			
			$op = ($with && !empty($op)) ? 'style="'.trim($op).'"' : $op;
		endif;
		
        return trim($op);
    }
	
	
    public static function getTemplateData($cs, $options = array())
	{	/*
		Liest die Werte der definierten Template-Settings aus und gibt diese als Data-Attribute aus.

		Aufruf:
		echo rex_gridblock_helper::getTemplateData($cs, array('data-aos'=>'aoseffect', 'data-aos-anchor'=>'aosanchor'));
		echo rex_gridblock_helper::getTemplateData($cs, array('data-aos'=>'aoseffect|empty'));
		
		$cs 			= $contentsettings
		array() 		= Array der gewünschten Optionsfelder aus $cs (Definition: 'Data-Attribut'=>'Feldname' oder 'Data-Attribut'=>'Feldname|Prüfroutine')
		
		Mögliche Prüfroutinen = empty
		*/
		
        return self::getDatas($cs, $col=0, $options, 'template');
    }
	
	
    public static function getColumnData($cs, $col, $options = array())
	{	/*
		Liest die Werte der definierten Spalten-Settings aus und gibt diese als Data-Attribute aus.
		
		Aufruf:
		echo rex_gridblock_helper::getColumnData($cs, 1, array('data-aos'=>'aoseffect', 'data-aos-anchor'=>'aosanchor'));
		echo rex_gridblock_helper::getColumnData($cs, 1, array('data-aos'=>'aoseffect|empty'));
		
		$cs 			= $contentsettings
		1...12			= Spaltennummer 1...12
		array() 		= Array der gewünschten Optionsfelder aus $cs (Definition: 'Data-Attribut'=>'Feldname' oder 'Data-Attribut|Einheit'=>'Feldname|Prüfroutine')
		
		Mögliche Prüfroutinen = empty
		*/
		
        return self::getDatas($cs, $col, $options, 'column');
    }
	

    private static function getDatas($cs, $col = 0, $options = array(), $type = "")
	{					
		$op = "";
		$type = ($type == 'column') ? 'column_'.intval($col) : 'template';
		
		if (!empty($options) && !empty($cs)):
			foreach ($options as $key=>$val):
			
				//Prüfroutine extrahieren
				$check = "";
				if (!empty($val) && strpos($val, "|") !== false):
					$tmp = explode("|", $val);
					$val = $tmp[0];
					$check = $tmp[1];
				endif;
			
				//gespeicherten Wert aus $cs holen
				$val = @$cs->$type->$val;
								
				//Prüfroutinen ausführen				
				if ($check == 'empty'):
					if (empty($val)) { continue; }
				endif;
				
				//Data-Definition erstellen
				if (!empty($key) && trim($val) != ""):
					$val = str_replace('"', "'", $val);
					$op .= $key.'="'.$val.'" ';
				endif;
				
			endforeach;
		endif;
		
        return trim($op);
    }
	

    public static function getTemplateHeader($cs, $header, $type = "h2", $align_class = "", $class = "", $with = true)
	{	/*
		Aufruf:
		echo rex_gridblock_helper::getTemplateHeader($cs, 'header', 'header_type', 'header_align');
		echo rex_gridblock_helper::getTemplateHeader($cs, 'header', 'header_type', 'header_align', 'myClass');
		echo rex_gridblock_helper::getTemplateHeader($cs, 'header', 'header_type', 'header_align', 'myClass', true|false);
		
		header 			= Feldname der Überschrift
		header_type 	= Feldname der H-Größe (H1-H6)
		header_align	= Feldname der Ausrichtung (CSS-Klasse)
		myClass			= zus. eigene CSS-Klasse für den Block
		true|false		= Ausgabe mit oder ohne umschließendes DIV (default: true)
		*/
		
		$op = "";
		$header = @$cs->template->$header;
		$type = @$cs->template->$type;
			$type = (!preg_match("/h[1-6]{1}/i", $type)) ? 'h2' : $type;
		$align_class = @$cs->template->$align_class;
		$class = (empty($class)) ? 'gridblock-mainheader' : $class;
		
		if (trim($header) != ""):
			if ($with):
				$op .= '<div class="'.$class.' '.$align_class.'"><'.$type.'>'.$header.'</'.$type.'></div>';
			else:
				$op .= '<'.$type.' class="'.$class.' '.$align_class.'">'.$header.'</'.$type.'>';
			endif;
		endif;
		
        return trim($op);
    }
	
}
?>