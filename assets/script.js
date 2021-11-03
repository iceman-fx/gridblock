// gridblock
// v1.0

$(function(){
	//Prüfungen & PlugIns aktivieren
	$(document).on("rex:ready", 'div.gridblock', function() { gridblock_initPlugins(); });
	gridblock_initPlugins();
});


function gridblock_initPlugins()
{	//console.log("init");

	//Inhaltsmodule sperren wenn disabled
	$('div.gridblock-slice-disabled input, div.gridblock-slice-disabled textarea, div.gridblock-slice-disabled select').attr('readonly', 'readonly');

	//Color-Abgleich
	$('div.gridblock-colorinput-group input[type=color]').on("input change", function(){
		$(this).parent().prevAll('input[type=text]').val(this.value);
	});
	$('div.gridblock-colorinput-group input[type=text]').on("input change", function(){
		$(this).next().children('input[type=color]').val(this.value);
	});
}