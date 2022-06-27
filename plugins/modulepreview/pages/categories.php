<?php
$func = rex_request('func', 'string');
$list = rex_request('list', 'string');
$id = rex_request('id', 'integer');

if ($id) {

    $oTmp = rex_yform_manager_dataset::get($id, rex::getTable("1620_gridblock_modulepreview_categories"));
    if (!$oTmp->id) {
        echo rex_view::error("Kategorie nicht gefunden");
        $func = '';
    }
}

if ($func == 'setstatus') {
    $status = (rex_request('oldstatus', 'int') + 1) % 2;
    rex_sql::factory()
        ->setTable(rex::getTable("1620_gridblock_modulepreview_categories"))
        ->setWhere(['id' => $id])
        ->setValue('status', $status)
        ->addGlobalUpdateFields()
        ->update();
    echo rex_view::success("Status gespeichert");
    $func = '';
}


if ($func == 'delete') {
    rex_sql::factory()
        ->setTable(rex::getTable("1620_gridblock_modulepreview_categories"))
        ->setWhere(['id' => $id])
        ->delete();
    echo rex_view::success("Kategorie gelöscht");
    $func = '';
}



if ($func == 'edit' || $func == 'add') {
    $fieldset = $func == 'edit' ? $this->i18n('gridblock_modulepreview_categories_edit') : $this->i18n('gridblock_modulepreview_categories_add');
    $id = rex_request('id', 'int');
    $form = rex_form::factory(rex::getTable("1620_gridblock_modulepreview_categories"), '', 'id=' . $id);

    $field = $form->addTextField('title');
    $field->setLabel($this->i18n('gridblock_modulepreview_categories_title'));
    $field->getValidator()->add('notEmpty', $this->i18n('gridblock_modulepreview_categories_title_validate_empty'));

    #$field = $form->addSelectField('modules', $value=null, ['class' => 'form-control selectpicker']);
    $field = $form->addSelectField('modules', $value = null, ['class' => 'nv-modules']);
    $field->setAttribute('multiple', 'multiple');
    $field->setLabel($this->i18n('gridblock_modulepreview_categories_modules'));
    $select = $field->getSelect();
    $select->setSize(3);
    $field->getValidator()->add('notEmpty', $this->i18n('gridblock_modulepreview_categories_modules_validate_empty'));


    $aUsedModules = array();

    if ($func == "edit") {
        $oDb = rex_sql::factory();
        $oDb->setQuery("SELECT * FROM " . rex::getTable("1620_gridblock_modulepreview_categories") . " WHERE id = :id Limit 1", ["id" => $id]);
        $aModules = explode("|", $oDb->getValue("modules"));
        foreach ($aModules as $iModuleId) {
            if ($iModuleId) {
                $oDb2 = rex_sql::factory();
                $oDb2->setQuery("SELECT id,name FROM " . rex::getTable("module") . " WHERE id = :id Limit 1", ["id" => $iModuleId]);
                if ($oDb2->getRows()) {
                    $aUsedModules[] = $iModuleId;
                    $select->addOption($oDb2->getValue("name"), $oDb2->getValue("id"));
                }
            }
        }
    }

    $oDb = rex_sql::factory();
    $oDb->setQuery("SELECT * FROM " . rex::getTable("module") . " WHERE name != '01 - Gridblock' ORDER BY name ASC");
    foreach ($oDb as $oItem) {
        if (rex::getUser()->getComplexPerm('modules')->hasPerm($oItem->getValue("id")) && !in_array($oItem->getValue("id"), $aUsedModules)) {
            $select->addOption($oItem->getValue("name"), $oItem->getValue("id"));
        }
    }

    $field = $form->addTextField('description');
    $field->setLabel($this->i18n('gridblock_modulepreview_categories_description'));

    $field = $form->addPrioField('prio');
    $field->setLabel($this->i18n('gridblock_modulepreview_categories_prio'));
    $field->setAttribute('class', 'selectpicker form-control');
    $field->setLabelField('title');

    if ($func == 'edit') {
        $form->addParam('id', $id);
    }

    $field = $form->addSelectField('status', $value = null, ['class' => 'selectpicker form-control']);
    $field->setLabel($this->i18n('gridblock_modulepreview_categories_status'));
    $select = $field->getSelect();
    $select->addOption($this->i18n('gridblock_modulepreview_categories_status_active'), "1");
    $select->addOption($this->i18n('gridblock_modulepreview_categories_status_inactive'), "0");

    $content = $form->get();
    $fragment = new rex_fragment();
    $fragment->setVar('class', 'edit', false);
    $fragment->setVar('title', "$fieldset");
    $fragment->setVar('body', $content, false);
    $content = '<div id="nv_bemails">' . $fragment->parse('core/page/section.php') . '</div>';
    echo $content;
    echo '<script>';
    echo '$(".nv-modules").selectize({
        plugins: ["drag_drop"],
        delimiter: "|",
        persist: false,

      });</script>';
      echo '<style>.btn.btn-apply,.btn.btn-delete { display:none}</style>';
}

if ($func == '') {

    if ($_SESSION["categories_show_msg"]) {
        echo $_SESSION["categories_show_msg"];
        unset($_SESSION["categories_show_msg"]);
    }

    $query = "SELECT id,title,prio,status FROM " . rex::getTable("1620_gridblock_modulepreview_categories") . " ORDER BY prio ASC";
    $list = rex_list::factory($query);
    $list->addTableAttribute('class', 'table-striped');

    $list->removeColumn('id');

    $list->setColumnLabel('title', "Kategorie");
    $list->setColumnSortable('title');
    $list->setColumnLabel('prio', "Priorität");
    $list->setColumnSortable('prio');


    $list->setColumnLabel('updatedate', "Aktualisiert");
    $list->setColumnSortable('updatedate');
    $list->setColumnFormat('updatedate', 'custom', function ($params) {
        $list = $params['list'];
        $sStr = date("d.m.Y H:i", strtotime($list->getValue(updatedate)));
        return $sStr;
    });

    $list->setColumnLabel('status', "Status");
    $list->setColumnSortable('status');

    $list->setColumnParams('status', ['func' => 'setstatus', 'oldstatus' => '###status###', 'id' => '###id###']);
    $list->setColumnLayout('status', ['<th class="rex-table-action">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnFormat('status', 'custom', function ($params) {
        /** @var rex_list $list */
        $list = $params['list'];
        if ($list->getValue('status') == 1) {
            $str = $list->getColumnLink('status', '<span class="rex-online"><i class="rex-icon rex-icon-online"></i> ' . $this->i18n('gridblock_modulepreview_categories_status_active') . '</span>');
        } else {
            $str = $list->getColumnLink('status', '<span class="rex-offline"><i class="rex-icon rex-icon-offline"></i> ' . $this->i18n('gridblock_modulepreview_categories_status_inactive') . '</span>');
        }
        return $str;
    });

    $list->addColumn("Funktion", "Bearbeiten");
    $list->setColumnLayout("Funktion", ['<th class="rex-table-action" colspan="3">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams("Funktion", ['func' => 'edit', 'id' => '###id###']);

    $list->addColumn('delete', "Löschen", -1, ['', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams('delete', ['func' => 'delete', 'id' => '###id###']);
    $list->addLinkAttribute('delete', 'onclick', "return confirm('Wirklich unwiderruflich löschen?');");


    $sContent = '<br><a href="' . $list->getUrl(['func' => 'add']) . '" class="btn btn-save"><i class="rex-icon rex-icon-add-article"></i> &nbsp; Kategorie hinzufügen</a><br><br>';
    $sContent .= $list->get();




    $oFragment = new rex_fragment();
    $oFragment->setVar("class", "edit");
    $oFragment->setVar('title', "Kategorie", false);
    $oFragment->setVar('body', $sContent, false);
    $sOutput = $oFragment->parse('core/page/section.php');
    echo $sOutput;
}
