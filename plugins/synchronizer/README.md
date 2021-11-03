# GridblockSynchronizer - Plugin für Gridblock
=========

Redaxo 5 Plugin für Addon Gridblock zum synchronisieren der Datenbank mit dem Dateisystem (analog Developer).

## Features

- Legt im Ordner data/addons/gridblock/plugins/synchronizer/ alle Templateinformationen (template.php & definition.json) ab
- Legt, wenn Plugin GridblockContentsettings aktiv ist, alle contentsettings.json-Dateien ebenfalls im o.g. Ordner ab
- Synchronisiert Änderungen aus template.php und definition.json mit der Datenbank von Gridblock
- Zusätzlich werden, wenn Addon Theme installiert ist, die Dateien mit dem Ordner theme/private/redaxo/gridblock synchronisiert
