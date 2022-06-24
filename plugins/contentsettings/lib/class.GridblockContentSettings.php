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

        $this->aSettings["categories"] = array();
        if (isset($this->projectData["categories"])) {
            if (count($this->projectData["categories"])) {
                foreach ($this->projectData["categories"] as $aCategory) {
                    $sKey = $aCategory["key"];
                    $sIcon = isset($aCategory["icon"]) ? $aCategory["icon"] : "";
                    $this->aSettings["categories"][$sKey] = array("label" => $aCategory["label"], "icon" => $sIcon);
                }
            }
        }

        if (isset($this->templateData["categories"])) {
            if (count($this->templateData["categories"])) {
                foreach ($this->templateData["categories"] as $aCategory) {
                    $sKey = $aCategory["key"];
                    $sIcon = isset($aCategory["icon"]) ? $aCategory["icon"] : "";
                    $this->aSettings["categories"][$sKey] = array("label" => $aCategory["label"], "icon" => $sIcon);
                }
            }
        }

        // Hide Options
        if (isset($this->templateData["hideOptions"])) {
            foreach ($this->templateData["hideOptions"] as $sKey) {
                if (($iX = array_search($sKey, $this->aSettings["showOptions"])) !== false) {
                    unset($this->aSettings["showOptions"][$iX]);
                }
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
 
        // start_group & end_group
        $iPos = "1";

        foreach ($this->aSettings["showOptions"] as $sOption) {
            $aOption = $this->aSettings["options"][$sOption];
            if ($aOption["type"] == "group") {
                $aNewShowOptions = array();
                $sLabel = $this->aSettings["options"][$sOption]["label"];
                $this->aSettings["options"][$sOption]["type"] = "html";

                $sAccordionOpen = $sAriaExpanded = "";
                if (isset($this->aSettings["options"][$sOption]["open"])) {
                    if ($this->aSettings["options"][$sOption]["open"] == true) {
                        $sAccordionOpen = "in";
                        $sAriaExpanded = 'aria-expanded="true"';
                    }
                }

                $sHtml = '<div class="gridblockcontentsettings-group" id="gridblockcontentsettings-accordion_' . $sType.'_'.$this->iColumnId.'_'.$aOption["key"] . '"><div class="gridblockcontentsettings-heading"><a data-toggle="collapse" data-parent="#gridblockcontentsettings-accordion_' . $sType.'_'.$this->iColumnId.'_'.$aOption["key"] . '" href="#gridblockcontentsettings-accordion_collapes_' . $sType.'_'.$this->iColumnId.'_'.$aOption["key"] . '" '.$sAriaExpanded.'>';
                if (isset($this->aSettings["options"][$sOption]["icon"]) && $this->aSettings["options"][$sOption]["icon"] != "") {
                    $sTippy = "";
                    if (isset($this->aSettings["options"][$sOption]["icon_tooltip"])) {
                        $sTippy = ' data-tippy-content="' . $this->aSettings["options"][$sOption]["icon_tooltip"] . '"';
                    }
                    $sHtml .= ' <i class="' . $this->aSettings["options"][$sOption]["icon"] . '" style="padding-right:10px" ' . $sTippy . '></i>';
                }
                $sHtml .= $sLabel . '</a></div><div id="gridblockcontentsettings-accordion_collapes_' . $sType.'_'.$this->iColumnId.'_'.$aOption["key"] . '" class="gridblockcontentsettings-body collapse ' . $sAccordionOpen . '"><div class="gbpanel-body">';

                $this->aSettings["options"][$sOption]["text"] = $sHtml;
                $this->aSettings["options"][$sOption]["label"] = "";

                $sCategory = "";
                if (isset($aOption["category"])) {
                    $sCategory = $aOption["category"];
                }

                $aGroupOptions = $aOption["options"];
                foreach ($aGroupOptions as $sGroupOption) {

                    $iArrKey = array_search($sGroupOption, $this->aSettings["showOptions"]);
                    if ($iArrKey != "") {
                        $this->aSettings["showOptions"][$iArrKey] = "gridblockcontentsettings_deleted_option";
                    }
                    
                    $aNewShowOptions[] = $sGroupOption;
                    $this->aSettings["options"][$sGroupOption]["category"] = $sCategory;
                }

                $aNewShowOptions[] = "gridblockcontentsettings_group_end_" . $sType.'_'.$this->iColumnId.'_'.$aOption["key"];

                $this->aSettings["options"]["gridblockcontentsettings_group_end_" . $sType.'_'.$this->iColumnId.'_'.$aOption["key"]] = array("type" => "html", "text" => "</div></div></div>", "category" => $sCategory);

                array_splice($this->aSettings["showOptions"], $iPos, 0, $aNewShowOptions);
                $iPos = $iPos + count($aGroupOptions) + 1;
            }
            $iPos++;
        }

        $aTmp = array();
        foreach($this->aSettings["showOptions"] AS $sOption) {
            if ($sOption != "gridblockcontentsettings_deleted_option") {
                $aTmp[] = $sOption;
            }
        }
        $this->aSettings["showOptions"] = $aTmp;




        // Kategorien aufbauen
        $this->aSettings["options_in_categories"] = false;
        if (isset($this->aSettings["showOptions"])) {
            foreach ($this->aSettings["showOptions"] as $sOption) {
                $aOption = $this->aSettings["options"][$sOption];

                if (!isset($aOption["use_last_category"])) {
                    $sCategory = "";
                }
                if (isset($aOption["category"])) {
                    $sCategory = $aOption["category"];
                }
                if ($sCategory != "") {
                    if (!isset($this->aSettings["categories"][$sCategory])) {
                        $this->aSettings["categories"][$sCategory] = array("label" => $sCategory);
                    }
                    $this->aSettings["categories"][$sCategory]["showOptions"][] = $sOption;
                    $this->aSettings["options_in_categories"] = true;
                }
            }
        }

        if ($this->aSettings["options_in_categories"] == true) {
            foreach ($this->aSettings["showOptions"] as $sOption) {
                $aOption = $this->aSettings["options"][$sOption];
                $sCategory = "";
                if (isset($aOption["category"])) {
                    $sCategory = $aOption["category"];
                }
                if ($sCategory == "" && !isset($aOption["use_last_category"])) {
                    $sCategory = "gridblockcontentsettingsgeneral";
                    if (!isset($this->aSettings["categories"][$sCategory])) {
                        $this->aSettings["categories"][$sCategory] = array("label" => "Sonstige Einstellungen");
                    }
                    $this->aSettings["categories"][$sCategory]["showOptions"][] = $sOption;
                }
            }
        }

        if ($this->aSettings["options_in_categories"] == false) {
            $this->aSettings["categories"] = array();
            $this->aSettings["categories"]["gridblockcontentsettingsgeneral"] = array("label" => "Keine Kategorien verwendet");
            if (isset($this->aSettings["showOptions"])) {
                foreach ($this->aSettings["showOptions"] as $sOption) {
                    $this->aSettings["categories"]["gridblockcontentsettingsgeneral"]["showOptions"][] = $sOption;
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

        $sForm = '<div class="gridblockcontentsettings-form gridblockcontentsettings-type-' . $sType . '">';

        if ($iColumnId) {
            $sType = "column_" . $iColumnId;
        } else {
            $sType = "template";
        }

        $aUsedTypes = array();
        $iTabRand = rand(0, 100) * rand(0, 100);
        if (isset($this->aSettings["categories"])) {

            $sForm .= '<ul class="nav nav-tabs tab-nav">';
            if ($this->aSettings["options_in_categories"] == true) {
                $iX = 0;
                foreach ($this->aSettings["categories"] as $aCategory) {
                    if (isset($aCategory["showOptions"])) {
                        $sForm .= '<li><a href="#gridblockcontentsettings-content-' . $iTabRand . '-' . $iX . '" data-toggle="tab">';
                        if (isset($aCategory["icon"]) && $aCategory["icon"] != "") {
                            $sTippy = "";
                            if (isset($aCategory["icon_tooltip"])) {
                                $sTippy = ' data-tippy-content="' . $aCategory["icon_tooltip"] . '"';
                            }
                            $sForm .= ' <i class="' . $aCategory["icon"] . '" style="padding-right:10px" ' . $sTippy . '></i>';
                        }
                        $sForm .= $aCategory["label"] . '</a></li>' . PHP_EOL;
                        $iX++;
                    }
                }
            }
            $sForm .= '<span class="gridblockcontentsettings-tab-icon-right"><i class="fa fa-cog"></i></span>';
            $sForm .= '</ul>';
            $sForm .= '<div class="tab-content" id="gridblockcontentsettings-tab-content-' . $iTabRand . '">';
            $iX = 0;

            foreach ($this->aSettings["categories"] as $aCategory) {
                if (isset($aCategory["showOptions"])) {
                    if ($this->aSettings["options_in_categories"] == true) {
                        $sForm .= '<div class="tab-pane fade" id="gridblockcontentsettings-content-' . $iTabRand . '-' . $iX . '">';
                    }
                    $sForm .= '<div class="rex-form-group active">';

                    foreach ($aCategory["showOptions"] as $sKey) {
                        $aOption = $this->aSettings["options"][$sKey];
                        if (isset($aOption)) {
                            if (count($aOption)) {

                                if (!in_array($aOption["type"], $aUsedTypes)) {
                                    array_push($aUsedTypes, $aOption["type"]);
                                }

                                if ($aOption["type"] != "html") {
                                    $sForm .= '<dl class="rex-form-group form-group gridblockcontentsettings">' . PHP_EOL;
                                }
                                if (isset($aOption["label"])) {
                                    if ($aOption["label"] != "") {
                                        $sTippy = "";
                                        if (isset($aOption["label_tooltip"])) {
                                            $sTippy = ' data-tippy-content="' . $aOption["label_tooltip"] . '"';
                                        }
                                        $sForm .= '<dt><label for=""><span ' . $sTippy . '>' . $aOption["label"] . '</span>';
                                        if (isset($aOption["icon"]) && $aOption["icon"] != "") {
                                            $sTippy = "";
                                            if (isset($aOption["icon_tooltip"])) {
                                                $sTippy = ' data-tippy-content="' . $aOption["icon_tooltip"] . '"';
                                            }
                                            $sForm .= ' <i class="' . $aOption["icon"] . '" style="padding-left:10px" ' . $sTippy . '></i>';
                                        }
                                        $sForm .= '</label></dt>' . PHP_EOL;
                                    }
                                }

                                switch ($aOption["type"]) {
                                    case "text":
                                    case "tel":
                                    case "url":
                                    case "number":
                                    case "color":
                                    case "numeric":
                                    case "email":
                                    case "date":
                                    case "datetime":
                                    case "datetime-local":
                                    case "month":
                                    case "week":

                                        $sFieldType = $aOption["type"];

                                        $sClass = 'class="form-control"';
                                        if (isset($aOption["class"])) {
                                            $sClass = 'class="' . $aOption['class'] . '"';
                                        }
                                        $sPlaceholder = '';
                                        if (isset($aOption["placeholder"])) {
                                            $sPlaceholder = 'placeholder="' . $aOption['placeholder'] . '"';
                                        }
                                        $sValue = "";
                                        if (isset($aSavedOptions[$sType][$sKey])) {
                                            $sValue = $aSavedOptions[$sType][$sKey];
                                        } else if (isset($aOption["default"]) && $aOption["default"] != "") {
                                            $sValue = $aOption["default"];
                                        }

                                        $sAttributes = "";
                                        if (isset($aOption["attributes"])) {
                                            $sAttributes = $aOption["attributes"];
                                        }

                                        $sForm .= '<dd><input name="REX_INPUT_VALUE[' . $this->iSettingsId . '][' . $sType . '][' . $sKey . ']" type="' . $sFieldType . '" ' . $sClass . ' ' . $sAttributes . ' value="' . $sValue . '" ' . $sPlaceholder . '></dd>' . PHP_EOL;
                                        break;

                                    case "textarea":
                                        $sClass = 'class="form-control"';
                                        if (isset($aOption["class"])) {
                                            $sClass = 'class="' . $aOption['class'] . '"';
                                        }
                                        $sPlaceholder = '';
                                        if (isset($aOption["placeholder"])) {
                                            $sPlaceholder = 'placeholder="' . $aOption['placeholder'] . '"';
                                        }
                                        $sValue = "";
                                        if (isset($aSavedOptions[$sType][$sKey])) {
                                            $sValue = $aSavedOptions[$sType][$sKey];
                                        } else if (isset($aOption["default"]) && $aOption["default"] != "") {
                                            $sValue = $aOption["default"];
                                        }

                                        $sAttributes = "";
                                        if (isset($aOption["attributes"])) {
                                            $sAttributes = $aOption["attributes"];
                                        }

                                        $sForm .= '<dd><textarea name="REX_INPUT_VALUE[' . $this->iSettingsId . '][' . $sType . '][' . $sKey . ']" ' . $sClass . ' ' . $sAttributes . ' ' . $sPlaceholder . '>' . $sValue . '</textarea></dd>' . PHP_EOL;
                                        break;

                                    case "colorpicker":
                                        $sClass = 'class="form-control"';
                                        if (isset($aOption["class"])) {
                                            $sClass = 'class="' . $aOption['class'] . '"';
                                        }
                                        $sPlaceholder = 'placeholder="Bsp. #003366"';
                                        if (isset($aOption["placeholder"])) {
                                            $sPlaceholder = 'placeholder="' . $aOption['placeholder'] . '"';
                                        }
                                        $sValue = "";
                                        if (isset($aSavedOptions[$sType][$sKey])) {
                                            $sValue = $aSavedOptions[$sType][$sKey];
                                        } else if (isset($aOption["default"]) && $aOption["default"] != "") {
                                            $sValue = $aOption["default"];
                                        }

                                        $sAttributes = "";
                                        if (isset($aOption["attributes"])) {
                                            $sAttributes = $aOption["attributes"];
                                        }

                                        $sForm .= '<dd><div class="input-group gridblock-colorinput-group"><input data-parsley-excluded="true" type="text" name="REX_INPUT_VALUE[' . $this->iSettingsId . '][' . $sType . '][' . $sKey . ']" ' . $sAttributes . ' value="' . $sValue . '" maxlength="7" ' . $sPlaceholder . ' pattern="^#([A-Fa-f0-9]{6})$" ' . $sClass . '><span class="input-group-addon gridblock-colorinput"><input type="color" value="' . @$aSavedOptions[$sType][$sKey] . '" pattern="^#([A-Fa-f0-9]{6})$" class="form-control"></span></div>' . PHP_EOL;
                                        break;

                                    case "select":
                                        $sClass = 'class="selectpicker w-100"';
                                        if (isset($aOption["class"])) {
                                            $sClass = 'class="' . $aOption['class'] . '"';
                                        }

                                        $sAttributes = "";
                                        if (isset($aOption["attributes"])) {
                                            $sAttributes = $aOption["attributes"];
                                        }

                                        $aSelectData = $this->getSelectData($sKey);
                                        $sForm .= '<dd><select name="REX_INPUT_VALUE[' . $this->iSettingsId . '][' . $sType . '][' . $sKey . ']" ' . $sClass . ' ' . $sAttributes . '>' . PHP_EOL;
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
                                        $sClass = 'class=""';
                                        if (isset($aOption["class"])) {
                                            $sClass = 'class="' . $aOption['class'] . '"';
                                        }
                                        $sAttributes = "";
                                        if (isset($aOption["attributes"])) {
                                            $sAttributes = $aOption["attributes"];
                                        }

                                        $sValue = "1";
                                        if (isset($aOption["value"])) {
                                            $sValue = $aOption["value"];
                                        }

                                        $sText = "";
                                        if (isset($aOption["text"])) {
                                            $sText = " " . $aOption["text"];
                                        }

                                        if (isset($aOption["checked"])) {
                                            if ($aOption["checked"] == "1" && !isset($aSavedOptions)) {
                                                $sChecked = 'checked="checked"';
                                            }
                                        }
                                        if (!isset($sChecked)) {
                                            $sChecked = ($sValue == @$aSavedOptions[$sType][$sKey]) ? 'checked="checked"' : '';
                                        }
                                        $sForm .= '<dd><div class="checkbox toggle"><label><input type="checkbox" name="REX_INPUT_VALUE[' . $this->iSettingsId . '][' . $sType . '][' . $sKey . ']" value="' . $sValue . '" ' . $sChecked . ' ' . $sClass . ' ' . $sAttributes . '>' . $sText . '</label></div></dd>' . PHP_EOL;
                                        unset($sChecked);
                                        break;

                                    case "radio":
                                        $sClass = 'class=""';
                                        if (isset($aOption["class"])) {
                                            $sClass = 'class="' . $aOption['class'] . '"';
                                        }
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
                                            $sForm .= '<input id="' . $iRand . '" name="REX_INPUT_VALUE[' . $this->iSettingsId . '][' . $sType . '][' . $sKey . ']" type="radio" value="' . $sSelectKey . '" ' . $sSelected . ' ' . $sClass . ' /> ' . $sSelectValue . PHP_EOL;
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
                                        if (isset($aOption["preview"])) {
                                            $aArgs["preview"] = "1";
                                        }
                                        if (isset($aOption["types"])) {
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

                                    case "slider":
                                        $sClass = 'class="form-control bootstap-slider"';
                                        if (isset($aOption["class"])) {
                                            $sClass = 'class="' . $aOption['class'] . '"';
                                        }
                                        if (!empty($aSavedOptions[$sType][$sKey])) {
                                            $sValue = @$aSavedOptions[$sType][$sKey];
                                        } else if (isset($aOption["default"])) {
                                            $sValue = $aOption["default"];
                                        }
                                        $sSliderMin = '';
                                        if (isset($aOption["slider-min"])) {
                                            $sSliderMin = 'data-slider-min="' . $aOption['slider-min'] . '"';
                                        }
                                        $sSliderMax = '';
                                        if (isset($aOption["slider-max"])) {
                                            $sSliderMax = 'data-slider-max="' . $aOption['slider-max'] . '"';
                                        }

                                        $sSliderTooltipSplit = 'data-slider-tooltip-split="true"';
                                        if (isset($aOption["slider-tooltip-split"])) {
                                            $sSliderTooltipSplit = 'data-slider-tooltip-split="' . $aOption["slider-tooltip-split"] . '"';
                                        }

                                        $sSliderRange = '';
                                        if (isset($aOption["slider-range"]) && ($aOption["slider-range"] == "1")) {
                                            $sSliderRange = 'data-slider-range="' . $aOption['slider-range'] . '"';
                                        } else {
                                            $sSliderTooltipSplit = '';
                                        }

                                        $sSliderStep = 'data-slider-step="1"';
                                        if (isset($aOption["slider-step"])) {
                                            $sSliderStep = 'data-slider-step="' . $aOption['slider-step'] . '"';
                                        }
                                        if (strpos($sValue, ',') !== false) {
                                            $sSliderValue = 'data-slider-value="[' . $sValue . ']"';
                                        } else {
                                            $sSliderValue = 'data-slider-value="' . $sValue . '"';
                                        }

                                        $sSliderShowTooltip = 'data-slider-tooltip="always"';
                                        if (isset($aOption["slider-tooltip"])) {
                                            if ($aOption["slider-tooltip"] == "hover") {
                                                $aOption["slider-tooltip"] = "show";
                                            }
                                            $sSliderShowTooltip = 'data-slider-tooltip="' . $aOption["slider-tooltip"] . '"';
                                        }



                                        $sForm .= '<dd><input name="REX_INPUT_VALUE[' . $this->iSettingsId . '][' . $sType . '][' . $sKey . ']" type="text" ' . $sClass . ' value="' . $sValue . '" ' . $sSliderTooltipSplit . ' ' . $sSliderMin . ' ' . $sSliderMax . ' ' . $sSliderRange . ' ' . $sSliderStep . ' ' . $sSliderValue . ' ' . $sSliderShowTooltip . '></dd>' . PHP_EOL;
                                        break;

                                    case "group":
                                        $sForm .= "<hr>Gruppe Anfang</hr>" . $aOption["label"];
                                        break;
                                }

                                if ($aOption["type"] != "html") {
                                    $sForm .= '</dl>' . PHP_EOL;
                                }
                            }
                        }
                    }

                    $sForm .= '</div>';
                    $sForm .= '</div>';
                    $iX++;
                }
            }
            $sForm .= '</div>';
            if ($this->aSettings["options_in_categories"] == true) {
                $sForm .= '</div>';
                $sForm .= '<script>$(function(){ $(\'a[href="#gridblockcontentsettings-content-' . $iTabRand . '-0"]\').tab("show"); });</script>';                    //immer ersten Tab einblenden
            }
            $sForm .= '</div>';

            if (!count($aUsedTypes)) {
                return;
            }
            return $sForm;
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

                    if (!isset($aArr["template"][$sKey]) or $aArr["template"][$sKey] == "gridblockcontentsettingsdefault") {
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
                        if (!isset($aArr["column_" . $iX][$sKey]) or $aArr["column_" . $iX][$sKey] == "gridblockcontentsettingsdefault") {
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
            if ($sContent && json_last_error() != "0") {
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
        $sHtml .= '<br /><a href="javascript:void(0)" class="btn btn-abort w-100 text-center gridblockcontentsettings-toggler gridblockcontentsettings-toggler-' . $iBlockId . '" data-id="#gridblockcontentsettings-' . $iBlockId . '" style="width:100%"><strong style="float:left">ContentSettings</strong> &nbsp; <i class="fa fa-cog" style="float:right;padding-top:3px"></i></a><br />' . PHP_EOL;
        $sHtml .= '<div class="gridblockcontentsettings-options" id="gridblockcontentsettings-' . $iBlockId . '">' . PHP_EOL;

        if (isset($oData->template)) {
            $sHtml .= '<p><strong>Template</strong></p>' . PHP_EOL;
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

                $sHtml .= '<p><strong>' . $sColumnLabel . '</strong></p>' . PHP_EOL;
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
			$(".gridblockcontentsettings-toggler-' . $iBlockId . '").click(function(){
				var iBlockId = $(this).attr("data-id");
				$(iBlockId).slideToggle();
			});
		})' . PHP_EOL;
        $sHtml .= '</script>' . PHP_EOL;


        #$sHtml .= "<pre>" . print_r($oData, 1) . "</pre>";


        return $sHtml;
    }
}
