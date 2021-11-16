<?php class GridblockContentSettings
{


    public function __construct($iModuleId = null)
    {
        $this->addon = rex_addon::get('gridblock');
        $this->plugin = rex_plugin::get('gridblock', 'contentsettings');
        $this->fileProject = $this->plugin->getDataPath('contentsettings.json');

        $this->iSettingsId = "20";
        $this->iTemplateId = "0";
        $this->iColumnId = "0";
        $this->getAllSettings();
    }

    function getAllSettings($sType = "template")
    {
        $this->aSettings = [];

        if (file_exists($this->fileProject)) {
            $sProject = $this->getJsonContent($this->fileProject);
            $this->projectData = (json_decode($sProject, true));
        }

        if ($this->iTemplateId) {
            if (!$this->iColumnId) {
                if (file_exists($this->plugin->getDataPath("templates/template_" . $this->iTemplateId . "/contentsettings.json"))) {
                    $sTemplate = $this->getJsonContent($this->plugin->getDataPath("templates/template_" . $this->iTemplateId . "/contentsettings.json"));
                    $this->templateData = json_decode($sTemplate, true)["template"];
                }
            } else {
                if (file_exists($this->plugin->getDataPath("templates/template_" . $this->iTemplateId . "/contentsettings.json"))) {
                    $sTemplate = $this->getJsonContent($this->plugin->getDataPath("templates/template_" . $this->iTemplateId . "/contentsettings.json"));
                    $this->templateData = json_decode($sTemplate, true)["columns"][$this->iColumnId];
                }
            }
            if (file_exists($this->plugin->getDataPath("templates/template_" . $this->iTemplateId . "/contentsettings.json"))) {
                $sTemplate = $this->getJsonContent($this->plugin->getDataPath("templates/template_" . $this->iTemplateId . "/contentsettings.json"));
                $this->templateGeneralData = json_decode($sTemplate, true);
            }
        }


        // ShowOptions

        if (isset($this->projectData["showOptions"])) {
            if (count($this->projectData["showOptions"])) {
                $this->aSettings["showOptions"] = $this->projectData["showOptions"];
            }
        }

        if (isset($this->projectData[$sType]["showOptions"])) {
            if (count($this->projectData[$sType]["showOptions"])) {
                $this->aSettings["showOptions"] = $this->projectData[$sType]["showOptions"];
            }
        }

        if (isset($this->templateData["showOptions"])) {
            if (count($this->templateData["showOptions"])) {
                $this->aSettings["showOptions"] = $this->templateData["showOptions"];
            }
        }

        // Options
        $aUsedKeys = [];

        if (isset($this->projectData["options"])) {
            if (count($this->projectData["options"])) {
                foreach ($this->projectData["options"] as $aOption) {
                    $sKey = $aOption["key"];
                    if (!in_array($sKey, $aUsedKeys)) {
                        $this->aSettings["options"][$sKey] = $aOption;
                        array_push($aUsedKeys, $sKey);
                    } else {
                        foreach ($aOption as $sOptionKey => $mOptionVal) {
                            if (isset($mOptionVal)) {
                                $this->aSettings["options"][$sKey][$sOptionKey] = $mOptionVal;
                            }
                        }
                    }
                }
            }
        }

        if (isset($this->projectData[$sType]["options"])) {
            if (count($this->projectData[$sType]["options"])) {
                foreach ($this->projectData[$sType]["options"] as $aOption) {
                    $sKey = $aOption["key"];
                    if (!in_array($sKey, $aUsedKeys)) {
                        $this->aSettings["options"][$sKey] = $aOption;
                        array_push($aUsedKeys, $sKey);
                    } else {
                        foreach ($aOption as $sOptionKey => $mOptionVal) {
                            if (isset($mOptionVal)) {
                                $this->aSettings["options"][$sKey][$sOptionKey] = $mOptionVal;
                            }
                        }
                    }
                }
            }
        }

        if (isset($this->templateGeneralData["options"])) {
            if (count($this->templateGeneralData["options"])) {
                foreach ($this->templateGeneralData["options"] as $aOption) {
                    $sKey = $aOption["key"];
                    if (!in_array($sKey, $aUsedKeys)) {
                        $this->aSettings["options"][$sKey] = $aOption;
                        array_push($aUsedKeys, $sKey);
                    } else {
                        foreach ($aOption as $sOptionKey => $mOptionVal) {
                            if (isset($mOptionVal)) {
                                $this->aSettings["options"][$sKey][$sOptionKey] = $mOptionVal;
                            }
                        }
                    }
                }
            }
        }

        if (isset($this->templateData["options"])) {
            if (count($this->templateData["options"])) {
                foreach ($this->templateData["options"] as $aOption) {
                    $sKey = $aOption["key"];
                    if (!in_array($sKey, $aUsedKeys)) {
                        $this->aSettings["options"][$sKey] = $aOption;
                        array_push($aUsedKeys, $sKey);
                    } else {
                        foreach ($aOption as $sOptionKey => $mOptionVal) {
                            if (isset($mOptionVal)) {
                                $this->aSettings["options"][$sKey][$sOptionKey] = $mOptionVal;
                            }
                        }
                    }
                }
            }
        }
    }

    public function getOptions($sKey)
    {
        $aSelectData = $this->getSelectData($sKey);
        if (isset($aSelectData)) {
            if (count($aSelectData)) {
                $this->aSettings["options"][$sKey]["selectdata"] = $aSelectData;
            }
        }
        return $this->aSettings["options"][$sKey];
    }

    public function getDefault($sKey)
    {
        if (isset($this->aSettings["options"][$sKey]["default"])) {
            return $this->aSettings["options"][$sKey]["default"];
        }
    }

    function getOptionLabel($sKey, $sValue)
    {
        return $this->aSettings["options"][$sKey]["selectdata"][$sValue];
    }

    function getSelectData($sKey)
    {

        $aOption = $this->aSettings["options"][$sKey];

        $aData = array();

        if (isset($aOption["data"])) {
            foreach ($aOption["data"] as $sOptionsKey => $sOptionsVal) {
                if (isset($aOption["default"])) {
                    if ($sOptionsKey == $aOption["default"]) {
                        $sOptionsKey = "gridblockcontentsettingsdefault";
                        $sOptionsVal .= " " . $this->plugin->i18n("gridblockcontentsettings_default_option");
                    }
                }
                $aData[$sOptionsKey] = $sOptionsVal;
            }
        }

        return $aData;
    }


    function getGridblockContentSettingsForm($aSavedOptions = array(), $iTemplateId = "0", $iColumnId = "0")
    {
        $this->iTemplateId = $iTemplateId;
        $this->iColumnId = $iColumnId;
        $sType = $iColumnId ? "columns" : "template";
        $this->getAllSettings($sType);


        if ($iColumnId) {
            $sType = "column_" . $iColumnId;
        } else {
            $sType = "template";
        }

        $aUsedTypes = array();
        if (isset($this->aSettings["showOptions"])) {
            if (count($this->aSettings["showOptions"])) {
                $sForm = '<div class="rex-form-group">';

                foreach ($this->aSettings["showOptions"] as $sKey) {
                    $aOption = $this->aSettings["options"][$sKey];
                    if (isset($aOption)) {
                        if (count($aOption)) {

                            if (!in_array($aOption["type"], $aUsedTypes)) {
                                array_push($aUsedTypes, $aOption["type"]);
                            }

                            if ($aOption["type"] != "html") {
                                $sForm .= '<dl class="rex-form-group form-group gridblockcontentsettings">' . PHP_EOL;
                            }
                            if ($aOption["label"] != "") {
                                $sForm .= '<dt><label for="">' . $aOption["label"] . ':</label></dt>' . PHP_EOL;
                            }

                            switch ($aOption["type"]) {
                                case "text":
                                    $sPlaceholder = '';
                                    if (isset($aOption["placeholder"])) {
                                        $sPlaceholder = 'placeholder="' . $aOption['placeholder'] . '"';
                                    }
                                    $sForm .= '<dd><input name="REX_INPUT_VALUE[' . $this->iSettingsId . '][' . $sType . '][' . $sKey . ']" type="text" class="form-control" value="' . @$aSavedOptions[$sType][$sKey] . '" ' . $sPlaceholder . '></dd>' . PHP_EOL;
                                    break;

                                case "textarea":
                                    $sPlaceholder = '';
                                    if (isset($aOption["placeholder"])) {
                                        $sPlaceholder = 'placeholder="' . $aOption['placeholder'] . '"';
                                    }
                                    $sForm .= '<dd><textarea name="REX_INPUT_VALUE[' . $this->iSettingsId . '][' . $sType . '][' . $sKey . ']" class="form-control" ' . $sPlaceholder . '>' . @$aSavedOptions[$sType][$sKey] . '</textarea></dd>' . PHP_EOL;
                                    break;

                                case "colorpicker":
                                    $sPlaceholder = 'placeholder="Bsp. #003366"';
                                    if (isset($aOption["placeholder"])) {
                                        $sPlaceholder = 'placeholder="' . $aOption['placeholder'] . '"';
                                    }
                                    $sForm .= '<dd><div class="input-group gridblock-colorinput-group"><input data-parsley-excluded="true" type="text" name="REX_INPUT_VALUE[' . $this->iSettingsId . '][' . $sType . '][' . $sKey . ']" value="' . @$aSavedOptions[$sType][$sKey] . '" maxlength="7" ' . $sPlaceholder . ' pattern="^#([A-Fa-f0-9]{6})$" class="form-control novinet"><span class="input-group-addon gridblock-colorinput"><input type="color" value="' . @$aSavedOptions[$sType][$sKey] . '" pattern="^#([A-Fa-f0-9]{6})$" class="form-control"></span></div>' . PHP_EOL;
                                    break;

                                case "select":
                                    $aSelectData = $this->getSelectData($sKey);
                                    $sForm .= '<dd><select name="REX_INPUT_VALUE[' . $this->iSettingsId . '][' . $sType . '][' . $sKey . ']" class="selectpicker w-100">' . PHP_EOL;
                                    foreach ($aSelectData as $sSelectKey => $sSelectValue) :
                                        if (isset($aSavedOptions[$sType][$sKey])) {
                                            $sSelected = ($sSelectKey == @$aSavedOptions[$sType][$sKey]) ? 'selected="selected"' : '';
                                        } else {
                                            $sSelected = ($sSelectKey == "gridblockcontentsettingsdefault") ? 'selected="selected"' : '';
                                        }
                                        $sForm .= '<option value="' . $sSelectKey . '" ' . $sSelected . '>' . $sSelectValue . '</option>' . PHP_EOL;
                                    endforeach;
                                    $sForm .= '</select></dd>' . PHP_EOL;
                                    break;

                                case "checkbox":
                                    $sChecked = ("1" == @$aSavedOptions[$sType][$sKey]) ? 'checked="checked"' : '';
                                    $sForm .= '<dd><div class="checkbox toggle"><label><input type="checkbox" name="REX_INPUT_VALUE[' . $this->iSettingsId . '][' . $sType . '][' . $sKey . ']" value="1" ' . $sChecked . '></label></div></dd>' . PHP_EOL;
                                    break;

                                case "radio":
                                    $sForm .= '<dd><div class="radio toggle switch">' . PHP_EOL;
                                    $aSelectData = $this->getSelectData($sKey);
                                    foreach ($aSelectData as $sSelectKey => $sSelectValue) :
                                        $iRand = rand(0, 1000000) * rand(0, 100000);
                                        if (isset($aSavedOptions[$sType][$sKey])) {
                                            $sSelected = ($sSelectKey == @$aSavedOptions[$sType][$sKey]) ? 'checked="checked"' : '';
                                        } else {
                                            $sSelected = ($sSelectKey == "gridblockcontentsettingsdefault") ? 'checked="checked"' : '';
                                        }
                                        $sForm .= '<label for="' . $iRand . '">' . PHP_EOL;
                                        $sForm .= '<input id="' . $iRand . '" name="REX_INPUT_VALUE[' . $this->iSettingsId . '][' . $sType . '][' . $sKey . ']" type="radio" value="' . $sSelectKey . '" ' . $sSelected . ' /> ' . $sSelectValue . PHP_EOL;
                                        $sForm .= '</label>';

                                    endforeach;
                                    $sForm .= '</div></dd>' . PHP_EOL;
                                    break;

                                case "media":
                                    $aArgs = array();
                                    if (isset($aOption["preview"])) {
                                        if ($aOption["preview"]) {
                                            $aArgs["preview"] = "1";
                                        }
                                    }
                                    if (isset($aOption["types"])) {
                                        if ($aOption["types"]) {
                                            $aArgs["types"] = $aOption["types"];
                                        }
                                    }
                                    $iRand = rand(0, 1000000) * rand(0, 100000);
                                    $sForm .= rex_var_media::getWidget($iRand, "REX_INPUT_VALUE[$this->iSettingsId][$sType][$sKey]", @$aSavedOptions[$sType][$sKey], $aArgs);
                                    break;

                                case "medialist":
                                    $aArgs = array();
                                    if ($aOption["preview"]) {
                                        $aArgs["preview"] = "1";
                                    }
                                    if ($aOption["types"]) {
                                        $aArgs["types"] = $aOption["types"];
                                    }
                                    $iRand = rand(0, 1000000) * rand(0, 100000);
                                    $sForm .= rex_var_medialist::getWidget($iRand, "REX_INPUT_VALUE[$this->iSettingsId][$sType][$sKey]", @$aSavedOptions[$sType][$sKey], $aArgs);
                                    break;

                                case "link":
                                    $aArgs = array();
                                    $iRand = rand(0, 1000000) * rand(0, 100000);
                                    $sForm .= rex_var_link::getWidget($iRand, "REX_INPUT_VALUE[$this->iSettingsId][$sType][$sKey]", @$aSavedOptions[$sType][$sKey], $aArgs);
                                    break;

                                case "linklist":
                                    $aArgs = array();
                                    $iRand = rand(0, 1000000) * rand(0, 100000);
                                    $sForm .= rex_var_linklist::getWidget($iRand, "REX_INPUT_VALUE[$this->iSettingsId][$sType][$sKey]", @$aSavedOptions[$sType][$sKey], $aArgs);
                                    break;
                                case "customlink":
                                    if (!rex_addon::get('mform')->isAvailable()) {
                                        $sForm .= 'To use customlink please install addon mform';
                                    } else {
                                        $aArgs = array();
                                        $iRand = rand(0, 100) * rand(0, 100);
                                        $sForm .= rex_var_custom_link::getWidget($iRand, "REX_INPUT_VALUE[$this->iSettingsId][$sType][$sKey]", @$aSavedOptions[$sType][$sKey], $aArgs);
                                    }
                                    break;
                                case "html":
                                    $sForm .= $aOption["text"];
                                    break;
                            }

                            if ($aOption["type"] != "html") {
                                $sForm .= '</dl>' . PHP_EOL;
                            }
                        }
                    }
                }

                if (!count($aUsedTypes)) {
                    return;
                }

                $sForm .= '</div>';
                return ($sForm);
            }
        }
    }

    function parseGridblockContentSettings($aArr, $iTemplateId)
    {
        $iTemplateId = intval($iTemplateId);
        $iNumsColumns = 0;

        $db = rex_sql::factory();
        $db->setQuery("SELECT columns, preview FROM " . rex::getTable('1620_gridtemplates') . " WHERE id = '" . $iTemplateId . "'");
        if ($db->getRows() > 0) {
            $iNumsColumns = $db->getValue('columns', 'int');
        }

        $this->iTemplateId = $iTemplateId;
        $this->getAllSettings();

        $aData = array();
        $aDataLabels = array();

        // template
        if (isset($this->aSettings["showOptions"])) {
            foreach ($this->aSettings["showOptions"] as $sKey) {
                if ($this->aSettings["options"][$sKey]["type"] != "html") {
                    $aData["template"][$sKey] = "";

                    if (!isset($aArr["template"][$sKey]) OR $aArr["template"][$sKey] == "gridblockcontentsettingsdefault") {
                        $aData["template"][$sKey] = $this->getDefault($sKey);
                    } else {
                        $aData["template"][$sKey] = $aArr["template"][$sKey];
                    }

                    $aDataLabels["template"][$sKey] = "";
                    $sValue = $aData["template"][$sKey];
                    $aDataLabels["template"][$sKey] = array("key" => $sValue, "label" => @$this->aSettings["options"][$sKey]["label"], "value" => @$this->aSettings["options"][$sKey]["data"][$sValue]);
                }
            }
        }

        // columns
        for ($iX = 1; $iX <= $iNumsColumns; $iX++) {
            $this->iColumnId = $iX;
            $this->getAllSettings("columns");
            if (isset($this->aSettings["showOptions"])) {
                foreach ($this->aSettings["showOptions"] as $sKey) {
                    if ($this->aSettings["options"][$sKey]["type"] != "html") {
                        $aData["column_" . $iX][$sKey] = "";
                        if (!isset($aArr["column_" . $iX][$sKey]) OR $aArr["column_" . $iX][$sKey] == "gridblockcontentsettingsdefault") {
                            $aData["column_" . $iX][$sKey] = $this->getDefault($sKey);
                        } else {
                            $aData["column_" . $iX][$sKey] = $aArr["column_" . $iX][$sKey];
                        }

                        $aDataLabels["column_" . $iX][$sKey] = "";
                        $sValue = $aData["column_" . $iX][$sKey];
                        $aDataLabels["column_" . $iX][$sKey] = array("key" => $sValue, "label" => @$this->aSettings["options"][$sKey]["label"], "value" => @$this->aSettings["options"][$sKey]["data"][$sValue]);
                    }
                }
            }
        }

        $aData["data_with_labels"] = $aDataLabels;

        $oData = json_decode(json_encode($aData), FALSE);
        return $oData;
    }

    public static function parseCustomLink($sLink = null)
    {
        if ($sLink == "") {
            return;
        }

        // email
        if (strpos($sLink, "mailto:") !== "false") {
            $sEmail = str_replace("mailto:", "", $sLink);
            if (filter_var($sEmail, FILTER_VALIDATE_EMAIL)) {
                $aArr = array(
                    "email" => $sEmail,
                    "type" => "email",
                    "source" => $sLink,
                );
                return $aArr;
            };
        }

        // tel
        if (strpos($sLink, "tel:") !== "false") {
            $sNr = str_replace("tel:", "", $sLink);
            if (filter_var($sNr, FILTER_VALIDATE_EMAIL)) {
                $aArr = array(
                    "number" => $sNr,
                    "type" => "tel",
                    "source" => $sLink,
                );
                return $aArr;
            };
        }

        // external url
        if (filter_var($sLink, FILTER_VALIDATE_URL)) {
            $aArr = array(
                "url" => $sLink,
                "target" => "_blank",
                "type" => "external_url",
                "source" => $sLink,
            );
            return $aArr;
        };



        // internal url
        if (filter_var($sLink, FILTER_VALIDATE_INT)) {
            $aArr = array(
                "url" => rex_getUrl($sLink),
                "target" => "_self",
                "type" => "internal_url",
                "source" => $sLink,
            );
            return $aArr;
        };

        // media
        $oMedia = rex_media::get($sLink);
        if ($oMedia) {
            $aArr = array(
                "url" => $oMedia->getUrl(),
                "type" => "media",
                "media" => $oMedia,
                "source" => $sLink,
            );
            return $aArr;
        }

        // other
        $aArr = array(
            "url" => $sLink,
            "type" => "other",
            "source" => $sLink,
        );
        return $aArr;
    }

    public function getJsonContent($sFile)
    {
        $sContent = "";
        if (file_exists($sFile)) {
            $sContent = file_get_contents($sFile);
            json_decode($sContent);
            if ($sContent && json_last_error() != "JSON_ERROR_NONE") {
                if (rex::isBackend()) {
                    throw new rex_exception("gridblockContentSettings: json Error in File $sFile");
                }
            }
        }
        return $sContent;
    }

    public function getBackendSummary($oData = "", $iTemplateId = 0)
    {

        $aColumns = array();
        $iColumns = "16";
        if ($iTemplateId) {
            $iTemplateId = intval($iTemplateId);
            $oDb = rex_sql::factory();
            $oDb->setQuery("SELECT columns,preview FROM " . rex::getTable('1620_gridtemplates') . " WHERE id = '$iTemplateId' Limit 1");
            $iColumns = $oDb->getValue("columns");
            $aPreview = json_decode($oDb->getValue("preview"), true);
        }




        $sHtml = "";
        $iBlockId = rand(0, 100000) . time() . rand(0, 10000000);
        $sHtml .= '<br /><a href="javascript:void(0)" class="btn btn-abort w-100 text-center nv-contentsettings-toggler-' . $iBlockId . '" data-id="#nv-contentsettings-' . $iBlockId . '" style="width:100%"><strong><span class = "caret"></span> &nbsp; ContentSettings</strong> &nbsp; <span class = "caret"></span></a><br />' . PHP_EOL;
        $sHtml .= '<div id="nv-contentsettings-' . $iBlockId . '" style="border: 1px solid #c1c9d4;border-top:none; padding: 10px 20px;display:none"><br>' . PHP_EOL;

        if (isset($oData->template)) {
            $sHtml .= '<strong>Template</strong>' . PHP_EOL;
            $sHtml .= '<ul class="list-group">' . PHP_EOL;
            foreach ($oData->template as $sKey => $oItem) {
                $sLabel = $oItem->label . " (" . $sKey . ")";
                $sValue = $oItem->key;
                if ($oItem->value != "") {
                    $sValue .= " (" . $oItem->value . ")";
                }
                $sHtml .= '<li class="list-group-item"><div class="row"><div class="col-12 col-lg-6" style="padding:0">' . $sLabel . '</div><div class="col-12 col-lg-6" style="padding:0">' . $sValue . '</div></div></li>' . PHP_EOL;
            }
            $sHtml .= '</ul>' . PHP_EOL;
        }
        for ($iX = 1; $iX <= $iColumns; $iX++) {
            if (isset($oData->{"column_" . $iX})) {
                $sColumnLabel = "Spalte " . $iX;
                $iColumn = $iX - 1;
                if ($aPreview["columns"][$iColumn]["title"] != "") {
                    $sColumnLabel = "Spalte $iX - " . $aPreview["columns"][$iColumn]["title"];
                }

                $sHtml .= '<strong>' . $sColumnLabel . '</strong>' . PHP_EOL;
                $sHtml .= '<ul class="list-group">' . PHP_EOL;
                foreach ($oData->{"column_" . $iX} as $sKey => $oItem) {
                    $sLabel = $oItem->label . " (" . $sKey . ")";
                    $sValue = $oItem->key;
                    if ($oItem->value != "") {
                        $sValue .= " (" . $oItem->value . ")";
                    }
                    $sHtml .= '<li class="list-group-item"><div class="row"><div class="col-12 col-lg-6" style="padding:0">' . $sLabel . '</div><div class="col-12 col-lg-6" style="padding:0">' . $sValue . '</div></div></li>' . PHP_EOL;
                }
                $sHtml .= '</ul>' . PHP_EOL;
            }
        }
        $sHtml .= '</div>' . PHP_EOL;
        $sHtml .= '<script>' . PHP_EOL;
        $sHtml .= '$( document ).ready(function() {
			$(".nv-contentsettings-toggler-' . $iBlockId . '").click(function(){
				var iBlockId = $(this).attr("data-id");
				$(iBlockId).slideToggle();
                console.log("click "+iBlockId);
			});
		})' . PHP_EOL;
        $sHtml .= '</script>' . PHP_EOL;


        #$sHtml .= "<pre>" . print_r($oData, 1) . "</pre>";


        return $sHtml;
    }
}
