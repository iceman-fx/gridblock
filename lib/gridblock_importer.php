<?php
/*
	Redaxo-Addon Gridblock
	Importer-Funktionen für templates
	v1.0
	by Falko Müller & Daniel Steffen @ 2021 (based on 0.1.0-dev von bloep)
*/

class rex_gridblock_importer
{
	static $aTemplateFiles = array(
		"template.php",
		"template.json",
		"definition.json"
	);

	public static function import($aFile)
	{
		$oAddon = rex_addon::get('gridblock');

		$sReturn = $oAddon->i18n('a1620_error_templates_notimported');

		if (!empty($aFile)) :

			$bDirCreated = false;

			if (!is_dir($oAddon->getDataPath())) {
				$bDirCreated = true;
				mkdir($oAddon->getDataPath(), 0775, true);
			}

			$iRand = rand(1000, 100000) . time();
			$sFolderImport = "import/import_" . $iRand;
			mkdir($oAddon->getDataPath($sFolderImport), 0775, true);

			move_uploaded_file($aFile["tmp_name"], $oAddon->getDataPath($sFolderImport) . "/import.zip");

			$zip = new ZipArchive;
			if ($zip->open($oAddon->getDataPath($sFolderImport) . "/import.zip")) {
				$zip->extractTo($oAddon->getDataPath($sFolderImport));
				$zip->close();
			}
			unlink($oAddon->getDataPath($sFolderImport) . "/import.zip");


			$oPluginSettings = rex_plugin::get('gridblock', 'contentsettings');

			$aItems = scandir($oAddon->getDataPath($sFolderImport));

			foreach ($aItems as $sItem) {

				if ($sItem != "." && $sItem != "..") {
					// template folder
					if (is_dir($oAddon->getDataPath($sFolderImport) . "/" . $sItem)) {
						foreach (self::$aTemplateFiles as $sTemplateFile) {
							if (!file_exists($oAddon->getDataPath($sFolderImport) . "/" . $sItem . "/" . $sTemplateFile)) {
								return $oAddon->i18n('a1620_error_templates_notimported_file_missing');
							}
						}

						$aTemplate = json_decode(file_get_contents($oAddon->getDataPath($sFolderImport) . "/" . $sItem . "/template.json"), true);
						$sTemplate = file_get_contents($oAddon->getDataPath($sFolderImport) . "/" . $sItem . "/template.php");
						$sDefinition = file_get_contents($oAddon->getDataPath($sFolderImport) . "/" . $sItem . "/definition.json");
						$aDefinition = json_decode($sDefinition, true);

						$iPrio = "1";
						if (isset($aTemplate["prio"]) && $aTemplate["prio"] > "0") {
							$iPrio = intval($aTemplate["prio"]);
							$oDb = rex_sql::factory();
							$oDb->setQuery("UPDATE " . rex::getTable('1620_gridtemplates') . " SET prio=prio+1 WHERE prio >= '$iPrio'");
						} else {
							$oDb = rex_sql::factory();
							$oDb->setQuery("SELECT id FROM " . rex::getTable('1620_gridtemplates'));
							$iPrio = $oDb->getRows() + 1;
						}

						$oDb = rex_sql::factory();
						$oDb->setTable(rex::getTable('1620_gridtemplates'));
						$oDb->setValue("title", $aTemplate["title"]);
						$oDb->setValue("description", $aTemplate["description"]);
						$oDb->setValue("columns", $aDefinition["totalcolumns"]);
						$oDb->setValue("prio", $iPrio);
						$oDb->setValue("template", $sTemplate);
						$oDb->setValue("preview", $sDefinition);
						$oDb->addGlobalCreateFields();
						$oDb->insert();

						$iTemplateId = $oDb->getLastId();

						if ($oPluginSettings->isAvailable() && file_exists($oAddon->getDataPath($sFolderImport) . "/" . $sItem . "/contentsettings.json")) {
							mkdir($oPluginSettings->getDataPath("templates/template_" . $iTemplateId), 0775, true);
							copy($oAddon->getDataPath($sFolderImport) . "/" . $sItem . "/contentsettings.json", $oPluginSettings->getDataPath("templates/template_" . $iTemplateId . "/contentsettings.json"));
						}

						// project contentsettings.json
					} else {
						if ($oPluginSettings->isAvailable() && $sItem == "contentsettings.json") {
							copy($oAddon->getDataPath($sFolderImport) . "/" . $sItem, $oPluginSettings->getDataPath($sItem));
						}
					}
				}
			}

			if ($bDirCreated) {
				self::rmDir($oAddon->getDataPath());
			} else {
				self::rmDir($oAddon->getDataPath("import/"));
			}

			//alle Templates synchronisieren
			if (rex_plugin::get('gridblock', 'synchronizer')->isAvailable()) :
				GridblockSynchronizer::sync();
			endif;

			$sReturn = "success";
		endif;

		return $sReturn;
	}

	private static function rmDir($sDir)
	{
		if (is_dir($sDir)) {
			$objects = scandir($sDir);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (is_dir($sDir . DIRECTORY_SEPARATOR . $object) && !is_link($sDir . "/" . $object))
						self::rmDir($sDir . DIRECTORY_SEPARATOR . $object);
					else
						unlink($sDir . DIRECTORY_SEPARATOR . $object);
				}
			}
			rmdir($sDir);
		}
	}
}
?>