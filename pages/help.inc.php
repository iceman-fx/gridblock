<?php
/*
	Redaxo-Addon Gridblock
	Verwaltung: Hilfe
	v0.8
	by Falko Müller @ 2021 (based on 0.1.0-dev von bloep)
*/
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
<p>Um dem Redakteur ein Auswahl bieten zu können, legen Sie die gewünschte Anzahl an Vorlagen an oder installieren bei der ersten Verwendung einige Beispielvorlagen über die entsprechende Schaltfläche.<br>
Die hinterlegten Vorlagen können anschließend vom Redakteur im Gridmodul gewählt werden.</p>
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
                          Beispiel:
<br>
<pre>&lt;div&gt;<br>	&lt;div&gt;REX_GRID[1]&lt;/div&gt;<br>	&lt;div&gt;REX_GRID[2]&lt;/div&gt;<br>	&lt;div&gt;REX_GRID[3]&lt;/div&gt;<br>&lt;/div&gt;</pre>

Mögliche Platzhalter für Spaltenausgabe:<br>
<strong>REX_GRID[1] </strong> = Ausgabe der Inhaltsmodule der jeweiligen Spalte (max. 12 Spalten)<br>
<strong>REX_GRID[id=1 output=class] </strong> = Ausgabe der  CSS-Klassen der nativen Optionen der jeweiligen Spalte (sofern aktiviert)<br>
<strong>REX_GRID[id=1 output=style] </strong> = Ausgabe der  CSS-Stile der nativen Optionen der jeweiligen Spalte (sofern aktiviert)<br>
<br>
Mögliche Platzhalter für gesamten Gridblock:<br>
<strong>REX_GRID[output=class] </strong> = Ausgabe der  CSS-Klassen der nativen Optionen für den gesamten Gridblock (sofern aktiviert)<br>
<strong>REX_GRID[output=style] </strong> = Ausgabe der  CSS-Stile der nativen Optionen für den gesamten Gridblock (sofern aktiviert)<br>
<br>
Beispiel mit allen Platzhaltern: <br>
<pre>&lt;div class=&quot;REX_GRID[output=class]&quot; style=&quot;REX_GRID[output=style]&quot;&gt;<br>	&lt;div&gt;REX_GRID[1]&lt;/div&gt;<br>	&lt;div class=&quot;REX_GRID[id=2 output=class]&quot;&gt;REX_GRID[2]&lt;/div&gt;<br>	&lt;div style=&quot;REX_GRID[id=3 output=style]&quot;&gt;REX_GRID[3]&lt;/div&gt;<br>&lt;/div&gt;</pre></td>
                      </tr>
                      <tr>
                        <td valign="top"><strong>Definition Layoutvorschau (JSON)</strong></td>
                        <td valign="top">Über eine Layoutvorschau kann die  Spaltenraster einfach visualisiert werden.<br>
                          Die Definition muss dabei im JSON-Format erfolgen. <br>
                          <br>
                          Beispiel:
<br>
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
<p>Mittels weiterer nativer Optionen können einige zusätzliche und öfters genutzte Spalten- und Blockeinstellungen definiert werden, welche anschließend mittels der REX_GRID-Platzhalter abgerufen werden können.  Optional besteht auch die Möglichkeit der Anbindung eines externen Einstellungsaddons (z.B. <a href="https://github.com/novinet-git/nv_modulesettings" target="_blank">nv_modulesettings</a>), wofür die REX_VALUE[20] freigehalten wurde.</p>
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
                      <tr>
                        <td valign="top"><strong>Native Optionen</strong></td>
                        <td valign="top">Hier können die nativen Optionen für Block &amp; Spalten aktiviert und weiter verfeinert werden.<br>
                          Aktive Optionen stehen anschließend imd Gridblock-Modul zur Verwaltung bereit und können 
                          mittels der REX_GRID-Platzhalter abgerufen werden.                        
                        </td>
                      </tr>
                  </table>



<p>&nbsp;</p>



<!-- Modul -->
<a name="modul"></a>
<h2>Gridblock-Modul für Redakteure</h2>                    
                    
<p>Das Gridblock-Modul wurde i.d.R. bei der Installation des Addons bereits mit installiert, so dass dieses sofort für das Anlegen eines Gridblockes genutzt werden kann.<br>
  Sollte dies nicht der Fall sein oder es sollen weitere Optionen definiert werden, dann kann ein eigenes Modul angelegt werden..
</p>
<p>Wichtig: Der Kommentar <strong>/* GRID_MODULE_IDENTIFIER | DONT REMOVE */</strong> sollte nicht entfernt werden!</p>
<p>&nbsp;</p>


                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <th width="200" scope="col">Modultyp</th>
                        <th scope="col">Erklärung &amp; Attribute</th>
                    </tr>
                      <tr>
                        <td valign="top"><strong>Moduleingabe (INPUT)</strong></td>
                        <td valign="top"><pre>&lt;?php<br>/* GRID_MODULE_IDENTIFIER | DONT REMOVE */                      
                        
$grid = new rex_gridblock();<br>$grid-&gt;getSliceValues(&quot;REX_SLICE_ID&quot;);<br><br>//$grid-&gt;allowModule( array(1,2,3) );			//Optional: IDs der auswählbaren Inhaltsmodule als INT oder ARRAY =&gt; 1 | array(1,2,3)<br>//$grid-&gt;ignoreModule( array(2) );				//Optional: IDs der nicht auswählbaren Inhaltsmodule als INT oder ARRAY =&gt; 1 | array(1,2,3)<br><br>echo $grid-&gt;getModuleInput();<br>?&gt;</pre></td>
                      </tr>
                      <tr>
                        <td valign="top"><strong>Modulausgabe (OUTPUT)</strong></td>
                        <td valign="top"><pre>&lt;?php<br>/* GRID_MODULE_IDENTIFIER | DONT REMOVE */                      
                        
$grid = new rex_gridblock();<br>$grid-&gt;getSliceValues(&quot;REX_SLICE_ID&quot;);<br>?&gt;<br>
&lt;div class=&quot;grid-container&quot;&gt;
   &lt;?php echo $grid-&gt;getModuleOutput(); ?&gt;
&lt;/div&gt;</pre></td>
                      </tr>
                  </table>
                  
                                    
                  

                    <p>&nbsp;</p>
                    
                    <h3>Fragen, Wünsche, Probleme?</h3>
                    Du hast einen Fehler gefunden oder ein nettes Feature parat?<br>
				Lege ein Issue unter <a href="<?php echo $this->getProperty('supportpage'); ?>" target="_blank"><?php echo $this->getProperty('supportpage'); ?></a> an. 
                    
</div>
			</div>

	  </div>
	</div>
</section>