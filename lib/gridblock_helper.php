<?php
/*
	Redaxo-Addon Gridblock
	Helper-Funktionen für contentsettings
	v1.0
	by Falko Müller @ 2021 (based on 0.1.0-dev von bloep)
*/

class rex_gridblock_helper
{	
    public static function getTemplateClasses($cs, $options = array(), $with = false)
	{	/*
		Liest die Werte der Template-Felder aus und gibt diese als CSS-Klassen-Aufzählung aus.
		
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
		Liest die Werte der Spalten-Felder aus und gibt diese als CSS-Klassen-Aufzählung aus.
		
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
				if (trim($option) != ""):				
					$op .= @$cs->$type->$option.' ';
				endif;
			endforeach;
			
			$op = ($with) ? 'class="'.$op.'"' : $op;
		endif;
		
        return trim($op);
    }
	
	
    public static function getTemplateStyles($cs, $options = array(), $with = false)
	{	/*
		Liest die Werte der Template-Felder aus und gibt diese als Style-Aufzählung aus.

		Aufruf:
		echo rex_gridblock_helper::getTemplateStyles($cs, array('background-image'=>'bgimage', 'background-color'=>'bgcolor'));
		echo rex_gridblock_helper::getTemplateStyles($cs, array('background-image'=>'bgimage', 'background-color'=>'bgcolor'), true|false);
		
		$cs 			= $contentsettings
		array() 		= Array der gewünschten Optionsfelder aus $cs (Definition: 'CSS-Attribut'=>'Feldname')
		true|false		= Ausgabe mit oder ohne umschließendes style="" (default: false)
		*/
		
        return self::getStyles($cs, $col=0, $options, 'template', $with);
    }
	
	
    public static function getColumnStyles($cs, $col, $options = array(), $with = false)
	{	/*
		Aufruf:
		echo rex_gridblock_helper::getColumnStyles($cs, 1, array('background-image'=>'bgimage', 'background-color'=>'bgcolor'));
		echo rex_gridblock_helper::getColumnStyles($cs, 2, array('background-image'=>'bgimage', 'background-color'=>'bgcolor'), true|false);
		
		$cs 			= $contentsettings
		1...12			= Spaltennummer 1...12
		array() 		= Array der gewünschten Optionsfelder aus $cs (Definition: 'CSS-Attribut'=>'Feldname')
		true|false		= Ausgabe mit oder ohne umschließendes style="" (default: false)
		*/
		
        return self::getStyles($cs, $col, $options, 'column', $with);
    }
	

    private static function getStyles($cs, $col = 0, $options = array(), $type = "", $with = false)
	{					
		$op = "";
		$type = ($type == 'column') ? 'column_'.intval($col) : 'template';
		
		if (!empty($options) && !empty($cs)):
			foreach ($options as $key=>$val):
				$val = @$cs->$type->$val;
				
				if (!empty($key) && trim($val) != ""):
					$val = (strtolower($key) == 'background-image') ? 'url(/media/'.$val.')' : $val;			
					$op .= $key.': '.$val.'; ';
				endif;
			endforeach;
			
			$op = ($with) ? 'style="'.$op.'"' : $op;
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
	

    public static function importTemplateArchive($file)
	{
		$return = false;
		
		if (!empty($file)):
			//hier dein PHP-Code rein
			dump($file);
			
		
			$return = true;
		endif;
		
		return $return;
	}
}
?>