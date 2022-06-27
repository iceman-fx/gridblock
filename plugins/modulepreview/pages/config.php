<?php

$plugin = rex_plugin::get('gridblock', 'modulepreview');
$form = rex_config_form::factory($this->getProperty('package'));



$field = $form->addSelectField('items_per_row', $value = null, ['class' => 'form-control selectpicker']);
$field->setLabel($this->i18n('gridblock_modulepreview_config_items_per_row'));
$select = $field->getSelect();
$select->addOption("1", "1");
$select->addOption("2", "2");
$select->addOption("3", "3");
$select->addOption("4", "4");

$field = $form->addSelectField('overwrite_module_select', $value = null, ['class' => 'form-control selectpicker']);
$field->setLabel($this->i18n('gridblock_modulepreview_config_overwrite_module_select'));
$select = $field->getSelect();
$select->addOption("Inaktiv", "0");
$select->addOption("Aktiv", "1");

$field = $form->addSelectField('show_only_gridblock', $value = null, ['class' => 'form-control selectpicker']);
$field->setLabel($this->i18n('gridblock_modulepreview_config_show_only_gridblock'));
$select = $field->getSelect();
$select->addOption("Inaktiv", "0");
$select->addOption("Aktiv", "1");



$fragment = new rex_fragment();
$fragment->setVar('class', 'edit', false);
$fragment->setVar('title', rex_i18n::msg('gridblock_modulepreview_config'), false);
$fragment->setVar('body', $form->get(), false);
echo $fragment->parse('core/page/section.php');
