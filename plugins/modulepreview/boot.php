<?php rex_extension::register('GRIDBLOCK_MODULESELECTOR_ADD', array('GridblockModulepreview', 'parseExtensionPoint'), rex_extension::LATE);

if ($this->getConfig("overwrite_module_select")) {
    if (!file_exists($this->getPath()."fragments/module_select.php")) {
        copy($this->getPath()."fragments/module_select.tpl",$this->getPath()."fragments/module_select.php");
    }
} else {
    if (file_exists($this->getPath()."fragments/module_select.php")) {
        unlink($this->getPath()."fragments/module_select.php");
    }
}

#rex_extension::register('STRUCTURE_CONTENT_MODULE_SELECT', ['GridblockModulepreview', 'parseModuleSelect'],rex_extension::LATE);