<?php
/*
	Redaxo-Addon Gridblock
	API-Anbindung
	v1.1.12
	by Falko Müller @ 2021-2023 (based on 0.1.0-dev von bloep)
*/

class rex_api_gridblock_loadModule extends rex_api_function
{
    public function execute()
    {
        $moduleID 	= rex_request::get('moduleid', 'int', null);
        $colID 		= rex_request::get('colid', 'int', null);
		$uID 		= rex_request::get('uid');
		$action		= rex_request::get('action');
		
        if ($moduleID && $colID && $uID):
            $ed = new rex_article_content_gridblock();
			echo $ed->getModuleEdit($moduleID, $colID, $uID, array(), $action);
            exit;
        endif;
		
        throw new rex_functional_exception('Module-ID, column-ID and unique-ID parameters are required!');
    }
}


class rex_api_gridblock_getModuleSelector extends rex_api_function
{
    public function execute()
    {
        $colID 		= rex_request::get('colid', 'int', null);
		
		if ($colID):
            $ed = new rex_article_content_gridblock();
			echo $ed->getModuleSelector($colID);
			exit;
        endif;
		
        throw new rex_functional_exception('Column-ID parameter is required!');
    }
}


class rex_api_gridblock_addModuleSelector extends rex_api_function
{
    public function execute()
    {
        $colID 		= rex_request::get('colid', 'int', null);
		$uID 		= rex_request::get('uid');
		
		if ($colID && $uID):
            $ed = new rex_article_content_gridblock();
			echo $ed->addModuleSelector($colID, $uID);
			exit;
        endif;
		
        throw new rex_functional_exception('Column-ID and unique-ID parameters are required!');
    }
}


class rex_api_gridblock_loadContentSettings extends rex_api_function
{
    public function execute()
    {
		$templateID	= rex_request::get('templateid', 'int', null);
		$colID 		= rex_request::get('colid', 'int', null);
		
        if ($templateID):
			//gespeicherte Werte ggf. aus SESSION holen und Settingfelder abrufen
			$settings = (isset($_SESSION['gridContentSettings'])) ? $_SESSION['gridContentSettings'] : array();
			$settings = (!is_array($settings)) ? rex_var::toArray($settings) : $settings;
			
			$oSettings = new GridblockContentSettings;
			echo $oSettings->getGridblockContentSettingsForm($settings, $templateID, $colID);
			exit;
        endif;
		
        throw new rex_functional_exception('Template-ID parameter is required!');
    }
}
	
	
class rex_api_gridblock_setCookie extends rex_api_function
{
    public function execute()
    {
        $sliceID	= rex_request::get('sliceid');
		$uID 		= rex_request::get('uid');
		$colID 		= rex_request::get('colid', 'int', null);
		$modID 		= rex_request::get('modid', 'int', null);
		$modStatus	= rex_request::get('modstatus', 'int', null);
		$action		= rex_request::get('action');
		$formData   = rex_request::get('form_data', 'string', '');
		$sourceUID  = rex_request::get('source_uid', 'string', '');
		
		if (!empty($uID) && !empty($action)):
			$value = [
				'sliceid' => $sliceID, 
				'uid' => $uID, 
				'colid' => $colID, 
				'modid' => $modID, 
				'modstatus' => $modStatus, 
				'action' => $action,
				'form_data' => $formData,
				'source_uid' => $sourceUID
			];
            rex_article_content_gridblock::setCookie($value);
			exit();
        endif;
		
        throw new rex_functional_exception('Action & uid parameter are required!');
    }
}
	
	
class rex_api_gridblock_getCookieName extends rex_api_function
{
    public function execute()
    {
		echo rex_article_content_gridblock::getCookieName();
		exit();
    }
}
	
	
class rex_api_gridblock_getCookie extends rex_api_function
{
    public function execute()
    {
		$key 		= rex_request::get('key');
		
		echo rex_article_content_gridblock::getCookie($key);
		exit();
    }
}
	
	
class rex_api_gridblock_deleteCookie extends rex_api_function
{
    public function execute()
    {
		rex_article_content_gridblock::deleteCookie();
		exit();
    }
}

?>