# GridblockContentSettings - Plugin für Gridblock
=========

Redaxo 5 Plugin für Addon Gridblock zum Verwalten von (Design-) Einstellungen von Templates und Spalten.

## Features

- Verwaltung von wiederkehrenden Optionen (z.B. Abstände, Breite, Hintergrundfarbe)
- Generiert automatisch ein Formular mit den definierten Optionen
- Es können Optionen für ein ganzes Projekt festgelegt werden (data/addons/gridblock/plugins/contentsettings/contentsettings.json)
- Es können Optionen für einzelne Templates festgelegt werden (data/addons/gridblock/plugins/contentsettings/templates/template_$ID/contentsettings.json)
- Es können Optionen überschrieben werden (z.B. im Projekt Abstand nach unten mb-5 aber bei einem speziellen Template mb-3)


## Beispiel Output im Template (Einstellungen für Template)

```php
dump($contentsettings->template);
```
Nun kann auf alle Einstellungsvariablen des Templates über $contentsettings->template->marginBottom (oder einen anderen Key) zugegriffen werden.

## Beispiel Output im Template (Einstellungen für Spalte, im Beispiel Spalte 1)

```php
dump($contentsettings->column_1);
```
Nun kann auf alle Einstellungsvariablen der Spalte über $contentsettings->column_1->marginBottom (oder einen anderen Key) zugegriffen werden.

## Struktur projektweite Einstellungen data/addons/gridblock/plugins/contentsettings/contentsettings.json

showOptions - Array mit Schlüsseln, die standardmäßig geladen werden (Template & Spalten)
template
    - showOptions - Array mit Schlüsseln, die standardmäßig geladen werden (Template)
    - options - Möglichkeit die allgemeine Definition der Optionen für alle Templates zu überschreiben
columns
    - showOptions - Array mit Schlüsseln, die standardmäßig geladen werden (alle Spalten)
    - options - Möglichkeit die allgemeine Definition der Optionen für alle Spalten in allen Templates zu überschreiben    
options - Array mit Schlüsseln und Felddefinitionen

## Struktur Einstellungen eines bestimmten Templates data/addons/gridblock/plugins/contentsettings/template_$ID/contentsettings.json

showOptions - Array mit Schlüsseln, die standardmäßig geladen werden (Template & Spalten)
template
    - showOptions - Array mit Schlüsseln, die standardmäßig geladen werden (Template)
    - options - Möglichkeit die allgemeine Definition der Optionen für das Template zu überschreiben
columns
    - $SpaltenNr
        - showOptions - Array mit Schlüsseln, die standardmäßig in der Spalte $SpaltenNr geladen werden
        - options - Möglichkeit die allgemeine Definition der Optionen für diese Spalte zu überschreiben    
options - Array mit Schlüsseln und Felddefinitionen (überschreibt Optionen der projektweiten Einstellungen)


## Beispiel Überschreiben von projektweiten und spezifischen Template-Optionen

Abstandsdefinition im Projekt

```php
{
	"key": "marginBottom",
	"label": "Block Außenabstand Unten",
	"type": "select",
	"data": {
		"mb-0": "Kein",
		"mb-1": "Sehr klein",
		"mb-2": "Klein",
		"mb-3": "Mittel",
		"mb-4": "Groß",
		"mb-5": "Sehr groß"
	},
	"default": "mb-3"
}
```

Überschreiben des Standardabstands für ein einzelnes Template (ata/addons/gridblock/plugins/contentsettings/template_$ID/contentsettings.json)

```php
{
	"key": "marginBottom",
	"default": "mb-5"
}
```

