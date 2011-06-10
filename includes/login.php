<?php

add_action('login_head', 'easywp_login');

function easywp_login(){
	$adminview = get_option('easywp_adminview');
	if($adminview == 'false'){
		?>
			<link rel="stylesheet" href="<?php echo WP_PLUGIN_URL ?>/easy-wp/css/loginstyle.css"/>
		<?php
	
		add_action('login_form', 'easywp_login_logo');
	}
}

function easywp_login_logo(){
	$adminview = get_option('easywp_adminview');
	
	if($adminview == 'false'){
		echo '<div id="loginlogo"><a href="http://www.easy-wp.com" target="_blank"><img src="'.plugins_url().'/easy-wp/img/logo_small.png"/></a></div>';
	}
}


?>