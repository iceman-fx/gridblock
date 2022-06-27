<?php

if (rex_request('func', 'string') !== "edit") {


    $list = rex_list::factory("SELECT id,name,gridblock_modulepreview_thumbnail,gridblock_modulepreview_description  FROM " . rex::getTable("module") . " WHERE name != '01 - Gridblock' ORDER BY name ASC");
    #dump($list);
    // Optionen der Liste
    $list->addTableAttribute('class', 'table-hover');
    // Columns
    $list->removeColumn('gridblock_modulepreview_thumbnail');
    $list->setColumnLabel('id', rex_i18n::msg('gridblock_modulepreview_module_id'));
    $list->setColumnLabel('name', rex_i18n::msg('gridblock_modulepreview_module_name'));
    // Preview-Column setzen
    $list->addColumn(rex_i18n::msg('gridblock_modulepreview_thumbnail'), '', 1, ['<th>###VALUE###</th>', '<td><img src="/media/gridblock_modulepreview_thumbnail/###gridblock_modulepreview_thumbnail###" width="100" height="100" alt="Thumbnail ###name###"></td>']);
    // Description
    $list->setColumnLabel('gridblock_modulepreview_description', rex_i18n::msg('gridblock_modulepreview_description'));
    // Funktionen der Liste
    $list->setColumnLabel('edit', '');
    $list->addColumn('edit', rex_i18n::msg('gridblock_modulepreview_module_edit'));
    $list->setColumnParams('name', ['func' => 'edit', 'id' => '###id###', 'start' => rex_request('start', 'int', 0)]);
    $list->setColumnParams('edit', ['func' => 'edit', 'id' => '###id###', 'start' => rex_request('start', 'int', 0)]);
    // Holzhammer: Leere Bilder suchen und durch Platzhalter ersetzen
    $list = $list->get();
    $list = str_replace('src="/media/gridblock_modulepreview_thumbnail/"', 'src="' . rex_url::pluginAssets('gridblock', 'modulepreview', 'thumbnail.jpg') . '"', $list);
    // Ins Fragment packen
    $fragment = new rex_fragment();
    $fragment->setVar('class', 'edit', false);
    $fragment->setVar('title', rex_i18n::msg('gridblock_modulepreview_modules'), false);
    $fragment->setVar('body', $list, false);
    echo $fragment->parse('core/page/section.php');
} // Eo if not edit

// If edit
if (rex_request('func', 'string') === "edit" && rex_request('id', 'int') !== "") {
    $id = rex_request('id', 'int');

    $formLabel = rex_i18n::msg('gridblock_modulepreview_edit') . ' [ID: ' . rex_get('id') . ']';

    $form = rex_form::factory(rex::getTable('module'), '', 'id=' . $id);

    $field = $form->addTextField('gridblock_modulepreview_description');
    $field->setLabel(rex_i18n::msg('gridblock_modulepreview_description'));

    $field = $form->addMediaField('gridblock_modulepreview_thumbnail');
    $field->setLabel(rex_i18n::msg('gridblock_modulepreview_thumbnail'));
    $field->setTypes('jpg,jpeg,png,gif');

    $form->addParam('id', $id);

    $content = $form->get();

    $fragment = new rex_fragment();
    $fragment->setVar('class', 'edit', false);
    $fragment->setVar('title', $formLabel, false);
    $fragment->setVar('body', $content, false);
    echo $fragment->parse('core/page/section.php');

    // Bisschen hacky den Löschen-Button ausblenden
    echo '<style>#rex-addon-editmode .btn-delete{display: none !important;}</style>';
}
