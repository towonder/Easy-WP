<?php
/*
Plugin Name: Easy WP
Plugin URI: http://www.easy-wp.com
Description: Easy WP transforms wordpress into a super-simple CMS;
Version: 1.1
Author: Luc Princen
Author URI: http://www.to-wonder.com
Contributors: Motief:Collectief (http://www.motiefcollectief.com)
*/


/*  Copyright 2011  To Wonder Multimedia & Motief:Collectief (email : hallo@to-wonder.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


if(!load_plugin_textdomain('easy-wp','/wp-content/languages/')){
	load_plugin_textdomain('easy-wp','/wp-content/plugins/easy-wp/languages/');
}

add_action('login_head', 'easywp_login');
add_action('admin_init', 'easywp_init');
add_action('admin_print_scripts', 'easywp_addstyles');
add_action('admin_footer', 'easywp_addui');


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

function easywp_init(){
	
	
	$view = get_option('easywp_adminview');
	if(empty($view)){
		add_option('easywp_adminview', 'false');
	}
	
	if(isset($_GET['toggleView'])){
		if($view == 'false'){
			update_option('easywp_adminview', 'true');
		}else{
			update_option('easywp_adminview', 'false');
		}
		Header('Location: '. $_SERVER['HTTP_REFERER']);
	}	
}



function easywp_addstyles(){
	
	global $pagenow;
	$page = $_GET['page'];
	$view = get_option('easywp_adminview');
	
	?>
	<link rel="stylesheet" href="<?php echo WP_PLUGIN_URL ?>/easy-wp/css/buttonstyle.css"/>	
<?php
	if($view == 'false'): 
		if($pagenow == 'post.php' || $pagenow == 'edit.php' || $pagenow == 'post-new.php'){
			//display editmode:
			if(!ISSET($_GET['view']) || $_GET['view'] == 'main'){
				echo '<link rel="stylesheet" href="'.plugins_url().'/easy-wp/css/editstyle.css"/>';
			}
		}else if($page == 'wp-analytics-reports' || $page == 'wp-analytics-options'){
			//display statspage or edit stats:
			echo '<link rel="stylesheet" href="'.plugins_url().'/easy-wp/css/statsstyle.css"/>';
		}else if($pagenow == 'media-upload.php'){
			echo '<link rel="stylesheet" href="'.plugins_url().'/easy-wp/css/mediastyle.css"/>';
		}else{
			//display main menu:
			echo '<link rel="stylesheet" href="'.plugins_url().'/easy-wp/css/mainstyle.css" />';
		}
	endif;
}


function easywp_addui(){
	$view = get_option('easywp_adminview');
	$page = $_GET['page'];
	global $pagenow;
	
	if($view == 'false'){
		$adminviewtext = __('Admin view', 'easy-wp');
		$adminviewclass = 'advanced.png';
	}else{
		$adminviewtext = __('Simple view', 'easy-wp');
		$adminviewclass = 'simple.png';
	}
	
	//plugins are a work in progress:
	//$plugins = easywp_get_plugins();
	$plugins = array();
	$pages = get_pages(array('sort_order' => 'ASC','sort_column' => 'menu_order'));
		
	if($view == 'false'){
		if($pagenow == 'post.php' || $pagenow == 'edit.php'){
			if(!ISSET($_GET['view']) || $_GET['view'] == 'main'){
				//edit pages / posts:
				easywp_set_back_button($page);	
			}
			//otherwise it's an easy wp plugin.
		}else if($page == 'wp-analytics-reports' || $page == 'wp-analytics-options'){
			//analytics
			easywp_set_back_button($page);
		}else{
			//main menu:
			easywp_setui($pages, $plugins);
		}
		
		echo '<script type="text/javascript">var theUrl = "'.plugins_url().'/easy-wp/";var loadTitle = "'. __('Loading', 'easy-wp').'";var loadBody = "'. __('please wait', 'easy-wp').'"; var credits ="'.__('Easy WP, created by <a href=\"http://www.to-wonder.com\" target=\"_blank\">To Wonder</a> & <a href=\"http://www.motiefcollectief.com\" target=\"_blank\">Motief Collectief</a>', 'easy-wp').'"</script>';
		echo '<script type="text/javascript" src="'.plugins_url().'/easy-wp/js/functions.js"></script>';
		echo '<script type="text/javascript" src="'.plugins_url().'/easy-wp/js/loader.js"></script>';
	}	
	easywp_setbutton($adminviewtext, $adminviewclass);

}


function easywp_setbutton($adminviewtext, $adminviewclass){
	echo '<div id="easy-wp-logo"><a href="'.get_site_url().'"><img src="'.plugins_url().'/easy-wp/img/logo.png" title="Powered by Wordpress"></a></div>';
	echo '<div id="favorite-actions-new" onclick="window.location.href=\''.admin_url().'?toggleView=true\'"><div id="favorite-first"><img src="'.plugins_url().'/easy-wp/img/'.$adminviewclass.'" class="fav-img" />';
	echo '<a href="'.admin_url().'?toggleView=true" id="fav-link">'.$adminviewtext.'</a>';
	echo '</div></div>';
}

function easywp_get_plugins(){
	$plugins = array();
	$gallery = get_option('easywp-gallery-active');
	if($gallery == 'true'){
		$plugins[] = 'gallery';
		
	}
	
	return $plugins;
}


function easywp_setui($pages, $plugins){?>
	
	<?php
	
	$amount = count($pages);
	$width = $amount * 140;
	$pamount = count($plugins);
	
	$stats = false;
	$statssettings = false;
	if(is_plugin_active('wp-analytics/analytics.php')){
		$stats = true;
		$pamount++;
		
		$a = get_option( 'wp-analytics-login-email' );
		$b = get_option ( 'wp-analytics-login-password' );
		$c = get_option ( 'wp-analytics-profile' );
		if(!empty($a) && !empty($b) && !empty($c) && $a != '' && $b != '' && $c != ''){
			$statssettings = true;
		}
	}
	
	$pwidth = $pamount * 140;
		
	?>
	
	<div id="bigmenutitle">
		<?php _e('Which page would you like to edit?', 'easy-wp');?>
	</div>
	<div id="newmenu">
		<div id="innermenu" style="width:<?php echo $width?>px;margin-left:-<?php echo floor($width / 2);?>px">
		<?php 
			foreach($pages as $page){
				echo '<div class="page_menuitem">';
				echo '<a href="'.get_site_url().'/wp-admin/post.php?post='.$page->ID.'&action=edit">';
				if(strtolower($page->post_title) == 'home' || strtolower($page->post_title) == 'homepage'){
					echo '<img src="'.plugins_url().'/easy-wp/img/home.jpg"/>';
				}else if(strtolower($page->post_title) == 'contact'){
					echo '<img src="'.plugins_url().'/easy-wp/img/contact.jpg"/>';
				}else{
					echo '<img src="'.plugins_url().'/easy-wp/img/page.png"/>';
				}
				echo '<br/>'.ucwords($page->post_title).'</a>';
				echo '</div>';
			}
		
		?>
		</div>
	</div>
		
	<div id="secondarymenutitle">
		<?php _e('Other functions', 'easy-wp');?>
		
	</div>
	<?php if($pwidth != 0):?>
	<div id="secondarymenu">
		<div id="innermenu" style="width:<?php echo $pwidth?>px;margin-left:-<?php echo floor($pwidth / 2);?>px">
			<?php if($stats == true && $statssettings == true):?>
				<div class="page_menuitem">
					<a href="<?php echo admin_url();?>index.php?page=wp-analytics-reports">
						<img src="<?php echo plugins_url(); ?>/easy-wp/img/stats.png" /><br/>
						<?php _e('Statistics', 'easy-wp');?>
					</a>
				</div>
			<?php elseif($stats == true && $statssettings == false):?>
				<div class="page_menuitem">
					<a href="<?php echo admin_url();?>plugins.php?page=wp-analytics-options">
						<img src="<?php echo plugins_url(); ?>/easy-wp/img/stats.png" /><br/>
						<?php _e('Setup statistics', 'easy-wp');?></a>
					</a>
				</div>
			<?php endif; ?>
			
		</div>
	</div>
	<?php endif;?>
	
	<?php if($stats == false):?>
		<p class="nostats"><?php _e('You can get free Google-analytics stats in Easy WP, download <a href="http://wordpress.org/extend/plugins/wp-analytics/ " target="_blank">this excellent plugin by imthiaz</a>', 'easy-wp')?></p>
	<?php endif;?>
<?php };



function easywp_set_back_button($page){
	
	$img = '';
	$title = '';
	
	if($page == null){
		$post_query = $_GET['post'];
		if(isset($post_query)){
			$current = get_post($post_query);
			$title = strtolower($current->post_title);
			if($title == 'home' || $title == 'homepage'){
				$img = plugins_url().'/easy-wp/img/home.jpg';
			}else if($title == 'contact'){
				$img = plugins_url().'/easy-wp/img/contact.jpg';
			}else{
				$img = plugins_url().'/easy-wp/img/page.png';
			}
			$overviewtitle = $current->post_title;
		}
	}else{
		if($page == 'wp-analytics-reports' || $page == 'wp-analytics-options'){
			$img = plugins_url().'/easy-wp/img/stats.png';
			$overviewtitle = __('Statistics', 'easy-wp');
		}
	}
	
	
	?>	
	<div id="backsection">
		<div id="easy-wp-overview">
			<img src="<?php echo $img; ?>"><br/>
				<?php echo $overviewtitle; ?>
			</div>
		<a href="<?php echo admin_url(); ?>" style="color:#f8f8f8;">
			<div id="backbutton" class="button-primary">
				<p class="backtext">&laquo; <?php _e('Back', 'easy-wp');?></p>
			</div>
		</a>
	</div>
<?php }
?>