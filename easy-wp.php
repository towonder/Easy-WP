<?php
/*
Plugin Name: Easy WP
Plugin URI: http://www.easy-wp.com
Description: Easy WP transforms wordpress into a super-simple CMS;
Version: 1.5
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

include('includes/login.php');
include('includes/settings.php');


add_action('admin_init', 'easywp_init');
add_action('admin_menu', 'easywp_addscripts');
add_action('admin_footer', 'easywp_addui');


function easywp_init(){
	
	
	$view = get_option('easywp_adminview');
	if(empty($view)){
		add_option('easywp_adminview', 'false');
		add_option('easywp_admin_godbutton', 'false');
		add_option('easywp_pages_add_delete', 'false');
		
		add_option('easy-wp-current-button', '');
		add_option('easy-wp-current-title', '');
		add_option('easy-wp-current-backlink', '');
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


function easywp_addscripts(){

	//scripts:
	wp_enqueue_script('easy_wp_jqui', plugins_url() . '/easy-wp/js/jquery-ui-1.8.12.custom.min.js');


	//styles:
	global $pagenow;
	$page = $_GET['page'];
	$view = get_option('easywp_adminview');

	wp_enqueue_style('easy_wp_button_style', plugins_url().'/easy-wp/css/buttonstyle.css');


		if($view == 'false'): 
			if($pagenow == 'post.php' || $pagenow == 'edit.php' || $pagenow == 'post-new.php'){
				//display editmode:
				if(!ISSET($_GET['view']) || $_GET['view'] == 'main'){
					wp_enqueue_style('easy_wp_edit_style', plugins_url().'/easy-wp/css/editstyle.css');
				}
			}else if($page == 'wp-analytics-reports' || $page == 'wp-analytics-options'){
				//display statspage or edit stats:
				wp_enqueue_style('easy_wp_stats_style', plugins_url().'/easy-wp/css/statsstyle.css');
			}else if($pagenow == 'media-upload.php'){
				wp_enqueue_style('easy_wp_media_style', plugins_url().'/easy-wp/css/mediastyle.css');
			}else{
				//display main menu:
				wp_enqueue_style('easy_wp_main_style', plugins_url().'/easy-wp/css/mainstyle.css');
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
	$plugins = easywp_get_plugins();

	//$plugins = array();
	$pages = get_pages(array('sort_order' => 'ASC','sort_column' => 'menu_order'));
		
	if($view == 'false'){
		if($pagenow == 'post.php' || $pagenow == 'edit.php' || $pagenow == 'post-new.php'){
			if(!ISSET($_GET['view']) || $_GET['view'] == 'main'){
				//edit pages / posts:
				
				if(!easywp_current_page_is_ewp_plugin()){
					update_option('easy-wp-current-button', '');
					update_option('easy-wp-current-title', '');
					update_option('easy-wp-current-backlink', '');					
				}
				
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
		
		//Add the javascripts quick and dirty, so we can pass some variables:
		echo '<script type="text/javascript">var theUrl = "'.plugins_url().'/easy-wp/"; var homeUrl = "'.admin_url().'"; var loadTitle = "'. __('Loading', 'easy-wp').'";var loadBody = "'. __('please wait', 'easy-wp').'"; var credits ="'.__('Easy WP, created by <a href=\"http://www.to-wonder.com\" target=\"_blank\">To Wonder</a> & <a href=\"http://www.motiefcollectief.com\" target=\"_blank\">Motief Collectief</a>', 'easy-wp').'"</script>';
		echo '<script type="text/javascript" src="'.plugins_url().'/easy-wp/js/functions.js"></script>';
		echo '<script type="text/javascript" src="'.plugins_url().'/easy-wp/js/loader.js"></script>';
	}	
	easywp_setbutton($adminviewtext, $adminviewclass);

}


function easywp_setbutton($adminviewtext, $adminviewclass){
	echo '<div id="easy-wp-logo"><a href="'.get_site_url().'"><img src="'.plugins_url().'/easy-wp/img/logo.png" title="Powered by Wordpress"></a></div>';
	
	$godbutton = get_option('easywp_admin_godbutton');
	
	if($godbutton == 'true' || current_user_can('administrator')){
		//everybody can see the button
		echo '<div id="favorite-actions-new" onclick="window.location.href=\''.admin_url().'?toggleView=true\'"><div id="favorite-first"><img src="'.plugins_url().'/easy-wp/img/'.$adminviewclass.'" class="fav-img" />';
		echo '<a href="'.admin_url().'?toggleView=true" id="fav-link">'.$adminviewtext.'</a>';
		echo '</div></div>';
	}
}


function easywp_get_plugins(){
	$plugins = array();
	
	$plugs = scandir(WP_CONTENT_DIR.'/plugins');
	$a = 0;
	foreach($plugs as $pl){
		if(substr($pl, 0, 8) == 'easy-wp-'){
			if(is_plugin_active($pl.'/'.$pl.'.php')){
				$plugins[$a] = $pl;
				$a++;
			}
		}
	}
	 return $plugins;
}


function easywp_current_page_is_ewp_plugin(){
	
	global $pagenow;
	$plugins = easywp_get_plugins();
	
	if(ISSET($_GET['post'])){
		
		foreach($plugins as $pl){
			if('easy-wp-'.get_post_type($_GET['post']) == $pl){
				return true;
				break;
			}
		}
		
	}else{
		if(!ISSET($_GET['post_type'])){
			return false;
		}else{
			return true;
		}
		
	}
}


function easywp_setui($pages, $plugins){?>
	
	<?php
	$pagesoptions = get_option('easywp_pages_add_delete');
	if($pagesoptions == 'false'){
		$amount = count($pages);
	}else{
		$amount = count($pages) + 1;
	}
	
	
	$width = $amount * 140;
	$pamount = count($plugins);
	
		
	$stats = false;
	$noplugins = true;
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
	
	if(!empty($plugins)){
		$noplugins = false;
	}
	
	$pwidth = $pamount * 140;
	
	
	?>
	
	<div id="bigmenutitle">
		<?php _e('Which page would you like to edit?', 'easy-wp');?>
	</div>
	<div id="newmenu">
		<div id="innermenu" style="width:<?php echo $width?>px;margin-left:-<?php echo floor($width / 2);?>px">
			<?php if($pagesoptions == 'true'):?>
				<div class="page_menuitem">
					<a href="<?php echo admin_url()?>post-new.php?post_type=page">
						<img src="<?php echo plugins_url()?>/easy-wp/img/new.png" /><br/>
						<?php _e('Add new page', 'easy-wp');?>
					</a>
				</div>
			<?php endif; ?>
			
		<?php 
			foreach($pages as $page){
				if($pageoption == 'false'){
					echo '<div class="page_menuitem">';
				}else{
					echo '<div class="page_menuitem draggable" id="'.$page->ID.'">';
				//	$nonce= wp_create_nonce('my-nonce');
					echo '<div id="post-'.$page->ID.'" style="display:none">';
					$delLink = wp_nonce_url( get_bloginfo('url') . "/wp-admin/post.php?action=delete&amp;post=" . $page->ID, 'delete-post_' . $page->ID);				    
					echo '<a class="submitdelete"  href="'.get_delete_post_link($page->ID).'"></a>';
					echo '</div>';
				}
				
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
			
			<?php foreach($plugins as $plugin):?>
				<?php 
					$ptype = '';
					$ptype = get_option($plugin.'-posttype');
				?>
				<div class="page_menuitem">
					<?php if($ptype != ''):?>
						<a href="<?php echo admin_url();?>edit.php?post_type=<?php echo $ptype?>">
					<?php else:?>
						<a href="<?php echo admin_url();?>edit.php">
					<?php endif;?>
							<img src="<?php echo plugins_url();?>/<?php echo $plugin?>/img/btn.png"><br/>
							<?php echo get_option($plugin.'-name');?>
					</a> 
				</div>
			<?php endforeach;?>
		</div>
	</div>
	<?php endif;?>
	
	<?php if($stats == false):?>
		<p class="nostats"><?php _e('You can get free Google-analytics stats in Easy WP, download <a href="http://wordpress.org/extend/plugins/wp-analytics/ " target="_blank">this excellent plugin by imthiaz</a>', 'easy-wp')?></p>
	<?php endif;?>
	
	<?php if($pagesoptions == 'true'):?>
		<div id="droppable">
		</div>
	<?php endif;?>
	
<?php };



function easywp_set_back_button($page, $posttype){
	
	$img = '';
	$title = '';
	$postview = false;
	
	$currentbutton = get_option('easy-wp-current-button');
	
	//check if we are dealing with a plugin:
	if(!easywp_current_page_is_ewp_plugin()){
		if($page == null){
			$post_query = $_GET['post'];
			if(isset($post_query)){
				$postview = true;
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
			}else{
				$img = plugins_url().$page.'/img/btn.png';
			}
		}
	}else if($_GET['post_type'] != 'page'){
		//we are: 
		global $pagenow;
		$post_query = $_GET['post_type'];
			
		if($pagenow == 'post.php' || $pagenow == 'post-new.php'){
			$postview = true;
		}
		
		$img = $currentbutton;
		$overviewtitle = get_option('easy-wp-current-title');
		$backlink = get_option('easy-wp-current-backlink');
	}else{
		$img = plugins_url().'/easy-wp/img/page.png';
		$overviewtitle = 'newpage';
	}
	
	
	?>	
	<div id="backsection">
		<div id="easy-wp-overview">
			<img src="<?php echo $img; ?>"><br/>
				<?php if($overviewtitle != 'newpage'):?>
					<?php echo $overviewtitle; ?>
				<?php else:?>
					<?php _e('New page', 'easy-wp');?>
				<?php endif;?>
			</div>
		
		<?php if($postview == false || $backlink == ''):?>	
			<a href="<?php echo admin_url(); ?>" style="color:#f8f8f8;">
		<?php else:?>
			<a href="<?php echo admin_url();?>edit.php?post_type=<?php echo $backlink?>" style="color:#f8f8f8">
		<?php endif;?>
			
			<div id="backbutton" class="button-primary">
				<p class="backtext">&laquo; <?php _e('Back', 'easy-wp');?></p>
			</div>
		</a>
	</div>
<?php }
?>