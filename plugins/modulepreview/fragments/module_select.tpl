<?php

$oPlugins = rex_plugin::get('gridblock', 'modulepreview');
if ($oPlugins->getConfig("overwrite_module_select")) {
    $aModules = array();

    foreach ($this->items as $aItem) {
        $iId = $aItem["id"];
        $aModules[$iId] = array(
            "name" => $aItem["title"],
            "href" => $aItem["href"],
        );
    }

    if ($oPlugins->getConfig("show_only_gridblock")) {
        $aModules = array();
        foreach ($this->items as $aItem) {
            $iId = $aItem["id"];
            if ($aItem["title"] == "01 - Gridblock") {
                $aModules[$iId] = array(
                    "name" => $aItem["title"],
                    "href" => $aItem["href"],
                );
            }
        }
    }

    $select = GridblockModulepreview::getPreview($aModules, $aParams);
} else {
    $select = new rex_select();
    #$select->setId('rex-add-select-pos-' . $this->position);
    $select->setSize('1');
    $select->addOption(
        rex_i18n::msg('add_block'),
        '',
        0,
        0,
        [
            "style" => "display:none;",
            "class" => "select-placeholder",
            "selected" => "selected",
            "disabled" => "disabled"
        ]
    );
    if ($oPlugins->getConfig("show_only_gridblock")) {
        foreach ($this->items as $item) {
            if ($item['title'] == "01 - Gridblock") {
                $select->addOption($item['title'], str_replace('&amp;', '&', $item['href']));
            }
        }
    } else {
        foreach ($this->items as $item) {
            $select->addOption($item['title'], str_replace('&amp;', '&', $item['href']));
        }
    }
    #$select->setAttribute('id', 'rex-select-pos-' . $this->position);
    $select->setAttribute('class', 'form-control selectpicker');
    $select->setAttribute('onchange', 'window.location = this.options[this.selectedIndex].value;');

    if (count($this->items) > rex_addon::get('modulsuche')->getConfig('limit')) {
        $select->setAttribute('data-live-search', 'true');
    }
    $select = $select->get();
}
echo $select;
