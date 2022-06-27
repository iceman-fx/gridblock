<?php class GridblockModulepreview
{

    private static function getPlugin()
    {
        return rex_plugin::get('gridblock', 'modulepreview');
    }

    public static function parseExtensionPoint($ep)
    {
        $aParams = $ep->getParams();
        $aModules = $aParams["allowedmodules"];
        $sHtml = self::getPreview($aModules, $aParams);
        return $sHtml;
    }

    public static function getPreview($aModules, $aParams = array())
    {
        $aItems = self::parseModules($aModules);

        if (!count($aItems)) {
            return;
        }


        $sHtml = "";
        $sHtml .= '<div class="dropdown btn-block">';
        $sHtml .= '<a class="btn btn-default btn-block btn-choosegridmodul dropdown-toggle" data-toggle="dropdown" title="' . rex_i18n::msg('a1620_mod_choose_modul') . '"><i class="fa fa-plus"></i>' . rex_i18n::msg('a1620_mod_choose_modul') . ' <span class="caret"></span></a>';

        $sHtml .= '<ul class="dropdown-menu btn-block gridblock-moduleselector" role="menu" data-colid="' . $aParams["colid"] . '" data-uid="' . $aParams["uid"] . '">';

        if ($aParams["copiedmodule"]) {
            $copUID = @$aParams["copiedmodule"]['uid'];
            $copCOLID = @intval($aParams["copiedmodule"]['colid']);
            $copSLID = @intval($aParams["copiedmodule"]['sliceid']);
            $copMODID = @intval($aParams["copiedmodule"]['modid']);
            if (rex_article_content_gridblock::checkCopyAvailable($copUID, $copCOLID, $copSLID) && $copMODID > 0 && rex::getUser()->getComplexPerm('modules')->hasPerm($copMODID)) {
                $module = @$_SESSION['gridAllowedModules'][$copMODID];

                $modName = aFM_maskChar($module['name']);
                $sHtml .= '<li class="gridblock-cutncopy-insert"><a data-copyid="' . $copUID . '" data-modid="' . $copMODID . '" data-modname="' . $modName . '">' . str_replace(array("###modname###", "###modid###"), array($modName, $copMODID), rex_i18n::rawmsg('a1620_mod_copy_insertmodul')) . '</a></li>';
            }
        }


        foreach ($aItems as $aItem) {
            $sHtml .= self::getPreviewMarkup($aItem);
        }

        $sHtml .= '</ul>';
        $sHtml .= '</div>';
        return $sHtml;
    }

    private static function parseModules($aModules)
    {
        $aCategories = array();
        $oDb = rex_sql::factory();
        $oDb->setQuery("SELECT * FROM " . rex::getTable("1620_gridblock_modulepreview_categories") . " WHERE status = '1' ORDER BY prio ASC");
        foreach ($oDb as $oItem) {
            $aCategories[] = array("label" => $oItem->getValue("title"), "description" => $oItem->getValue("description"), "prio" => $oItem->getValue("prio"), "modules" => $oItem->getValue("modules"));
        }


        $aArr = array();
        $aUsedModules = array();
        $bUsedCategories = false;
        foreach ($aCategories as $aCategory) {
            $bUsedCategory = false;
            $aCategoryModules = explode("|", $aCategory["modules"]);
            foreach ($aCategoryModules as $iCategoryModule) {
                if (isset($iCategoryModule)) {
                    if (isset($aModules[$iCategoryModule]) && rex::getUser()->getComplexPerm('modules')->hasPerm($iCategoryModule)) {
                        $bUsedCategories = true;
                        if (!$bUsedCategory) {
                            $aData = array(
                                "type" => "category",
                                "label" => $aCategory["label"],
                                "description" => $aCategory["description"],
                            );
                            $aArr[] = $aData;
                            $bUsedCategory = true;
                        }

                        $aData = array(
                            "type" => "module",
                            "module_id" => $iCategoryModule,
                            "module" => $aModules[$iCategoryModule],
                        );
                        $aArr[] = $aData;
                        $aUsedModules[] = $iCategoryModule;
                    }
                }
            }
        }

        $bUsedCategory = false;

        foreach ($aModules as $iModuleId => $aModule) {
            if (!in_array($iModuleId, $aUsedModules) && rex::getUser()->getComplexPerm('modules')->hasPerm($iModuleId)) {

                if (!$bUsedCategory && $bUsedCategories) {
                    $aData = array(
                        "type" => "category",
                        "label" => "Ohne Kategorie",
                    );
                    $aArr[] = $aData;
                    $bUsedCategory = true;
                }


                $aData = array(
                    "type" => "module",
                    "module_id" => $iModuleId,
                    "module" => $aModule,
                );
                $aArr[] = $aData;
                $aUsedModules[] = $iModuleId;
            }
        }

        return $aArr;
    }

    private static function getPreviewMarkup($aData)
    {

        if ($aData["type"] == "category") {
            $sHtml = '<li class="col-md-12" style="background-color:#efefef;padding:10px"><strong>' . $aData["label"] . '</strong>';
            if ($aData["description"] != "") {
                $sHtml .= '<br><span class="text-muted"><small>' . $aData["description"] . '</small></span>';
            }
            $sHtml .= '</li>';
            return $sHtml;
        }

        if ($aData["type"] == "module") {
            $iModuleId = $aData["module_id"];
            $aModule = $aData["module"];
            $sModName = aFM_maskChar($aModule['name']);
            $sql = rex_sql::factory();
            $sql->setTable(rex::getTable('module'));
            $sql->setWhere(['id' => $iModuleId]);
            $sql->select();

            $fileUrl = rex_url::pluginAssets('gridblock', 'modulepreview', 'thumbnail.jpg');
            if ($sql->getValue('gridblock_modulepreview_thumbnail') !== '') {
                $fileUrl = '/media/gridblock_modulepreview/' . $sql->getValue('gridblock_modulepreview_thumbnail');
            }
            $thumbnail = '<img src=\'' . $fileUrl . '\' alt=\'Thumbnail ' . $sql->getValue('gridblock_modulepreview_thumbnail') . '\'>';

            $description = '';
            if ($sql->getValue('gridblock_modulepreview_description') !== '') {
                $description = '<br /><br /><span class=\'text-muted\'><small>' . $sql->getValue('gridblock_modulepreview_description') . '</small></span>';
            }
            $oPlugin = self::getPlugin();
            $iItemsPerRow = 12 / ($oPlugin->getConfig("items_per_row") ?: "2");
            $sHtml = '<li class="col-md-' . $iItemsPerRow . '"><a data-modid="' . $iModuleId . '" data-modname="' . $sModName . '"';
            if ($aData["module"]["href"]) {
                $sHtml .= 'onclick="window.location.href = \'' . $aData["module"]["href"] . '\'"';
            }
            $sHtml .= '><div class="row" style="padding:10px"><div class="col-md-6">' . $thumbnail . '</div><div class="col-md-6"><strong>' . $sModName . '</strong>' . $description . '</div></div></a></li>' . PHP_EOL;
            return $sHtml;
        }
    }

    public static function parseModuleSelect($ep)
    {
        $sSubject = $ep->getSubject();
        #dump($sSubject);
        preg_match_all('/<li>(.*)<\/li>/ismu', $sSubject, $aMatches);
        dump($aMatches);
        $ep->setSubject($sSubject);
    }
}
