<?php
/*
	Redaxo-Addon Gridblock
	Verwaltung: AJAX Loader - Template-Liste
	v1.0
	by Falko Müller @ 2021 (based on 0.1.0-dev von bloep)
*/

//Variablen deklarieren
$page = rex_request('page', 'string');
$subpage = "";																//ggf. manuell setzen
$subpage2 = rex_be_controller::getCurrentPagePart(3);						//2. Unterebene = dritter Teil des page-Parameters
	$subpage2 = preg_replace("/.*-([0-9])$/i", "$1", $subpage2);			//Auslesen der ClangID

$sbeg = trim(urldecode(rex_request('sbeg')));
$id = rex_request('id', 'int');

$order = (strtolower(rex_request('order')) == 'desc') ? 'DESC' : 'ASC';

$limStart = rex_request('limStart', 'int');


//Sessionwerte zurücksetzen
$_SESSION['as_sbeg_gridtemplatelist'] = $_SESSION['as_id_gridtemplatelist'] = "";


//AJAX begin
echo '<!-- ###AJAX### -->';


//SQL erstellen und Filterung berücksichtigen
$sql = "SELECT id, prio, title, description, columns, preview, status FROM ".rex::getTable('1620_gridtemplates');
$sql_where = " WHERE 1";


//Eingrenzung: Suchbegriff
if (!empty($sbeg)):
	$_SESSION['as_sbeg_gridtemplatelist'] = $sbeg;
	$sql_where .= " AND ( 
					BINARY LOWER(title) like LOWER('%".aFM_maskSql($sbeg)."%') 
					OR BINARY LOWER(description) like LOWER('%".aFM_maskSql($sbeg)."%')
					)";
					//BINARY sorgt für einen Binärvergleich, wodurch Umlaute auch als Umlaute gewertet werden (ohne BINARY ist ein Ä = A)
					//LOWER sorgt für einen Vergleich auf Basis von Kleinbuchstaben (ohne LOWER würde das BINARY nach Groß/Klein unterscheiden)
					//DATE_FORMAT wandelt den Wert in eine andere Schreibweise um (damit kann der gespeicherte Wert vom gesuchten Wert abweichen) --> DATE_FORMAT(`date`, '%e.%m.%Y')
					//FROM_UNIXTIME arbeit wie DATE-FORMAT, aber benötigt als Quelle einen timestamp
					//		OR ( FROM_UNIXTIME(`date`, '%e.%m.%Y') like '".aFM_maskSql($sbeg)."%' OR FROM_UNIXTIME(`date`, '%d.%m.%Y') like '".aFM_maskSql($sbeg)."%' )
endif;


//Sortierung
$sql_where .= " ORDER BY prio ".$order.", columns ASC, title ASC, id ASC";


//Limit
$limStart = ($limStart > 0) ? $limStart : 0;
$limCount = 25;
$sql_limit = " LIMIT ".($limStart * $limCount).",".$limCount;


//SQL zwischenspeichern
//$_SESSION['as_sql_gridblock'] = $sql.$sql_where;


//Ergebnisse nachladen
$db = rex_sql::factory();
$db->setQuery($sql.$sql_where.$sql_limit);
$addPath = "index.php?page=".$page;

	//echo "<tr><td colspan='10'>$sql$sql_where$sql_limit</td></tr>";
	//echo "<tr><td colspan='10'>Anzahl Datensätze: ".$db->getRows()."</td></tr>";

            if ($db->getRows() > 0):
                //Liste ausgeben
                for ($i=0; $i < $db->getRows(); $i++):
					$eid = intval($db->getValue('id'));
					$editPath = $addPath.'&amp;func=update&amp;id='.$eid;
						
					$status = ($db->getValue('status') == "checked") ? 'online' : 'offline';

					$prio = $db->getValue('prio', 'int');
					$title = aFM_maskChar(aFM_textOnly($db->getValue('title'), true));
					$desc = aFM_maskChar(aFM_textOnly($db->getValue('description'), true));
					$columns = $db->getValue('columns', 'int');
					$preview = $db->getValue('preview');
						$preview = (!empty($preview)) ? json_decode($preview, TRUE) : '';
						
					$preview = a1620_generatePreview($preview, $eid, $columns, $title, $desc);
						
					
					//Ausgabe
                    ?>
                        
                    <tr id="entry<?php echo $eid; ?>">
                        <td class="rex-table-icon"><a href="<?php echo $editPath; ?>" title="<?php echo $this->i18n('a1620_edit'); ?>"><i class="rex-icon rex-icon-article"></i></a></td>
                        <td class="rex-table-id"><?php echo $eid; ?></td>
                        <td data-title="<?php echo $this->i18n('a1620_bas_list_name'); ?>"><a href="<?php echo $editPath; ?>"><?php echo $title; ?></a></td>
                        <td data-title="<?php echo $this->i18n('a1620_bas_list_columns'); ?>"><?php echo $columns; ?></td>
                        <td data-title="<?php echo $this->i18n('a1620_bas_list_preview'); ?>"><?php echo $preview; ?></td>
                        <td data-title="<?php echo $this->i18n('a1620_bas_list_priority'); ?>"><?php echo $prio; ?></td>
                        <td class="rex-table-action"><a href="<?php echo $editPath; ?>"><i class="rex-icon rex-icon-edit"></i> <?php echo $this->i18n('a1620_edit'); ?></a></td>
                        <td class="rex-table-action"><a href="<?php echo $addPath; ?>&func=duplicate&id=<?php echo $eid; ?>"><i class="rex-icon rex-icon-duplicate"></i> <?php echo $this->i18n('a1620_duplicate'); ?></a></td>
                        <td class="rex-table-action"><a href="<?php echo $addPath; ?>&func=delete&id=<?php echo $eid; ?>" data-confirm="<?php echo $this->i18n('a1620_delete'); ?> ?"><i class="rex-icon rex-icon-delete"></i> <?php echo $this->i18n('a1620_delete'); ?></a></td>
                        <td class="rex-table-action"><a href="<?php echo $addPath; ?>&func=status&id=<?php echo $eid; ?>" class="rex-<?php echo $status; ?>"><i class="rex-icon rex-icon-<?php echo $status; ?>"></i> <?php echo ($status == "online") ? $this->i18n('a1620_online') : $this->i18n('a1620_offline'); ?></a></td>
                    </tr>

                    <?php
					$db->next();
                endfor;
				
				
				//Seitenschaltung generieren
				$dbl = rex_sql::factory();
				$dbl->setQuery($sql.$sql_where);
					$maxEntry = $dbl->getRows();
					$maxSite = ceil($maxEntry / $limCount);

				if ($dbl->getRows() > $limCount):
					echo '<tr><td colspan="10" align="center"><ul class="addon_list-pagination pagination">';
					
					for ($i=0; $i<$maxSite; $i++):
						$sel = ($i == $limStart) ? 'ajaxNavSel' : '';
						$selLI = ($i == $limStart) ? 'active' : '';
						echo '<li class="rex-page '.$selLI.'"><span class="ajaxNav '.$sel.'" data-navsite="'.$i.'">'.($i+1).'</span></li>';
					endfor;
					
					echo '</ul></td></tr>';
				endif;
				
            else:
                ?>
                
                    <tr>
                        <td colspan="10" align="center"> - <?php echo $this->i18n('a1620_search_notfound'); ?> -</td>
                    </tr>
                
                <?php
            endif;

//AJAX end
echo '<!-- ###/AJAX### -->';
?>