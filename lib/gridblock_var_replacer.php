<?php
/*
	Redaxo-Addon Gridblock
	VAR-Replacer (REX-VARS und Widget-Buttons umschreiben)
	v1.0
	by Falko Müller @ 2021 (based on 0.1.0-dev von bloep)
*/

class rex_gridblock_var_replacer
{
    public static function replaceVars($colID, $moduleContent, $uID, $rexVars = array())
	{
        $moduleContent = self::replaceInput($colID, $moduleContent, $uID);
        $moduleContent = self::replaceMedia($colID, $moduleContent, $uID);
        $moduleContent = self::replaceMediaList($colID, $moduleContent, $uID);
        $moduleContent = self::replaceLink($colID, $moduleContent, $uID);
        $moduleContent = self::replaceLinkList($colID, $moduleContent, $uID);
		$moduleContent = self::replaceModuleVars($moduleContent, $rexVars);
		
        return $moduleContent;
    }
	

    private static function replaceInput($colID, $moduleContent, $uID)
    {
        $moduleContent = preg_replace('/REX_INPUT_VALUE\[(\d+)\]/', 'REX_INPUT_VALUE['.$colID.']['.$uID.'][VALUE][$1]', $moduleContent);																	//normalen REX-VARS
        $moduleContent =  preg_replace('/REX_INPUT_VALUE\['.$colID.'\]\['.$uID.'\]\[VALUE\]\[(\d+)\]\[(\d+)\]\[/', 'REX_INPUT_VALUE['.$colID.']['.$uID.'][VALUE][$1_MBLOCK][$2][', $moduleContent);			//MBLOCK-VARS

        return $moduleContent;
    }
	

    private static function replaceMedia($colID, $moduleContent, $uID)
    {
		$moduleContent = preg_replace('/REX_INPUT_MEDIA\[(\d+)\]/', 'REX_INPUT_VALUE['.$colID.']['.$uID.'][MEDIA][$1]', $moduleContent);
        $moduleContent = preg_replace('/REXMedia\((\d+)/', 'REXMedia('.$colID.$uID.'00$1', $moduleContent);
        $moduleContent = preg_replace('/id="REX_MEDIA_(\d+)"/', 'id="REX_MEDIA_'.$colID.$uID.'00$1"', $moduleContent);

        //zusätzliche Ersetzung der MP-Buttons im Media-Widget
        $moduleContent = preg_replace('/openREXMedia\(\'(\d+)\'/',				'openREXMedia(\''.$colID.$uID.'00$1\'', $moduleContent);
        $moduleContent = preg_replace('/addREXMedia\(\'(\d+)\'/',				'addREXMedia(\''.$colID.$uID.'00$1\'', $moduleContent);
        $moduleContent = preg_replace('/deleteREXMedia\(\'(\d+)\'/',			'deleteREXMedia(\''.$colID.$uID.'00$1\'', $moduleContent);
        $moduleContent = preg_replace('/viewREXMedia\(\'(\d+)\'/',				'viewREXMedia(\''.$colID.$uID.'00$1\'', $moduleContent);
		
        return $moduleContent;
    }
	

    private static function replaceMediaList($colID, $moduleContent, $uID)
    {
        $moduleContent = preg_replace('/REX_MEDIALIST_SELECT\[(\d+)\]/', 'REX_INPUT_VALUE['.$colID.']['.$uID.'][MEDIALIST_SELECT][$1]', $moduleContent);
        $moduleContent = preg_replace('/REX_INPUT_MEDIALIST\[(\d+)\]/', 'REX_INPUT_VALUE['.$colID.']['.$uID.'][MEDIALIST][$1]', $moduleContent);
        $moduleContent = preg_replace('/REXMedialist\((\d+)/', 'REXMedialist('.$colID.$uID.'00$1', $moduleContent);
        $moduleContent = preg_replace('/id="REX_MEDIALIST_(\d+)"/', 'id="REX_MEDIALIST_'.$colID.$uID.'00$1"', $moduleContent);
        $moduleContent = preg_replace('/id="REX_MEDIALIST_SELECT_(\d+)"/', 'id="REX_MEDIALIST_SELECT_'.$colID.$uID.'00$1"', $moduleContent);
        
        //zusätzliche Ersetzung der Buttons im Medialist-Widget
        $moduleContent = preg_replace('/openREXMedialist\(\'(\d+)\'/',			'openREXMedialist(\''.$colID.$uID.'00$1\'', $moduleContent);
        $moduleContent = preg_replace('/addREXMedialist\(\'(\d+)\'/',			'addREXMedialist(\''.$colID.$uID.'00$1\'', $moduleContent);
        $moduleContent = preg_replace('/deleteREXMedialist\(\'(\d+)\'/',		'deleteREXMedialist(\''.$colID.$uID.'00$1\'', $moduleContent);
        $moduleContent = preg_replace('/viewREXMedialist\(\'(\d+)\'/',			'viewREXMedialist(\''.$colID.$uID.'00$1\'', $moduleContent);
        $moduleContent = preg_replace('/moveREXMedialist\(\'(\d+)\'/',			'moveREXMedialist(\''.$colID.$uID.'00$1\'', $moduleContent);
		
        return $moduleContent;
    }
	

    private static function replaceLink($colID, $moduleContent, $uID)
    {
        $moduleContent = preg_replace('/REX_LINK_NAME\[(\d+)\]/', 'REX_INPUT_VALUE['.$colID.']['.$uID.'][LINK_NAME][$1]', $moduleContent);
        $moduleContent = preg_replace('/id="REX_LINK_(\d+)_NAME"/', 'id="REX_LINK_'.$colID.$uID.'00$1_NAME"', $moduleContent);

        $moduleContent = preg_replace('/REX_INPUT_LINK\[(\d+)\]/', 'REX_INPUT_VALUE['.$colID.']['.$uID.'][LINK][$1]', $moduleContent);
        $moduleContent = preg_replace('/openLinkMap\(\'REX_LINK_(\d+)\'/', 'openLinkMap(\'REX_LINK_'.$colID.$uID.'00$1\'', $moduleContent);
        $moduleContent = preg_replace('/REXLink\((\d+)/', 'REXLink('.$colID.$uID.'00$1', $moduleContent);
        $moduleContent = preg_replace('/id="REX_LINK_(\d+)"/', 'id="REX_LINK_'.$colID.$uID.'00$1"', $moduleContent);

        //zusätzliche Ersetzung der Buttons im Link-Widget
        $moduleContent = preg_replace('/openLinkMap\(\'REX_LINK_(\d+)\'/',		'openLinkMap(\'REX_LINK_'.$colID.$uID.'00$1\'', $moduleContent);
        $moduleContent = preg_replace('/deleteREXLink\(\'(\d+)\'/',				'deleteREXLink(\''.$colID.$uID.'00$1\'', $moduleContent);
		
        return $moduleContent;
    }
	

    private static function replaceLinkList($colID, $moduleContent, $uID)
    {
        $moduleContent = preg_replace('/REX_LINKLIST_SELECT\[(\d+)\]/', 'REX_INPUT_VALUE['.$colID.']['.$uID.'][LINKLIST_SELECT][$1]', $moduleContent);
        $moduleContent = preg_replace('/REX_INPUT_LINKLIST\[(\d+)\]/', 'REX_INPUT_VALUE['.$colID.']['.$uID.'][LINKLIST][$1]', $moduleContent);
        $moduleContent = preg_replace('/REXLinklist\((\d+)/', 'REXLinklist('.$colID.$uID.'00$1', $moduleContent);
        $moduleContent = preg_replace('/id="REX_LINKLIST_(\d+)"/', 'id="REX_LINKLIST_'.$colID.$uID.'00$1"', $moduleContent);
        $moduleContent = preg_replace('/id="REX_LINKLIST_SELECT_(\d+)"/', 'id="REX_LINKLIST_SELECT_'.$colID.$uID.'00$1"', $moduleContent);

        //zusätzliche Ersetzung der Buttons im Linklist-Widget
        $moduleContent = preg_replace('/openREXLinklist\(\'(\d+)\'/',			'openREXLinklist(\''.$colID.$uID.'00$1\'', $moduleContent);
        $moduleContent = preg_replace('/deleteREXLinklist\(\'(\d+)\'/',			'deleteREXLinklist(\''.$colID.$uID.'00$1\'', $moduleContent);
        $moduleContent = preg_replace('/moveREXLinklist\(\'(\d+)\'/',			'moveREXMedialist(\''.$colID.$uID.'00$1\'', $moduleContent);
		
        return $moduleContent;
    }
	

    public static function replaceModuleVars($moduleContent, $rexVars)
    {
		//REX-Vars
		$moduleContent = preg_replace('/REX_CTYPE_ID/', 				@$rexVars['ctypeID'], $moduleContent);				//wird nicht unterstützt
        $moduleContent = preg_replace('/REX_SLICE_ID/', 				@$rexVars['sliceID'], $moduleContent);
		$moduleContent = preg_replace('/REX_MODULE_ID/',				@$rexVars['moduleID'], $moduleContent);
		$moduleContent = preg_replace('/REX_MODULE_KEY/', 				@$rexVars['moduleKEY'], $moduleContent);
		$moduleContent = preg_replace('/REX_CLANG_ID/', 				@$rexVars['clangID'], $moduleContent);
		$moduleContent = preg_replace('/REX_ARTICLE_ID/', 				@$rexVars['artID'], $moduleContent);
		$moduleContent = preg_replace('/REX_TEMPLATE_ID/', 				@$rexVars['tmplID'], $moduleContent);
		
		//GRID-Vars
		$moduleContent = preg_replace('/REX_GRID_TEMPLATE_ID/', 		@$rexVars['grid_tmplID'], $moduleContent);
		$moduleContent = preg_replace('/REX_GRID_TEMPLATE_PREVIEW/', 	@$rexVars['grid_tmplPREV'], $moduleContent);
		$moduleContent = preg_replace('/REX_GRID_TEMPLATE_COLUMNS/', 	@$rexVars['grid_tmplCOLS'], $moduleContent);
		$moduleContent = preg_replace('/REX_GRID_COLUMN_NUMBER/', 		@$rexVars['grid_colNR'], $moduleContent);
		
        return $moduleContent;
    }
}
?>