<?php
/*
	REX5 :: Gridblock mit multiplen Spalteninhalten
	Eingabe
	v1.0
	by Falko Müller @ 2021 (based on 0.1.0-dev von bloep)
*/

/* GRID_MODULE_IDENTIFIER | DONT REMOVE */

//Vorgaben
$grid = new rex_gridblock();
$grid->getSliceValues("REX_SLICE_ID");


//$grid->allowModule( array(1,2,3) );			//Optional: IDs der auswählbaren Inhaltsmodule als INT oder ARRAY => 1 | array(1,2,3)
//$grid->ignoreModule( array(2) );				//Optional: IDs der nicht auswählbaren Inhaltsmodule als INT oder ARRAY => 1 | array(1,2,3)


$config = rex_addon::get('gridblock')->getConfig('config');
?>


<?php if (@$config['hideinfotexts'] != 'checked'): ?>
Bitte wählen Sie die gewünschte Layoutvorlage aus und hinterlegen Ihre Inhalte in den entsprechenden Spalten. Speichern Sie Ihre Änderungen mit "Block speichern" bzw. "Block übernehmen".
<br><br>
<?php endif; ?>


<?php echo $grid->getModuleInput(); ?>


<?php if (@$config['hideinfotexts'] != 'checked'): ?>
<br><br>
<strong>Quickinfo:</strong><br>
Mit diesem Modul legen Sie an der entsprechenden Stelle verschiedene Inhalte in einem Spaltenraster an.
<br><br>
<?php endif; ?>