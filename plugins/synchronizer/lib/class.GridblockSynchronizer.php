<?php class GridblockSynchronizer
{
    private static $aLogs = array();

    private static function getPlugin()
    {
        return rex_plugin::get('gridblock', 'synchronizer');
    }

    private static function getPath($sFilename = null)
    {
        return self::getPlugin()->getDataPath() . "templates/" . $sFilename;
    }

    public static function saveFile($sType = "template.php", $iTemplateId, $sMarkup = null)
    {
        $iTemplateId = intval($iTemplateId);
        if (!is_dir(self::getPath("template_" . $iTemplateId))) {
            array_push(self::$aLogs, "creating folder: " . self::getPath("template_" . $iTemplateId));
            mkdir(self::getPath("template_" . $iTemplateId), 0755);
        }

        $sFilename = self::getPath("template_" . $iTemplateId . "/" . $sType);
        file_put_contents($sFilename, $sMarkup);
        array_push(self::$aLogs, "saving markunp in: " . $sFilename);

        self::syncWithThemeAddon();
    }

    public static function deleteFile($sType = "template.php", $iTemplateId)
    {
        $iTemplateId = intval($iTemplateId);
        $sFilename = self::getPath("template_" . $iTemplateId . "/" . $sType);
        if (file_exists($sFilename)) {
            unlink($sFilename);
            array_push(self::$aLogs, "deleting file: " . $sFilename);
        }
    }

    private static function getSubDirectories($sDirectory, $sSeperator = '/')
    {
        $dirs = array_map(function ($item) use ($sSeperator) {
            return $item . $sSeperator;
        }, array_filter(glob($sDirectory . '*'), 'is_dir'));

        foreach ($dirs as $dir) {
            $dirs = array_merge($dirs, self::getSubDirectories($dir, $sSeperator));
        }
        return $dirs;
    }

    private static function isDirectoryEmpty($sDirectory)
    {
        $handle = opendir($sDirectory);
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                closedir($handle);
                return false;
            }
        }
        closedir($handle);
        return true;
    }

    public static function sync()
    {
        array_push(self::$aLogs, "GridblockSynchronizer START");

        self::syncWithThemeAddon();

        $aFoundFiles = array();

        // check project contentsettings.json
        if (rex_plugin::get('gridblock', 'contentsettings')->isAvailable()) {
            $sFile = self::getPlugin()->getDataPath("contentsettings.json");
            $sFileOrigin = rex_plugin::get('gridblock', 'contentsettings')->getDataPath("contentsettings.json");
            if (!file_exists($sFile) && file_exists($sFileOrigin)) {
                copy($sFileOrigin, $sFile);
            } else if (file_exists($sFile) && !file_exists($sFileOrigin)) {
                copy($sFile, $sFileOrigin);
            } else if (file_exists($sFile) && file_exists($sFileOrigin)) {
                if (filectime($sFile) > filectime($sFileOrigin)) {
                    copy($sFile, $sFileOrigin);
                } else {
                    copy($sFileOrigin, $sFile);
                }
            }
            array_push($aFoundFiles, $sFile);
        }

        // check templates
        $db = rex_sql::factory();
        $db->setQuery("SELECT id,template,preview,updatedate FROM " . rex::getTable('1620_gridtemplates') . " ORDER BY prio ASC");
        foreach ($db as $oRes) {
            $oDate = new DateTime($oRes->getValue("updatedate"));
            $iTemplateId = $oRes->getValue("id");
            $iUpdatedate = $oDate->format('U');
            $bDoUpdate = false;
            $sMarkupTemplate = $sMarkupDefinition = "";
            $sMarkupTemplateDatabase = $oRes->getValue("template");
            $sMarkupDefinitionDatabase = $oRes->getValue("preview");

            $sPath = self::getPath("template_" . $iTemplateId . "/");

            // check template
            $sFile = $sPath . "template.php";
            array_push(self::$aLogs, "checking template: " . $sFile);
            if (!file_exists($sFile)) {
                self::saveFile("template.php", $iTemplateId, $oRes->getValue("template"));
            } else {
                $sMarkupTemplate = file_get_contents($sFile);
                if (md5($sMarkupTemplateDatabase) != md5($sMarkupTemplate)) {
                    if (filectime($sFile) > $iUpdatedate) {
                        $bDoUpdate = true;
                        array_push(self::$aLogs, "updating database from file: " . $sFile);
                    } else {
                        array_push(self::$aLogs, "updating file from database: " . $sFile);
                        self::saveFile("template.php", $iTemplateId, $oRes->getValue("template"));
                    }
                } else {
                    array_push(self::$aLogs, "markup is equal. nothing to do");
                }
            }
            array_push($aFoundFiles, $sFile);

            // check definition
            $sFile = $sPath . "definition.json";
            array_push(self::$aLogs, "checking definition: " . $sFile);
            if (!file_exists($sFile)) {
                //echo "Lege Datei " . $sFile . " an<br>";
                self::saveFile("definition.json", $iTemplateId, $oRes->getValue("preview"));
            } else {
                $sMarkupDefinition = file_get_contents($sFile);
                if (md5($sMarkupDefinitionDatabase) != md5($sMarkupDefinition)) {
                    if (filectime($sFile) > $iUpdatedate) {
                        $bDoUpdate = true;
                        array_push(self::$aLogs, "updating database from file: " . $sFile);
                    } else {
                        array_push(self::$aLogs, "updating file from database: " . $sFile);
                        self::saveFile("definition.json", $iTemplateId, $oRes->getValue("preview"));
                    }
                } else {
                    array_push(self::$aLogs, "markup is equal. nothing to do");
                }
            }
            array_push($aFoundFiles, $sFile);

            if ($bDoUpdate == true) {
                $oDb = rex_sql::factory();
                $oDb->setTable(rex::getTable('1620_gridtemplates'));
                $oDb->setValue("template", $sMarkupTemplate);
                $oDb->setValue("preview", $sMarkupDefinition);
                $oDb->setWhere("id = '" . $iTemplateId . "'");
                $oDb->addGlobalUpdateFields();
                $oDb->update();
                array_push(self::$aLogs, "finally updating database");
            }
        }

        // delete unused data
        array_push(self::$aLogs, "deleting unused files");
        $aFolders = self::getSubDirectories(self::getPath());
        foreach ($aFolders as $sFolder) {
            foreach (glob($sFolder . "*") as $sFilename) {
                if (!in_array($sFilename, $aFoundFiles)) {
                    array_push(self::$aLogs, "deleting unused file: " . $sFilename);
                    unlink($sFilename);
                }
            }
            if (self::isDirectoryEmpty($sFolder)) {
                array_push(self::$aLogs, "deleting unused folder: " . $sFolder);
                rmdir($sFolder);
            }
        }
        foreach (glob(self::getPath() . "*.{*}", GLOB_BRACE) as $sFilename) {
            if (!in_array($sFilename, $aFoundFiles)) {
                array_push(self::$aLogs, "deleting unused file: " . $sFilename);
                unlink($sFilename);
            }
        }

        array_push(self::$aLogs, "GridblockSynchronizer END");

        return self::$aLogs;
    }

    private static function syncWithThemeAddon()
    {
        if (!rex_addon::get('theme')->isAvailable()) {
            array_push(self::$aLogs, "addon theme not available");
            return;
        }

        $oTheme = rex_addon::get('theme');
        $sThemePath = rex_path::base($oTheme->getProperty('theme_folder') . "/private/redaxo/gridblock/");
        array_push(self::$aLogs, "sync theme folder START");

        if (!is_dir($sThemePath)) {
            array_push(self::$aLogs, "creating theme folder: $sThemePath");
            mkdir($sThemePath, 0775, true);
        }

        $aFoundFiles = array();

        $db = rex_sql::factory();
        $db->setQuery("SELECT id,template,preview FROM " . rex::getTable('1620_gridtemplates') . " ORDER BY prio ASC");
        foreach ($db as $oRes) {
            $iTemplateId = $oRes->getValue("id");
            $sPath = self::getPath("template_" . $iTemplateId . "/");
            $sThemeTemplatePath = $sThemePath . "template_" . $iTemplateId . "/";

            if (!is_dir($sThemeTemplatePath)) {
                array_push(self::$aLogs, "creating theme templatefolder: $sThemeTemplatePath");
                mkdir($sThemeTemplatePath, 0755);
            }

            $aFileTypes = array(
                "template" => array(
                    "filename" => "template.php",
                    "field" => "template"
                ),
                "definition" => array(
                    "filename" => "definition.json",
                    "field" => "preview"
                )
            );

            foreach ($aFileTypes as $sFileType => $aFileArgs) {
                $sThemeFile = $sThemeTemplatePath . $aFileArgs["filename"];
                array_push(self::$aLogs, "checking $sFileType: " . $sThemeFile);
                array_push($aFoundFiles, $sThemeFile);

                if (!file_exists($sThemeFile)) {
                    file_put_contents($sThemeFile, $oRes->getValue($aFileArgs["field"]));
                    array_push(self::$aLogs, "creating $sFileType: " . $sThemeFile);
                } else {
                    $sPluginFile = self::getPath("template_" . $iTemplateId . "/" . $aFileArgs["filename"]);
                    if (!file_exists($sPluginFile)) {
                        file_put_contents($sThemeFile, $oRes->getValue($aFileArgs["field"]));
                        array_push(self::$aLogs, "updating $sFileType from database: " . $sThemeFile);
                    } else {
                        $sMd5Plugin = md5_file($sPluginFile);
                        $sMd5Theme = md5_file($sThemeFile);

                        if ($sMd5Plugin != $sMd5Theme) {
                            $iPluginFiledate = filectime($sPluginFile);
                            $iThemeFiledate = filectime($sThemeFile);
                            if ($iPluginFiledate > $iThemeFiledate) {
                                $sContent = file_get_contents($sPluginFile);
                                file_put_contents($sThemeFile, $sContent);
                                array_push(self::$aLogs, "updating theme file from plugin");
                            } else {
                                $sContent = file_get_contents($sThemeFile);
                                file_put_contents($sPluginFile, $sContent);
                                array_push(self::$aLogs, "updating plugin file from theme");
                            }
                        } else {
                            array_push(self::$aLogs, "markup is equal. nothing to do");
                        }
                    }
                }
            }



            if (rex_plugin::get('gridblock', 'contentsettings')->isAvailable()) {
                array_push(self::$aLogs, "sync contentsettings");

                $oSettings = rex_plugin::get('gridblock', 'contentsettings');


                // check project contentsettings.json
                $sFile = $sThemePath . "contentsettings.json";
                $sFileOrigin = $oSettings->getDataPath("contentsettings.json");
                if (!file_exists($sFile) && file_exists($sFileOrigin)) {
                    copy($sFileOrigin, $sFile);
                } else if (file_exists($sFile) && !file_exists($sFileOrigin)) {
                    copy($sFile, $sFileOrigin);
                } else if (file_exists($sFile) && file_exists($sFileOrigin)) {
                    if (filectime($sFile) > filectime($sFileOrigin)) {
                        copy($sFile, $sFileOrigin);
                    } else {
                        copy($sFileOrigin, $sFile);
                    }
                }
                array_push($aFoundFiles, $sFile);



                $sPluginFile = $oSettings->getDataPath("templates/template_" . $iTemplateId . "/contentsettings.json");
                $sThemeFile = $sThemeTemplatePath . "contentsettings.json";
                array_push($aFoundFiles, $sThemeFile);

                if (file_exists($sPluginFile)) {
                    if (!file_exists($sThemeFile)) {
                        copy($sPluginFile, $sThemeFile);
                    } else {
                        $sPluginMd5 = md5_file($sPluginFile);
                        $sThemeMd5 = md5_file($sThemeFile);

                        if ($sPluginMd5 != $sThemeMd5) {
                            $iPluginFiledate = filectime($sPluginFile);
                            $iThemeFiledate = filectime($sThemeFile);
                            if ($iPluginFiledate > $iThemeFiledate) {
                                $sContent = file_get_contents($sPluginFile);
                                file_put_contents($sThemeFile, $sContent);
                                array_push(self::$aLogs, "updating theme file from plugin");
                            } else {
                                $sContent = file_get_contents($sThemeFile);
                                file_put_contents($sPluginFile, $sContent);
                                array_push(self::$aLogs, "updating plugin file from theme");
                            }
                        } else {
                            array_push(self::$aLogs, "markup is equal. nothing to do");
                        }
                    }
                } else if (file_exists($sThemeFile)) {
                    $sPluginTemplatePath = $oSettings->getDataPath("templates/template_" . $iTemplateId . "/");
                    if (!is_dir($sPluginTemplatePath)) {
                        array_push(self::$aLogs, "creating plugin templatefolder: $sPluginTemplatePath");
                        mkdir($sPluginTemplatePath, 0755);
                    }
                    copy($sThemeFile, $sPluginFile);
                }
            }
        }

        // delete unused data
        array_push(self::$aLogs, "deleting unused files");
        $aFolders = self::getSubDirectories($sThemePath);
        foreach ($aFolders as $sFolder) {
            foreach (glob($sFolder . "*") as $sFilename) {
                if (!in_array($sFilename, $aFoundFiles)) {
                    array_push(self::$aLogs, "deleting unused file: " . $sFilename);
                    unlink($sFilename);
                }
            }
            if (self::isDirectoryEmpty($sFolder)) {
                array_push(self::$aLogs, "deleting unused folder: " . $sFolder);
                rmdir($sFolder);
            }
        }
        foreach (glob($sThemePath . "*.{*}", GLOB_BRACE) as $sFilename) {
            if (!in_array($sFilename, $aFoundFiles)) {
                array_push(self::$aLogs, "deleting unused file: " . $sFilename);
                unlink($sFilename);
            }
        }
        array_push(self::$aLogs, "sync theme folder END");
    }
}
