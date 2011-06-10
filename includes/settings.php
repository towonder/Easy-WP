<?php

/* CREATE A SETTINGSPAGE: */
	
	add_action('admin_menu', 'easywp_plugin_menu');

	function easywp_plugin_menu() {
		add_options_page('Easy WP Options', 'Easy WP', 'manage_options', 'easy-wp-options', 'easywp_options');
		add_action('admin_init', 'easywp_register_settings');
	}
		
	function easywp_options() {
		if (!current_user_can('manage_options'))  {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
		
		$saved = false;

		if(!empty($_POST)){
			$godbutton = $_POST['easywp_admin_godbutton'];
			$pages = $_POST['easywp_pages_add_delete'];

			if($godbutton == 'on'){
				update_option('easywp_admin_godbutton', 'true');
			}else{
				update_option('easywp_admin_godbutton', 'false');
			}

			if($pages == 'on'){
				update_option('easywp_pages_add_delete', 'true');
			}else{
				update_option('easywp_pages_add_delete', 'false');
			}
			$saved = true;
		}
		
		
		/* Here we get to create our form: */
?>
<div class="wrap">
	<div id="icon-options-general" class="icon32"><br></div>
	<h2><?php _e('Easy WP Options', 'easy-wp'); ?></h2>
	
	<?php if($saved == true):?>
		<div id="setting-error-settings_updated" class="updated settings-error"> 
		<p><strong>Settings saved.</strong></p></div>
	<?php endif;?>
	
	<form method="post" action="<?php echo admin_url().'options-general.php?page=easy-wp-options' ?>"> 
		<?php 
				settings_fields('easywp-group');
				do_settings_sections('easywp');
			
				$godbutton = get_option('easywp_admin_godbutton');
				$pages = get_option('easywp_pages_add_delete');
		
		?>
	    <table class="form-table">
	        <tr valign="top">
	        <th scope="row">'Simple/Admin-view button' available to everyone</th>
			<?php if($godbutton == 'false' || $godbutton == '0'):?>
	        	<td><input type="checkbox" name="easywp_admin_godbutton" /></td>
	        <?php else: ?>
				<td><input type="checkbox" name="easywp_admin_godbutton" checked /></td>
			<?php endif;?>
			</tr>

	        <tr valign="top">
	        <th scope="row">Create / delete pages</th>
			<?php if($pages == 'false' || $pages == '0'):?>
	        	<td><input type="checkbox" name="easywp_pages_add_delete" /></td>
	        <?php else: ?>
				<td><input type="checkbox" name="easywp_pages_add_delete" checked /></td>
			<?php endif;?>
	        </tr>
	
		</table>
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	</p>
	</form>
</div>
</div>		
<?php
	}
	
	function easywp_register_settings() {
		//register our settings
		register_setting( 'easywp-group', 'easywp_admin_godbutton');
		register_setting( 'easywp-group', 'easywp_pages_add_delete');
	}
	
	
	
?>