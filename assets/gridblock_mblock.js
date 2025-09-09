/**
 * Gridblock MBlock Integration
 * Funktionen für die Kompatibilität mit MBlock Addon
 */

// Global verfügbare MBlock-Funktionen für Gridblock
window.GridblockMBlock = {
    
    /**
     * Initialisiert MBlock-Wrapper nach dem Einfügen von Content
     * @param {jQuery} content - Der eingefügte Content
     */
    initializeMBlocks: function(content) {
        this.processMBlocksRecursive(content);
    },
    
    /**
     * Verarbeitet MBlocks rekursiv im gegebenen Content
     * @param {jQuery} content - Der zu verarbeitende Content
     */
    processMBlocksRecursive: function(content) {
        var mblockWrappers = content.find('.mblock-wrapper');
        
        if (mblockWrappers.length > 0) {
            var self = this;
            mblockWrappers.each(function() {
                var mblockWrapper = $(this);
                
                setTimeout(function() {
                    // Bestehende MBlock Items entfernen (werden durch Paste neu erstellt)
                    mblockWrapper.find('.mblock-item').remove();
                    
                    // MBlock neu initialisieren
                    self.reinitializeMBlock(mblockWrapper);
                    
                    // Prüfe ob noch weitere MBlocks existieren
                    setTimeout(function() {
                        self.processMBlocksRecursive(content);
                    }, 200);
                    
                }, 300);
            });
        }
    },
    
    /**
     * Initialisiert einen MBlock-Wrapper neu
     * @param {jQuery} mblockWrapper - Der MBlock-Wrapper
     */
    reinitializeMBlock: function(mblockWrapper) {
        // MBlock neu initialisieren
        if (typeof window.mblock_init === 'function') {
            window.mblock_init(mblockWrapper);
        }
        
        // MBlock Items neu indizieren
        if (typeof window.mblock_reindex === 'function') {
            window.mblock_reindex(mblockWrapper);
        }
        
        // Events neu binden
        if (typeof window.mblock_add === 'function') {
            window.mblock_add(mblockWrapper);
        }
        if (typeof window.mblock_sortable === 'function') {
            window.mblock_sortable(mblockWrapper);
        }
        if (typeof window.mblock_remove === 'function') {
            window.mblock_remove(mblockWrapper);
        }
        if (typeof window.mblock_init_toolbar === 'function') {
            window.mblock_init_toolbar(mblockWrapper);
        }
        
        // Custom Event für MBlock triggern
        mblockWrapper.trigger('mblock:refresh');
        mblockWrapper.trigger('mblock:init');
    },
    
    /**
     * Sammelt Formulardaten aus einem Container für MBlock-Unterstützung
     * @param {jQuery} container - Container mit Formularelementen
     * @param {string} sourceUid - Quell-UID für Mapping
     * @returns {Object} Gesammelte Formulardaten
     */
    collectFormData: function(container, sourceUid) {
        var formData = {};
        
        // Alle Formularelemente sammeln
        container.find('input, textarea, select').each(function() {
            var element = $(this);
            var name = element.attr('name');
            var id = element.attr('id');
            var type = element.attr('type');
            var value = '';
            
            if (name && name.trim() !== '') {
                // Wert je nach Elementtyp ermitteln
                if (type === 'checkbox' || type === 'radio') {
                    if (element.is(':checked')) {
                        value = element.val();
                    }
                } else if (element.is('select')) {
                    value = element.val();
                } else {
                    value = element.val();
                }
                
                formData[name] = {
                    value: value,
                    type: type,
                    id: id,
                    checked: element.is(':checked'),
                    selected: element.is(':selected')
                };
            }
        });
        
        return formData;
    },
    
    /**
     * Stellt Formulardaten in einem Container wieder her
     * @param {jQuery} container - Ziel-Container
     * @param {Object} formData - Wiederherzustellende Formulardaten
     * @param {string} sourceUid - Quell-UID
     * @param {string} targetUid - Ziel-UID
     */
    restoreFormData: function(container, formData, sourceUid, targetUid) {
        var self = this;
        
        Object.keys(formData).forEach(function(originalName) {
            var data = formData[originalName];
            
            // UID in Feldnamen ersetzen
            var newName = self.replaceUidInString(originalName, sourceUid, targetUid);
            var element = container.find('[name="' + newName + '"]');
            
            if (element.length > 0) {
                var type = data.type;
                
                if (type === 'checkbox' || type === 'radio') {
                    element.prop('checked', data.checked);
                } else if (element.is('select')) {
                    element.val(data.value);
                } else {
                    element.val(data.value);
                }
                
                // Event triggern für eventuelle Listener
                element.trigger('change');
            }
        });
    },
    
    /**
     * Ersetzt UID-Patterns in einem String
     * @param {string} str - Zu bearbeitender String
     * @param {string} sourceUid - Quell-UID
     * @param {string} targetUid - Ziel-UID
     * @returns {string} Bearbeiteter String
     */
    replaceUidInString: function(str, sourceUid, targetUid) {
        if (!str || !sourceUid || !targetUid) return str;
        
        // Verschiedene UID-Patterns ersetzen
        var patterns = [
            new RegExp('\\b' + sourceUid + '\\b', 'g'),
            new RegExp('GBS[0-9a-f]{32}', 'g'),
            new RegExp('gbs[0-9a-f]{32}', 'g'),
            new RegExp(sourceUid.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'g')
        ];
        
        var result = str;
        patterns.forEach(function(pattern) {
            result = result.replace(pattern, targetUid);
        });
        
        return result;
    },

    /**
     * Verarbeitet MBlock-Daten beim Kopieren/Einfügen
     * @param {jQuery} dst - Ziel-Container
     * @param {Object} parsedData - Geparste Formulardaten
     * @param {string} sourceUID - Quell-UID
     * @param {string} uID - Ziel-UID
     */
    processMBlockData: function(dst, parsedData, sourceUID, uID) {
        var self = this;
        var mblockWrappers = dst.find('.mblock_wrapper');
        
        mblockWrappers.each(function() {
            var mblockWrapper = $(this);
            
            // Analysiere die kopierten Daten um zu sehen wie viele MBlock-Items benötigt werden
            var maxMblockIndex = -1;
            $.each(parsedData, function(fieldName, fieldValue) {
                var mblockMatch = fieldName.match(/\[(\d+)_MBLOCK\]\[(\d+)\]/);
                if (mblockMatch) {
                    var itemIndex = parseInt(mblockMatch[2]);
                    maxMblockIndex = Math.max(maxMblockIndex, itemIndex);
                }
            });
            
            // Falls MBlock-Items gefunden wurden, erstelle die notwendigen Items
            if (maxMblockIndex >= 0) {
                var currentItems = mblockWrapper.find('.sortitem').length;
                var neededItems = maxMblockIndex + 1;
                
                // Füge fehlende MBlock-Items hinzu
                for (var i = currentItems; i < neededItems; i++) {
                    var addButton = mblockWrapper.find('.addme').first();
                    if (addButton.length > 0) {
                        addButton.trigger('click');
                    }
                }
                
                // Warte bis alle Items erstellt sind, dann setze die Werte
                setTimeout(function() {
                    
                    // Nochmal alle Felder durchgehen und Werte setzen
                    $.each(parsedData, function(fieldName, fieldValue) {
                        var newFieldName = fieldName;
                        
                        if (sourceUID && sourceUID.length > 0) {
                            var pattern1 = new RegExp('\\[' + sourceUID.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '\\]', 'g');
                            newFieldName = newFieldName.replace(pattern1, '[' + uID + ']');
                            var pattern2 = new RegExp("\\['" + sourceUID.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + "'\\]", 'g');
                            newFieldName = newFieldName.replace(pattern2, "['" + uID + "']");
                        } else {
                            newFieldName = fieldName.replace(/\[GBS[a-f0-9]{40}\]/g, '[' + uID + ']');
                            newFieldName = newFieldName.replace(/\['GBS[a-f0-9]{40}'\]/g, "['" + uID + "']");
                        }
                        
                        var targetField = dst.find('[name="' + newFieldName + '"]');
                        if (targetField.length > 0) {
                            if (targetField.is(':checkbox') || targetField.is(':radio')) {
                                targetField.prop('checked', targetField.val() == fieldValue);
                            } else {
                                targetField.val(fieldValue);
                            }
                            targetField.trigger('change');
                        }
                    });
                    
                }, (neededItems - currentItems) * 150); // Warte basierend auf Anzahl neuer Items
            }
            
            // MBlock neu initialisieren
            self.reinitializeMBlock(mblockWrapper);
        });
    }
};
