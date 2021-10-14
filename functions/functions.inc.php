<?php
/*
	Redaxo-Addon Gridblock
	Globale-Funktionen (Addon + Modul)
	v0.8
	by Falko Müller @ 2021 (based on 0.1.0-dev von bloep)
*/

//aktive Session prüfen


//globale Variablen
//global $a1620_mypage;
//$a1620_mypage = $this->getProperty('package');


//Funktionen
//Ajax-Inhalte holen
if (!function_exists('aFM_bindAjax')):
	function aFM_bindAjax($ep)
	{	$op = $ep->getSubject();
		$op = preg_replace('/(.*<\!-- ###AJAX### -->)(.*)(<\!-- ###\/AJAX### -->.*)/s', '$2', $op);
		return $op;
	}
endif;


//Maskierungen + Tags
if (!function_exists('aFM_maskChar')):
	function aFM_maskChar($str)
	{	//Maskiert folgende Sonderzeichen: & " < > '
		$str = stripslashes($str);
		$str = htmlspecialchars($str, ENT_QUOTES);
		$str = trim($str);		
		return $str;
	}
endif;
if (!function_exists('aFM_maskSql')):
	function aFM_maskSql($str)
	{	//Maskiert desn Wert für DB-Abfrage
		$s = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
    	$r = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");
		return str_replace($s, $r, $str);
	}
endif;
if (!function_exists('aFM_revChar')):
	function aFM_revChar($str)
	{	//Demaskiert folgende Sonderzeichen: & " < > '
		$chars = array("&amp;amp;quot;"=>'"', "&amp;quot;"=>'"', "&amp;"=>"&", "&lt;"=>"<", "&gt;"=>">", "&quot;"=>'"', "&#039;"=>"'");
		foreach ($chars as $key => $value):
			$str = str_replace($key, $value, $str);
		endforeach;
		
		return $str;
	}
endif;
if (!function_exists('aFM_noQuote')):
	function aFM_noQuote($str)
	{	//Ersetzt Double-Quotes: "
		return str_replace('"', "'", $str);
	}
endif;
if (!function_exists('aFM_textOnly')):
	function aFM_textOnly($str, $nobreak = false)
	{	//Entfernt HTML-Tags, Zeilenumbrüche und Tabstops
		if ($str != ""):
			$str = stripslashes($str);
			$str = str_replace("\xc2\xa0", ' ', $str);	//&nbsp; als UTF8 ersetzen in nortmales WhiteSpace
			$str = str_replace("\t", ' ', $str);		//Tabstop (\t) ersetzen in normales WhiteSpace
			$str = strip_tags(nl2br($str));
			$str = ($nobreak) ? str_replace(array("\r\n","\n\r", "\n", "\r"), "", $str) : $str;
			$str = trim($str);
		endif;
		
		return $str;
	}
endif;
?>