<?php
/*
	REX5 :: Gridblock mit multiplen Spalteninhalten
	Ausgabe
	v0.8
	by Falko Müller @ 2021 (based on 0.1.0-dev von bloep)
*/

/* GRID_MODULE_IDENTIFIER | DONT REMOVE */

//Vorgaben
$grid = new rex_gridblock();
$grid->getSliceValues("REX_SLICE_ID");


//Ausgabe
?>

<div class="grid-container">
	<?php echo $grid->getModuleOutput(); ?>
</div>