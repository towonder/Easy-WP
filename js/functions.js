jQuery(document).ready(function(){
	jQuery('#footer-upgrade').empty();
	jQuery('#footer-upgrade').append('Easy WP, Gemaakt door <a href="http://www.to-wonder.com" target="_blank">To Wonder</a> & <a href="http://www.motiefcollectief.com" target="_blank">Motief Collectief</a>');
	var header1 = jQuery('#postviews').find('h3');
	header1.empty();
	header1.append('Best bezochte paginas');
	
	var header2 = jQuery('#referrers').find('h3');
	header2.empty();
	header2.append('Waar komen de bezoeken vandaan?');
	
	jQuery('#stat1').empty();
	jQuery('#stat1').append('Dagen');
	jQuery('#stat7').empty();
	jQuery('#stat7').append('Weken');
	jQuery('#stat31').empty();
	jQuery('#stat31').append('Maanden'); 
})