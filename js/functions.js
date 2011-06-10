var c = credits;
jQuery(document).ready(function(){
	jQuery('#footer-upgrade').empty();
	jQuery('#footer-upgrade').append(c);	
	jQuery('#easy-wp-loader').fadeOut('slow');

	jQuery('.draggable').draggable({
		revert: 'invalid',
		helper: 'clone',
		cursor: 'move'
	});
	jQuery("#droppable").droppable({
		drop: function(event, ui) {
			if (confirm('Wil je dit echt verwijderen?')) {
				deleteThing(ui.draggable);
			}
		}
	});	
			
});

function deleteThing($item){
	var id = $item.attr('id');	
	
	//get the url for the wpnonce:	
	var url = jQuery('#post-'+id+' .submitdelete').attr('href');
	
	jQuery.ajax({ 
		type : 'get',
		url: url,
  		success: function(data) {
			$item.fadeOut('fast');
			var w = jQuery('#innermenu').width();
  			var ml = jQuery('#innermenu').css('margin-left').replace('px', '');
			jQuery('#innermenu').css('width', w - 140 +'px');
			jQuery('#innermenu').css('margin-left', parseInt(ml) + 70 +'px');
		}
	});
}

