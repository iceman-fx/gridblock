<?php
/*
	Redaxo-Addon Gridblock
	API-Anbindung
	v0.8
	by Falko Müller @ 2021 (based on 0.1.0-dev von bloep)
*/

class rex_api_gridblock_loadModule extends rex_api_function
{
    public function execute()
    {
        $moduleID 	= rex_request::get('moduleid', 'int', null);
        $colID 		= rex_request::get('colid', 'int', null);
		$uID 		= rex_request::get('uid');
		
        if ($moduleID && $colID && $uID) {
            $ed = new rex_article_content_gridblock();
            echo $ed->getModuleEdit($moduleID, $colID, $uID);
            exit;
        }
        throw new rex_functional_exception('Module-ID, column-ID and unique-ID parameters are required!');
    }
}


class rex_api_gridblock_getModuleSelector extends rex_api_function
{
    public function execute()
    {
        $colID 		= rex_request::get('colid', 'int', null);
		$uID 		= rex_request::get('uid');
		
        if ($colID && $uID) {
            $ed = new rex_article_content_gridblock();
            echo $ed->getModuleSelector($colID, $uID);
            exit;
        }
        throw new rex_functional_exception('Column-ID and unique-ID parameters are required!');
    }
}