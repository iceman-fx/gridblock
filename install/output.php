<?php
/*
	REX5 :: Gridblock mit multiplen Spalteninhalten
	Ausgabe
	v1.0
	by Falko Müller @ 2021 (based on 0.1.0-dev von bloep)
*/

/* GRID_MODULE_IDENTIFIER | DONT REMOVE */

//Vorgaben
$grid = new rex_gridblock();
$grid->getSliceValues("REX_SLICE_ID");


//Ausgabe
echo $grid->getModuleOutput();
?>