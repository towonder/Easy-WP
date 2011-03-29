<?php
/*
Plugin Name: Easy WP
Plugin URI: http://www.easy-wp.com
Description: Easy WP transforms wordpress into a super-simple CMS;
Version: 0.9
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


add_action('admin_init', 'easywp_init');
add_action('admin_print_scripts', 'easywp_addstyles');
add_action('admin_footer', 'easywp_addui');



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
		Header('Location: '. admin_url());
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
				echo '<link rel="stylesheet" href="'.WP_PLUGIN_URL.'/easy-wp/css/editstyle.css"/>';
			}
		}else if($page == 'stats'){
			//display statspage:
			echo '<link rel="stylesheet" href="'.WP_PLUGIN_URL.'/easy-wp/css/statsstyle.css"/>';
		}else{
			//display main menu:
			echo '<link rel="stylesheet" href="'.WP_PLUGIN_URL.'/easy-wp/css/mainstyle.css" />';
		}

	endif;
}



function easywp_addui(){
	$view = get_option('easywp_adminview');
	$page = $_GET['page'];
	global $pagenow;
	
	if($view == 'false'){
		$adminviewtext = 'Admin weergave';
		$adminviewclass = 'easy-wp-advance';
	}else{
		$adminviewtext = 'Simpele weergave';
		$adminviewclass = 'easy-wp-simple';
	}
	
	$plugins = easywp_get_plugins();
	$pages = get_pages(array('sort_order' => 'ASC','sort_column' => 'menu_order'));
		
	if($view == 'false'){
		if($pagenow == 'post.php' || $pagenow == 'edit.php'){
			if(!ISSET($_GET['view']) || $_GET['view'] == 'main'){
				easywp_set_back_button();	
			}
		}else if($page == 'stats'){
			easywp_set_back_button();
		}else{
			//main menu:
			easywp_setui($pages, $plugins);
			easywp_setbutton($adminviewtext, $adminviewclass);
		}
	}else{
		easywp_setbutton($adminviewtext, $adminviewclass);
	}
	
	echo '<script type="text/javascript" src="'.WP_PLUGIN_URL.'/easy-wp/js/functions.js"></script>';
}


function easywp_setbutton($adminviewtext, $adminviewclass){
	echo '<div id="favorite-actions-new" class="'.$adminviewclass.'"><div id="favorite-first">';
	echo '<a href="'.admin_url().'?toggleView=true">'.$adminviewtext.'</a>';
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
	
	$pamount = count($plugins) + 1;
	$pwidth = $pamount * 140;
	?>
	
	<div id="bigmenutitle">
		Welke pagina wilt u bewerken?
	</div>
	<div id="newmenu">
		<div id="innermenu" style="width:<?php echo $width?>px;margin-left:-<?php echo floor($width / 2);?>px">
		<?php 
			foreach($pages as $page){
				echo '<div class="page_menuitem">';
				echo '<a href="'.get_site_url().'/wp-admin/post.php?post='.$page->ID.'&action=edit">';
				if(strtolower($page->post_title) == 'home'){
					echo '<img src="'.plugins_url().'/easy-wp/img/home.png"/>';
				}else if(strtolower($page->post_title) == 'contact'){
					echo '<img src="'.plugins_url().'/easy-wp/img/contact.png"/>';
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
		Overige functies
	</div>
	
	<div id="secondarymenu">
		<div id="innermenu" style="width:<?php echo $pwidth?>px;margin-left:-<?php echo floor($pwidth / 2);?>px">
			<div class="page_menuitem">
				<a href="<?php echo admin_url();?>admin.php?page=stats">
					<img src="<?php echo plugins_url(); ?>/easy-wp/img/stats.png" /><br/>
					Statistieken</a>
				</a>
			</div>
			<?php foreach($plugins as $plugin):?>
			<div class="page_menuitem">
				<a href="<?php echo admin_url(); ?>edit.php?post_type=page&page=easy-wp-<?php echo $plugin?>">
					<img src="<?php echo plugins_url(); ?>/easy-wp-<?php echo $plugin?>/img/menu.png" /><br/>
					<?php echo ucwords(str_replace('-', ' ', $plugin));?>
				</a>
			</div>
			<?php endforeach;?>
		</div>
	</div>
	
	
	
	
<?php };

function easywp_set_back_button(){
	?>
	<a href="<?php echo admin_url(); ?>" style="color:#f8f8f8;">
		<div id="backbutton" class="button-primary">
			&laquo; Terug
		</div>
	</a>
<?php }
?>