// gridblockcontentsettings
// v1.0

$(function(){

	//Prüfungen & PlugIns aktivieren
	$(document).on("rex:ready",function() { gridblock_initContentsettings(); });
	gridblock_initContentsettings();
	
	function gridblock_initContentsettings()
	{	
		$('.gridblockcontentsettings .selectpicker').selectpicker({ width: "100%"});
		$('.gridblockcontentsettings input.bootstap-slider').slider({});   
		$('.gridblockcontentsettings .dropdown.bootstrap-select.w-100.bs3').css('width','100%');
		//Color-Abgleich
		$('.gridblockcontentsettings div.gridblock-colorinput-group input[type=color]').on("input change", function(){
			$(this).parent().prevAll('input[type=text]').val(this.value);
		});
		$('.gridblockcontentsettings div.gridblock-colorinput-group input[type=text]').on("input change", function(){
			$(this).next().children('input[type=color]').val(this.value);
		});
	}
});