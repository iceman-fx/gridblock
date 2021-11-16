<?php
/*
	Redaxo-Addon Gridblock
	Grid-Basisklasse
	v1.0
	by Falko Müller @ 2021 (based on 0.1.0-dev von bloep)
*/

class rex_gridblock {

	public $sliceID = 0;					//int
	public $artID = 0;						//int
	public $tmplID = 0;						//int
	public $clangID = 0;					//int
	public $ctypeID = 0;					//int -> ist aktuell nicht auslesbar !!!
	
	public $rexVars = [];					//array -> ctypeID ist aktuell nicht verfügbar !!!
	
    public $ingoredModules = [];			//array
	public $allowedModules = [];			//array
    private $values = [];					//array
    private $modules = [];					//array

	public $maxCols = 12;					//int
	

    public function getModules()
	{
		//Standard-Modulauswahl holen
		$config = rex_addon::get('gridblock')->getConfig('config');
		if (!empty($config['modules'])):
			if (@$config['modulesmode'] == 'ignore'):
				$this->ignoreModule(explode("#", $config['modules']));
			else:
				$this->allowModule(explode("#", $config['modules']));
			endif;
		endif;
		
		//Modulauswahl setzen
		$where_im = (count($this->ingoredModules) > 0 ? 'id not in ('.join(',', $this->ingoredModules).')' : '');
		$where_am = (count($this->allowedModules) > 0 ? 'id in ('.join(',', $this->allowedModules).')' : '');
			
		$where = "";	
			$where .= (!empty($where_im)) ? " AND $where_im" : '';
			$where .= (!empty($where_am)) ? " AND $where_am" : '';
		$where = (!empty($where) || @$config['modulesmode'] == 'ignore') ? 'input NOT LIKE "%/* GRID_MODULE_IDENTIFIER | DONT REMOVE */%" '.$where : '0';
		
		//Module aus DB holen und zwischenspeichern
		$sql = 'SELECT id, name, output, input FROM '.rex::getTable('module').' WHERE '.$where.' ORDER BY name, id';
		$db = rex_sql::factory();
		$db->setQuery($sql);

        $modules = [];
        while ($db->hasNext()):
            $modules[$db->getValue('id')] = [
                'name' => $db->getValue('name'),
                'output' => $db->getValue('output'),
                'input' => $db->getValue('input'),
            ];
			
            $db->next();
        endwhile;
		
		/*
		echo $sql;
		dump($modules);
		*/		
		
		$_SESSION['gridAllowedModules'] = $modules;
        return $modules;
    }
	

    public function ignoreModule($moduleID)
	{
		$this->checkModule($moduleID, 'ignore');
    }


    public function allowModule($moduleID)
	{
		$this->checkModule($moduleID, 'allow');
    }


    private function checkModule($moduleID, $type = 'allow')
	{
		if (is_array($moduleID)):
			foreach ($moduleID as $mid):
				if (!empty($mid)):
					if ($type == 'ignore'):
						$this->ingoredModules[] = (int) $mid;
					else:
						$this->allowedModules[] = (int) $mid;
					endif;
				endif;
			endforeach;
		
		elseif (is_int($moduleID)):
			if ($type == 'ignore'):
				$this->ingoredModules[] = (int) $moduleID;
			else:
				$this->allowedModules[] = (int) $moduleID;
			endif;
		endif;
    }
	

    public function getModuleInput()
	{
        $this->modules = self::getModules();
        $cols = [];

        //for ($i = 1; $i <= 12; ++$i):
		
		for ($i = 1; $i <= $this->maxCols; ++$i):
            $cols[$i] = ['content' => $this->genCol($i)];
        endfor;

        $fragment = new rex_fragment();
		$fragment->setVar('maxCols', $this->maxCols, false);
        $fragment->setVar('cols', $cols, false);
        $fragment->setVar('values', $this->values, false);
		$fragment->setVar('rexVars', $this->rexVars, false);
		
        return $fragment->parse('gridblock/module_input.php');
    }
	

    private function genCol($id)
	{
        $fragment = new rex_fragment();
        $fragment->setVar('id', $id, true);
        $fragment->setVar('modules', $this->modules, false);
        $fragment->setVar('values', $this->values, false);
		$fragment->setVar('rexVars', $this->rexVars, false);
		
        return $fragment->parse('gridblock/column.php');
    }


    public function getSliceValues($sliceID)
    {
		$sliceId = intval($sliceID);
		$this->rexVars['sliceID'] = (int) $sliceID;
		
		$art = rex_article::get(rex_article::getCurrentId());
		if ($art):
			$this->rexVars['artID'] = (int) $art->getId();
			$this->rexVars['tmplID'] = (int) $art->getTemplateId();
			$this->rexVars['clangID'] = (int) $art->getClangId();
		endif;
		
		$_SESSION['gridRexVars'] = $this->rexVars;
		//dump($_SESSION['gridRexVars']);
		
        $sql = rex_sql::factory();
        $sql->prepareQuery('SELECT value1, value2, value3, value4, value5, value6, value7, value8, value9, value10, value11, value12, value13, value14, value15, value16, value17, value18, value19, value20 FROM '.rex::getTable('article_slice').' WHERE id = ?');
        $sql->execute([$sliceID]);
		
        if ($sql->hasNext()):
            $this->values = [];
			
            for ($i = 1; $i <= 20; ++$i):
                $this->values[$i] = $sql->getValue('value'.$i);
            endfor;
        endif;
    }


    public function getModuleOutput()
	{
        $fragment = new rex_fragment();
        $fragment->setVar('maxCols', $this->maxCols, false);
		$fragment->setVar('values', $this->values, false);
		$fragment->setVar('rexVars', $this->rexVars, false);
		
        return $fragment->parse('gridblock/module_output.php');
    }
	

    public static function getBlankValues()
	{
        $values = [];
		
        for ($i = 1; $i <= 20; ++$i):
            $values['value'.$i] = '';
        endfor;
		
        for ($i = 1; $i <= 10; ++$i):
            $values['media'.$i] = '';
        endfor;
		
        for ($i = 1; $i <= 10; ++$i):
            $values['medialist'.$i] = '';
        endfor;
		
        for ($i = 1; $i <= 10; ++$i):
            $values['link'.$i] = '';
        endfor;
		
        for ($i = 1; $i <= 10; ++$i):
            $values['linklist'.$i] = '';
        endfor;
		
        return $values;
    }

    public static function getConfig($sKey=null) {
        $aConfig = rex_addon::get('gridblock')->getConfig('config');
        if ($sKey != "") {
            return $aConfig[$sKey];
        }
        return $aConfig;
    }
}
?>