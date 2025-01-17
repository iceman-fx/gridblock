<?php
/*
	Redaxo-Addon Gridblock
	Verwaltung: Hilfe
	v1.1.12
	by Falko Müller @ 2021-2023 (based on 0.1.0-dev von bloep)
*/

/** RexStan: Vars vom Check ausschließen */
/** @var rex_addon $this */
/** @var array $config */
/** @var string $func */
/** @var string $page */
/** @var string $subpage */


//Vorgaben
?>

<style>
.faq { margin: 0px !important; cursor: pointer; }
.faq + div { margin: 0px 0px 15px; }
</style>

<section class="rex-page-section">
<div class="panel panel-default">

<header class="panel-heading"><div class="panel-title"><?php echo $this->i18n('a1620_head_help'); ?></div></header>

<div class="panel-body">
    <div class="rex-docs">
        <div class="rex-docs-sidebar">
            <nav class="rex-nav-toc">
                <ul>
                    <li><a href="#start">Allgemein</a>
                    <li><a href="#templates">Layoutvorlagen</a>
                    <li><a href="#config">Einstellungen</a>
                    <li><a href="#modul">Gridblock-Modul</a>
                    <li><a href="#placeholders">Platzhalter in Modulen</a>
                    <li><a href="#plugin_cs">Plugin: ContentSettings</a>
                    <li><a href="#plugin_sy">Plugin: Synchronizer</a>
                    <li><a href="#faq">FAQ</a>
                </ul>
            </nav>
        </div>

                
<div class="rex-docs-content">
<h1>Addon: <?php echo $this->i18n('a1620_title'); ?></h1>


<!-- Alkgemein -->
<a name="start"></a>

<p>Mit dieser Erweiterung können mehrere Inhaltsmodule (Blöcke) gruppiert und in einem Spaltenraster ausgegeben werden.</p>
<p>Nach Auswahl einer Spaltenvorlage kann der Redakteur anschließend eine <strong>beliebige Anzahl vorhandener Inhaltsmodule</strong> je Spalte gruppieren.<br>
  Die Inhaltsmodule entsprechen dabei den üblichen Modulblöcken, welche auch direkt in einer Artikelseite angelegt werden können.</p>
<p>Mit weiteren Einstellungen kann der Umfang der Pflegemöglichkeiten erweitert oder beschränkt werden (<a href="#config">siehe Einstellungen</a>).</p>



<p>&nbsp;</p>

<!-- Templates -->
<a name="templates"></a>
<h2>Definition von Layoutvorlagen (Templates)</h2>                    
                    
<p>Über die  Definition von Layoutvorlagen geben Sie dem Redakteur die Möglichkeit, verschiedene Darstellungen und Spaltenraster auswählen zu können.<br>
  Des Weiteren können  damit verschiedene Definitionen für unterschiedliche CSS-Gridysteme definiert werden (z.B. UIKit, Bootstrap, CSS-Grid).
</p>
<p>Um dem Redakteur eine Auswahl bieten zu können, legen Sie die gewünschte Anzahl an Vorlagen an oder installieren bei der ersten Verwendung einige Beispielvorlagen über die entsprechende Schaltfläche. Auch können eigene Templates über die Schaltfläche &quot;Eigene Templates importieren&quot; jederzeit importiert werden.</p>
<p>Nach dem Anlegen Ihrer Vorlagen können diese vom Redakteur im Gridmodul ausgewählt und genutzt werden.</p>
<p>Es empfiehlt sich, beim Anlegen einer Vorlage auch eine Layoutvorschau zu definieren, um dem Redakteur einen kleinen Einblick in die spätere Spaltendarstellung zu ermöglichen.</p>
<p>&nbsp;</p>

<p><strong>Mögliche Optionen einer Layoutvorlage (Template)</strong></p>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <th width="200" scope="col">Option</th>
    <th scope="col">Erklärung &amp; Attribute</th>
</tr>
  <tr>
    <td valign="top"><strong>Titel / Bezeichnung<br>
</strong>(Pflichtangabe)</td>
    <td valign="top">
    Der Titel legt den Namen des Templates fest.<br>
    Sofern keine Layoutvorschau definiert wird, wird dieser Titel als Layoutvorschau genutzt.</td>
  </tr>
  <tr>
    <td valign="top"><strong>Kurzbeschreibung<br>
</strong></td>
    <td valign="top">Über die Kurzbeschreibung können Sie eine kurze Erklärung des Templates hinterlegen, welche als Hover-Text bei der Layoutvorschau angezeigt wird.</td>
  </tr>
  <tr>
    <td valign="top"><strong>Priorität</strong></td>
    <td valign="top">Über die Priorität kann die Sortierung der Templates vorgenommen werden, um z.B. wichtige Templates innerhalb der Auswahl ganz oben platzieren zu können. &nbsp;</td>
  </tr>
  <tr>
    <td valign="top"><strong>Anzahl Spalten<br>
    </strong>(Pflichtangabe) </td>
    <td valign="top">Definieren Sie hier die Anzahl der zu verwendeten Spalten des Templates.<br>
      Gängige Spaltenanzahlen sind 1, 2, 3 oder 4 Spalten.<br>
      <br>
    Die max. Anzahl ist dabei auf 12 begrenzt.</td>
  </tr>
  <tr>
    <td valign="top"><strong>Template-Ausgabecode<br>
    </strong>(Pflichtangabe) </td>
    <td valign="top">Über den Template-Ausgabecode wird die Darstellung der Ausgabe (Frontend) gesteuert, wobei spezielle Platzhalter zum Einsatz kommen.<br>
      Dabei kann normales HTML genutzt  und bei Bedarf um JavaScript- und PHP-Code
      erweitert werden.<br>
      <br>
      <strong>Beispiel: </strong><br>
<pre>&lt;div&gt;<br>	&lt;div&gt;REX_GRID[1]&lt;/div&gt;<br>	&lt;div&gt;REX_GRID[2]&lt;/div&gt;<br>	&lt;div&gt;REX_GRID[3]&lt;/div&gt;<br>&lt;/div&gt;</pre>

<p>Mögliche Platzhalter für Spaltenausgabe:<br>
  <strong>REX_GRID[1...12] </strong> = Ausgabe der Inhaltsmodule der jeweiligen Spalte (max. 12 Spalten)<br>
  <br>
  Weitere verfügbare Platzhalter:<br>
  <strong>REX_GRID_TEMPLATE_ID </strong> = ID des gewählten Templates<br>
  <strong>REX_GRID_TEMPLATE_PREVIEW </strong> = Definition Layoutvorschau (JSON) des gewählten Templates (Sonderzeichen werden <u>nicht</u> escaped)<br>
  <strong>REX_GRID_TEMPLATE_COLUMNS </strong> = Anzahl der Spalten des gewählten Templates</p>
<p>&nbsp;</p>
<p>Alle Contentsettings und Templateeinstellungen stehen zusätzlich als Array über <code>rex_gridblock::getSettings()</code> zur Verfügung.
</p></td>
  </tr>
  <tr>
    <td valign="top"><strong>Definition Layoutvorschau (JSON)</strong></td>
    <td valign="top">Über eine Layoutvorschau kann die  Spaltenraster einfach visualisiert werden.<br>
      Die Definition muss dabei im JSON-Format erfolgen. <br>
      <br>
      <strong>Beispiel: </strong><br>
<pre>{<br>	&quot;totalcolumns&quot;: &quot;3&quot;,<br>	&quot;columns&quot;: [<br>		{<br>			&quot;width&quot;: &quot;50&quot;,<br>			&quot;title&quot;: &quot;50%&quot;<br>		},<br>		{<br>			&quot;width&quot;: &quot;25&quot;,<br>			&quot;title&quot;: &quot;25%&quot;<br>		},<br>		{<br>			&quot;width&quot;: &quot;25&quot;,<br>			&quot;title&quot;: &quot;25%&quot;<br>		}<br>	]<br>}</pre>

Die Angabe  &quot;totalcolumns&quot; definiert die Anzahl der Spalten innerhalb der Vorschau.<br>
Alle &quot;width&quot;-Angaben werden  immer als prozentuale Breite (Definition ohne Prozentzeichen) der jeweiligen Spalte genutzt.<br>
&quot;Title&quot;-Angaben dienen nur der Beschriftung der jeweiligen Spaltenvorschau und können auf Wunsch auch als Spaltennamen innerhalb der Inhaltsverwaltung verwendet werden.</td>
  </tr>
</table>



<p>&nbsp;</p>

<!-- Einstellungen -->
<a name="config"></a>
<h2>Einstellungen</h2>                    
                    
<p>Durch Veränderung der Basis-Einstellungen kann der Umfang der Pflegemöglichkeiten erweitert oder weiter eingeschränkt werden.</p>
<p>Die wichtigste Einstellung ist dabei die Auswahl der Inhaltsmodule innerhalb der Spalten sowie die Art der Bereitstellung dieser Module (zulässige Module oder zu ignorierende Module).</p>
<p>Über das  ContentSettings-Plugin können weitere zusätzliche und öfters genutzte Template- und Spalteneinstellungen definiert werden, welche anschließend mittels PHP abgerufen und verarbeitet werden können.</p>
<p>&nbsp;</p>

<p><strong>Übersicht Einstellungen</strong></p>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <th width="200" scope="col">Option</th>
    <th scope="col">Erklärung &amp; Attribute</th>
</tr>
  <tr>
    <td valign="top"><strong>Modus für Inhaltsmodule</strong></td>
    <td valign="top">
    Definition der Art der Verwendung von ausgewählten Inhaltsmodulen.<br>
    <br>
    <strong>Zulässige Inhaltsmodule </strong> = nur die ausgewählten Module sind nutzbar<br>
    <strong>Zu ignorierende Inhaltsmodule </strong> = die  ausgewählten Module sind nicht nutzbar<br>
    <br>
    Hinweis: über das Gridmodul können zusätzlich sowohl zulässige als auch zu ignorierende Inhaltsmodule definiert werden.</td>
  </tr>
  <tr>
    <td valign="top"><strong>Auswahl Inhaltsmodule<br>
</strong></td>
    <td valign="top">Wählen Sie hier die gewünschten Inhaltsmodule aus, welche im jeweiligen Modus berücksichtigt werden sollen.</td>
  </tr>
  <tr>
    <td valign="top"><strong>Spaltenbezeichnungen</strong></td>
    <td valign="top">Bei aktiver Option werden die in der Layoutvorschau definierten Spalten-Titel auch für Auswahlen der Spalten im Pflegebereich genutzt.</td>
  </tr>
  </table>



<p>&nbsp;</p>

<!-- Modul -->
<a name="modul"></a>
<h2>Gridblock-Modul für Redakteure</h2>                    
                    
<p>Das Gridblock-Modul wurde i.d.R. bei der Installation des Addons bereits mit installiert, so dass dieses sofort für das Anlegen eines Gridblockes genutzt werden kann.<br>
  Sollte dies nicht der Fall sein oder es sollen weitere Optionen definiert werden, so kann ein eigenes Modul angelegt werden.
</p>
<p>Wichtig: Der Kommentar <strong>/* GRID_MODULE_IDENTIFIER | DONT REMOVE */</strong> sollte nicht entfernt werden!</p>
<p>&nbsp;</p>


<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <th width="200" scope="col">Modultyp</th>
    <th scope="col">Beispiel</th>
</tr>
  <tr>
    <td valign="top"><strong>Moduleingabe (INPUT)</strong></td>
    <td valign="top"><pre>&lt;?php<br>/* GRID_MODULE_IDENTIFIER | DONT REMOVE */                      
    
$grid = new rex_gridblock();<br>$grid-&gt;getSliceValues(&quot;REX_SLICE_ID&quot;);<br><br>//$grid-&gt;allowModule( array(1,2,3) );			//Optional: IDs der auswählbaren Inhaltsmodule als INT oder ARRAY =&gt; 1 | array(1,2,3)<br>//$grid-&gt;ignoreModule( array(2) );				//Optional: IDs der nicht auswählbaren Inhaltsmodule als INT oder ARRAY =&gt; 1 | array(1,2,3)<br><br>echo $grid-&gt;getModuleInput();<br>?&gt;</pre>

</td>
  </tr>
  <tr>
    <td valign="top"><strong>Modulausgabe (OUTPUT)</strong></td>
    <td valign="top"><pre>&lt;?php<br>/* GRID_MODULE_IDENTIFIER | DONT REMOVE */                      
    
$grid = new rex_gridblock();<br>$grid-&gt;getSliceValues(&quot;REX_SLICE_ID&quot;);<br><br>echo $grid-&gt;getModuleOutput(); <br>?&gt;
</pre></td>
  </tr>
</table>



<p>&nbsp;</p>

<!-- Platzhalter in Inhaltsmodulen -->
<a name="placeholders"></a>
<h2>Verfügbare Platzhalter &amp; Abfragen in Inhaltsmodulen:</h2>
                    
<p>Innerhalb der Inhaltsmodule (Blöcke) stehen  Redaxo-Platzhalter sowie zusätzliche Platzhalter des Gridblock-Addons zur Verfügung.
</p>
<p>
<strong>REX_GRID_TEMPLATE_ID </strong> = ID des gewählten Templates<br>
<strong>REX_GRID_TEMPLATE_PREVIEW </strong> = Definition Layoutvorschau (JSON) des gewählten Templates (Sonderzeichen werden <u>nicht</u> escaped)<br>
<strong>REX_GRID_TEMPLATE_COLUMNS </strong> = Anzahl der Spalten des gewählten Templates

<br>
<strong>REX_GRID_COLUMN_NUMBER </strong> = Nummer der Grid-Spalte (1 ... 12)</p>
<p><strong>REX_SLICE_ID</strong> = Slice-ID des gesamten Gridblockes (Redaxo)<br>
  <strong>REX_MODULE_ID</strong> = Modul-ID des jeweiligen Inhaltsmoduls innerhalb eines Gridblockes<br>
  <strong>REX_MODULE_KEY</strong> = Modul-KEY des jeweiligen Inhaltsmoduls innerhalb eines Gridblockes<br>
  <strong>REX_CLANG_ID</strong> = Clang-ID des Artikels (Redaxo)<br>
  <strong>REX_ARTICLE_ID</strong> =  ID des Artikels (Redaxo)<br>
  <strong>REX_TEMPLATE_ID</strong> =  Template-ID des Artikels (Redaxo)</p>
<p>&nbsp;</p>
<p>Alle Contentsettings und Templateeinstellungen stehen in Inhaltsmodulen  zusätzlich als Array über <code>rex_gridblock::getSettings()</code> zur Verfügung.</p>
<p>Des Weiteren kann im Inhaltsmodul über <code>rex_gridblock::isBackend()</code> abgefragt werden, ob man sich im Editiermodus des Gridblockes (Backend) befindet.</p>



<p>&nbsp;</p>

<!-- Plugin Contentsettings -->
<a name="plugin_cs"></a>
<h2>Plugin: ContentSettings</h2>                    
                    
<p>Mit dem Plugin &quot;ContentSettings&quot; können beliebige Einstellungen für Templates und deren Spalten verwaltet werden.
</p>
<p>Features:</p>
<ul>
  <li>Verwaltung von wiederkehrenden Einstellungen (z.B. Abstände, Breite &amp; Hintergrundfarbe) und  Gruppierung dieser Einstellungen in Kategorien und Gruppen</li>
  <li>Generiert automatisch ein Formular mit den definierten Einstellungen</li>
  <li>Es können Einstellungen für ein ganzes Projekt festgelegt werden (data/addons/gridblock/plugins/contentsettings/contentsettings.json)</li>
  <li>Es können Einstellungen für einzelne Templates festgelegt werden (data/addons/gridblock/plugins/contentsettings/templates/template_$ID/contentsettings.json)</li>
  <li>Es können Optionen überschrieben werden (z.B. im Projekt Abstand nach unten mb-5 aber bei einem speziellen Template mb-3)</li>
</ul>
<p>&nbsp;</p>
<p>Zur Definition eigener Einstellungsfelder passt man die jeweilige contentsettings.json den eigenen Wünschen an.<br>
  Die global für alle Templates verfügbare Datei liegt dabei unter <strong>/redaxo/data/addons/gridblock/plugins/contentsettings/contentsettings.json</strong>.</p>

<p><strong>Beispieldefinition</strong></p>
<pre style="height: 400px;">
<?php
echo htmlspecialchars(rex_file::get(rex_addon::get('gridblock')->getPath('data/contentsettings.json')));
?>
</pre>


<p>&nbsp;</p>


<p><strong>Zugriff auf die Einstellungen innerhalb der Layoutvorlagen (Templates)</strong></p>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <th width="200" scope="col">Typ</th>
    <th scope="col">Beispiel &amp; Erklärung</th>
</tr>
  <tr>
    <td valign="top"><strong>Einstellungen für alle Templates</strong></td>
    <td valign="top"><pre>&lt;?php<br>dump($contentsettings-&gt;template);<br><br>echo $contentsettings-&gt;template-&gt;<strong>key</strong>;<br>?&gt;</pre>
    
      <p>Nun kann auf alle Einstellungsvariablen des Templates über $contentsettings-&gt;template-&gt;marginBottom (oder einen anderen Key) zugegriffen werden. </p>
      <p>Alle Contentsettings und Templateeinstellungen stehen   zusätzlich als Array über <code>rex_gridblock::getSettings()</code> zur Verfügung.</p></td>
  </tr>
  <tr>
    <td valign="top"><strong>Einstellungen für Spalten, im Beispiel Spalte 1</strong></td>
    <td valign="top"><pre>&lt;?php<br>dump($contentsettings-&gt;column_1);<br><br>echo $contentsettings-&gt;column_1-&gt;<strong>key</strong>;<br>?&gt;</pre>
    
      <p>Nun kann auf alle Einstellungsvariablen der Spalte über $contentsettings-&gt;column_1-&gt;marginBottom (oder einen anderen Key) zugegriffen werden.</p>
      <p>Alle Contentsettings und Templateeinstellungen stehen   zusätzlich als Array über <code>rex_gridblock::getSettings()</code> zur Verfügung.</p></td>
  </tr>
</table>
<p>&nbsp;</p>


<p><strong>Struktur projektweiter Einstellungen:</strong> /redaxo/data/addons/gridblock/plugins/contentsettings/contentsettings.json</p>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <th width="200" scope="col">Option</th>
    <th scope="col">Erklärung &amp; Attribute</th>
</tr>
  <tr>
    <td valign="top"><strong>showOptions</strong></td>
    <td valign="top">Array mit Schlüsseln, die standardmäßig geladen werden (Template &amp; Spalten)</td>
  </tr>
  <tr>
    <td valign="top"><strong>template</strong></td>
    <td valign="top">Block zur Definition der im Templatebereich zu nutzenden Settings</td>
  </tr>
  <tr>
    <td valign="top"><strong>template &gt; showOptions<br>
    </strong></td>
    <td valign="top">Array mit Schlüsseln, die standardmäßig in den Template-Settings geladen werden</td>
  </tr>
  <tr>
    <td valign="top"><strong>template &gt; options</strong></td>
    <td valign="top">Möglichkeit die allgemeine Definition der Optionen für das Template zu überschreiben</td>
  </tr>
  <tr>
    <td valign="top"><strong>columns</strong></td>
    <td valign="top">Block zur Definition der in den Spalten zu nutzenden Settings</td>
  </tr>
  <tr>
    <td valign="top"><strong>columns</strong> &gt; <strong>showOptions</strong></td>
    <td valign="top">Array mit Schlüsseln, die standardmäßig in den Spalten-Settings geladen werden</td>
  </tr>
  <tr>
    <td valign="top"><strong>columns &gt; options</strong></td>
    <td valign="top">Möglichkeit die allgemeine Definition der Optionen für die Spalten zu überschreiben</td>
  </tr>
  <tr>
    <td valign="top"><strong>categories</strong></td>
    <td valign="top">Block zur Definition von zusätzlichen Kategorien zur Gruppierung von Settings in einer Tab-Struktur</td>
  </tr>
  <tr>
    <td valign="top"><strong>options</strong></td>
    <td valign="top">Block zur Definition der eigentlichen  Felddefinitionen (Settings) inkl. Zuweisung zu den categories und möglichen Gruppen (Akkordeon-Stil)</td>
  </tr>
  </table>

<p>&nbsp;</p>
<p><strong>Struktur Einstellungen eines bestimmten Templates:</strong> /redaxo/data/addons/gridblock/plugins/contentsettings/template_$ID/contentsettings.json</p>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <th width="200" scope="col">Option</th>
    <th scope="col">Erklärung &amp; Attribute</th>
</tr>
  <tr>
    <td valign="top"><strong>showOptions</strong></td>
    <td valign="top">Array mit Schlüsseln, die standardmäßig geladen werden (Template &amp; Spalten)</td>
  </tr>
  <tr>
    <td valign="top"><strong>template &gt; showOptions<br>
      </strong></td>
    <td valign="top">Array mit Schlüsseln, die standardmäßig in den Template-Settings geladen werden</td>
  </tr>
  <tr>
    <td valign="top"><strong>template &gt; options</strong></td>
    <td valign="top">Möglichkeit die allgemeine Definition der Optionen für das Template zu überschreiben</td>
  </tr>
  <tr>
    <td valign="top"><strong>columns &gt; $SpaltenNr &gt; showOptions</strong></td>
    <td valign="top">Array mit Schlüsseln, die standardmäßig in der Spalte $SpaltenNr geladen werden</td>
  </tr>
  <tr>
    <td valign="top"><strong>columns &gt; $SpaltenNr &gt; options</strong></td>
    <td valign="top">Möglichkeit die allgemeine Definition der Optionen für diese Spalte zu überschreiben</td>
  </tr>
  <tr>
    <td valign="top"><strong>options</strong></td>
    <td valign="top">Array mit Schlüsseln und Felddefinitionen (überschreibt Optionen der projektweiten Einstellungen)</td>
  </tr>
</table>
<p>&nbsp;</p>


<p><strong>Beispiel Überschreiben von projektweiten und spezifischen Template-Optionen</strong></p>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <th width="200" scope="col">Feld</th>
    <th scope="col">Beispiel &amp; Erklärung</th>
</tr>
  <tr>
    <td valign="top"><strong>Abstandsdefinition im Projekt</strong></td>
    <td valign="top"><pre>{<br>	"key": "marginBottom",<br>	"label": "Block Außenabstand Unten",<br>	"type": "select",<br>	"data": {<br>		"mb-0": "Kein",<br>		&quot;mb-1": "Sehr klein",<br>		"mb-2": {
			&quot;label&quot;: &quot;Klein&quot;,
			&quot;data-subtext&quot;: &quot;Zusatzinformation&quot;,
			&quot;data-icon&quot;: &quot;fa fa-phone&quot;,
			&quot;data-content&quot;: &quot;&lt;img src='/assets/mein-icon.png'&gt;&quot;<br>		},<br>		"mb-3": "Mittel",<br>		"mb-4": "Groß",<br>		"mb-5": "Sehr groß"<br>	},<br>	&quot;default": "mb-3"<br>}</pre></td>
  </tr>
  <tr>
    <td valign="top"><strong>Überschreiben des Standardabstands für ein einzelnes Template</strong></td>
    <td valign="top"><pre>{<br>	"key": "marginBottom",<br>	&quot;default": "mb-5"<br>}</pre>
        
     Datei: /redaxo/data/addons/gridblock/plugins/contentsettings/template_$ID/contentsettings.json</td>
  </tr>
</table>
<p>&nbsp;</p>


<p><strong>Trennung von globalen und Projekt-ContentSettings </strong></p>
<p>Standardmäßig wird die contentsettings.json als Projektdefinition genutzt.
<p>	Zusätzlich kann eine <strong>contentsettings.global.json</strong> mit globalen Definitionen im gleichen Ordner wie die contentsettings.json angelegt werden, welche 
	vorrangig eingelesen (vor der contentsettings.json)
	wird.<br>
	Globale Definitionen in der contentsettings.global.json können damit zur Laufzeit überschrieben werden.
<p>&nbsp;</p>



<!-- Plugin Contentsettings -->
<a name="plugin_sy"></a>
<h2>Plugin: Synchronizer</h2>                    
                    
<p>Mit dem Plugin &quot;Synchronizer&quot; werden alle gespeicherten Tempaltes in der Datenbank mit dem Dateisystem synchronisiert (analog Developer).</p>

<p>Features</p>
<ul>
  <li>Legt im Ordner /redaxo/data/addons/gridblock/plugins/synchronizer/ alle Templateinformationen (template.php & definition.json) ab  </li>
  <li>Legt, wenn Plugin ContentSettings aktiv ist, alle contentsettings.json-Dateien ebenfalls im o.g. Ordner ab  </li>
  <li>Synchronisiert Änderungen aus template.php und definition.json mit der Datenbank von Gridblock  </li>
  <li>Zusätzlich werden, wenn Addon Theme installiert ist, die Dateien mit dem Ordner theme/private/redaxo/gridblock synchronisiert</li>
</ul>
<p>&nbsp;</p>

<a name="faq"></a>
<h2>FAQ:</h2>

<p class="faq text-danger" data-toggle="collapse" data-target="#f001"><span class="caret"></span> Bei MBlock-Modulen funktioniert die Medienauswahl/Linkauswahl nicht korrekt.</p>
<div id="f001" class="collapse">MBlock muss mind. in der Version 3.4.0 vorliegen, da es in älteren Versionen einen Fehler in der Werteübergabe zum Medienpool/zur Linkmap gab.</div>

<p class="faq text-danger" data-toggle="collapse" data-target="#f002"><span class="caret"></span> Beim Einfügen eines zuvor kopierten Inhaltsblockes fehlen aktuelle Änderungen der Eingabefelder.</p>
<div id="f002" class="collapse">Beim Einfügen eines zuvor kopierten Inhaltsblockes wird immer nur der zuletzt gespeicherte Zustand eingefügt. Speichern Sie vor dem Kopieren einfach den gesamten Block, um beim Einfügen die korrekten Inhalte zu erhalten.</div>

<p class="faq text-danger" data-toggle="collapse" data-target="#f003"><span class="caret"></span> Die Kopieren-Schaltfläche fehlt bei einem neuen Inhaltsblock.</p>
<div id="f003" class="collapse">Die Kopieren-Schaltfläche des jeweiligen Inhaltsblockes wird erst angezeigt, wenn dieser gespeichert wurde.</div>


<div id="markdownOut">
	<h3>Weitergehende Möglichkeiten</h3><p>Durch&nbsp;die&nbsp;Verwendung&nbsp;von&nbsp;Templates&nbsp;und&nbsp;einer&nbsp;Helper-Funktion&nbsp;kannst&nbsp;du&nbsp;die&nbsp;Ausgabe&nbsp;deiner&nbsp;Gridblock-Templates einfach&nbsp;steuern, ohne&nbsp;dass&nbsp;du&nbsp;an&nbsp;den&nbsp;Modulen&nbsp;selbst&nbsp;Änderungen&nbsp;vornehmen&nbsp;musst.  </p>
<b>Verwendung von Gridblock-Spalteninhalten in Funktionen und Klassen</b><p>Das&nbsp;Gridblock-Addon&nbsp;dafür&nbsp;einzurichten, funktioniert so und braucht folgende Plugins:</p><ol><li><strong>Content&nbsp;Settings</strong>: Stelle sicher, dass du die&nbsp;contentsettings&nbsp;korrekt konfiguriert hast. Diese&nbsp;Einstellungen&nbsp;bestimmen, wie&nbsp;dein&nbsp;Gridblock&nbsp;aussieht und&nbsp;sich&nbsp;verhält.</li><li><strong>Platzhalter definieren:</strong> Definiere&nbsp;die Platzhalter für die Grid-Spalten. Diese Platzhalter sind entscheidend&nbsp;für die&nbsp;Ausgabe&nbsp;der Inhalte in&nbsp;den&nbsp;jeweiligen&nbsp;Spalten.</li><li><strong>Helper-Funktion&nbsp;verwenden</strong>: Nutze&nbsp;eine Helper-Funktion wie z.B&nbsp;helper_GB::getTemplateMarkup,&nbsp;um den HTML-Ausgabe-Wrapper&nbsp;zu&nbsp;generieren. Diese Funktion nimmt die&nbsp;contentsettings&nbsp;und die definierten Platzhalter als Parameter entgegen.</li></ol><p>So minimierst Du den Pflegeaufwand für die Templates, wenn du etwas ändern willst.&nbsp;</p>
<b>Beispiel</b><p>Hier ist&nbsp;ein&nbsp;Beispiel, wie&nbsp;du&nbsp;die&nbsp;Helper-Funktion in&nbsp;deinem Template verwenden kannst:</p><p><code></code>`php</p><p>$cs&nbsp;=&nbsp;$contentsettings;</p><p>$data[1]&nbsp;=&nbsp;&lt;&lt;&lt;'EOT'</p>REX_GRID[1]<p>EOT;</p><p>$data[2]&nbsp;=&nbsp;&lt;&lt;&lt;'EOT'</p>REX_GRID[2]<p>EOT;</p><p>//&nbsp;Füge&nbsp;hier&nbsp;weitere&nbsp;Platzhalter&nbsp;hinzu,&nbsp;falls&nbsp;es mehr Spalten gibt.</p><p>echo&nbsp;helper_GB::getTemplateMarkup($cs,&nbsp;$data);</p><code></code>`

<b>Erklärung des&nbsp;Beispiels</b>
<ul><li>$cs: Dies&nbsp;ist&nbsp;eine&nbsp;Variable, die die&nbsp;contentsettings&nbsp;enthält. Diese Einstellungen werden verwendet, um&nbsp;das&nbsp;Layout&nbsp;und die&nbsp;Stile&nbsp;des Gridblocks&nbsp;zu&nbsp;definieren.</li><li>$data: Dies ist&nbsp;ein&nbsp;Array, das&nbsp;die Platzhalter für&nbsp;die&nbsp;Grid-Spalten&nbsp;enthält. Jeder&nbsp;Platzhalter&nbsp;entspricht&nbsp;einer Spalte im&nbsp;Gridblock. Du&nbsp;kannst beliebig viele Platzhalter&nbsp;hinzufügen, je nach&nbsp;Bedarf.</li><li>Helper-Funktion: Die&nbsp;Funktion&nbsp;helper_GB::getTemplateMarkup&nbsp;generiert&nbsp;den&nbsp;gesamten&nbsp;HTML-Code&nbsp;für&nbsp;den&nbsp;Gridblock&nbsp;basierend&nbsp;auf&nbsp;den&nbsp;übergebenen&nbsp;contentsettings&nbsp;und&nbsp;Platzhaltern. Dadurch wird&nbsp;der Code&nbsp;sauber&nbsp;und&nbsp;wartungsfreundlich.</li></ul>

<b>Implementierung&nbsp;der&nbsp;Helper-Funktion</b><p>Hier&nbsp;ist, wie&nbsp;eine&nbsp;Helper-Funktion wie&nbsp;helper_GB&nbsp;in&nbsp;der&nbsp;Klasse&nbsp;fmHelper_gridblock&nbsp;aussieht:</p><p><code></code>`php</p>class&nbsp;helper_GB&nbsp;{<p>public&nbsp;static&nbsp;function&nbsp;getTemplateMarkup($contentsettings,&nbsp;$data)&nbsp;{</p><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//&nbsp;Beginne&nbsp;mit&nbsp;dem&nbsp;Container</p><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$output&nbsp;=&nbsp;'<div class="' .($contentsettings->template-&gt;container_width)&nbsp;.&nbsp;'&nbsp;'&nbsp;.&nbsp;($contentsettings-&gt;template-&gt;bg_color)&nbsp;.&nbsp;'"&gt;';</div class="' .($contentsettings-></p><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$output&nbsp;.=&nbsp;'<div class="' .($contentsettings->template-&gt;content_width)&nbsp;.&nbsp;'"&gt;';</div class="' .($contentsettings-></p><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$output&nbsp;.=&nbsp;'<div class="row ' .($contentsettings->column_1-&gt;margin_top)&nbsp;.&nbsp;'"&gt;';</div class="row ' .($contentsettings-></p><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//&nbsp;Füge&nbsp;die&nbsp;Grid-Spalten&nbsp;hinzu</p><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;foreach&nbsp;($data&nbsp;as&nbsp;$index&nbsp;=&gt;&nbsp;$placeholder)&nbsp;{</p><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$output&nbsp;.=&nbsp;'<div class="col' .($contentsettings->{'column_'&nbsp;.&nbsp;$index}-&gt;responsive_breakpoint)&nbsp;.&nbsp;'-'&nbsp;.&nbsp;(12&nbsp;/&nbsp;count($data))&nbsp;.&nbsp;'&nbsp;'&nbsp;.&nbsp;($contentsettings-&gt;{'column_'&nbsp;.&nbsp;$index}-&gt;bg_color)&nbsp;.&nbsp;'"&gt;';</div class="col' .($contentsettings-></p><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$output&nbsp;.=&nbsp;'</p><div>'&nbsp;.&nbsp;$placeholder&nbsp;.&nbsp;'</div>';&nbsp;//&nbsp;Platzhalter&nbsp;verwenden<p></p><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$output&nbsp;.=&nbsp;'';</p><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}</p><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//&nbsp;Schließe&nbsp;die&nbsp;Container</p><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$output&nbsp;.=&nbsp;'';</p><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;return&nbsp;$output;</p><p>&nbsp;&nbsp;&nbsp;&nbsp;}</p><p>}</p><code></code>`


<b>Erklärung&nbsp;der Helper-Funktion</b>
<ul><li>Container: Die&nbsp;Funktion&nbsp;beginnt mit dem Erstellen&nbsp;des Hauptcontainers, der&nbsp;die&nbsp;Breite und&nbsp;den Hintergrund&nbsp;des Templates&nbsp;basierend&nbsp;auf den&nbsp;contentsettings&nbsp;festlegt.</li><li>Grid-Spalten: Die&nbsp;Funktion&nbsp;iteriert über das&nbsp;$data-Array, um die&nbsp;Platzhalter&nbsp;für jede&nbsp;Spalte&nbsp;hinzuzufügen. Die&nbsp;CSS-Klassen&nbsp;werden dynamisch&nbsp;basierend auf&nbsp;den&nbsp;contentsettings&nbsp;zugewiesen.</li></ul></div>

<p>&nbsp;</p>
<!-- Fragen / Probleme -->
<h3>Fragen, Wünsche, Probleme?</h3>
Du hast einen Fehler gefunden oder ein nettes Feature parat?<br>
Lege ein Issue unter <a href="<?php echo $this->getProperty('supportpage'); ?>" target="_blank"><?php echo $this->getProperty('supportpage'); ?></a> an. 


<!-- Credits -->
<h3>Credits</h3>
Plugin ContentSettings/Synchronizer: <a href="https://github.com/novinet-git" target="_blank">Daniel Steffen</a><br>
Ursprüngliche Gridblock-Idee: <a href="https://github.com/bloep" target="_blank">Marcel Kuhmann</a><br>
Hilfe beim Debuggen: <a href="https://github.com/rkemmere" target="_blank">Ronny Kemmereit</a>


	</div>
</div>

</div>
</div>
</section>
