<?php
if ( function_exists('register_sidebar') )
    register_sidebar();


function dd_add_admin() {
		
		if ( 'save' == $_REQUEST['dd_action'] ) {

			// Update Options
			update_option('dd_asides_cat', $_REQUEST['dd_asides_cat'] );
			update_option('dd_menu_home', $_REQUEST['dd_menu_home'] );
			update_option('dd_menu_order', $_REQUEST['dd_menu_order'] );
			update_option('dd_gravatars', $_REQUEST['dd_gravatars'] );
	
			// Go back to the options
			header("Location: themes.php?page=functions.php&saved=true");
			die;
		}

    add_theme_page("Monterrey Options", "Monterrey Options", 'edit_themes', basename(__FILE__), 'dd_admin');
	add_option('dd_menu_home', 'yes', 'Show home link the in pages menu', 'yes');
	add_option('dd_menu_order', 'alpha', 'Sorts order of the menu', 'yes');
	add_option('dd_gravatars', 'off', 'Toggles gravatars on and off', 'yes');

}

function dd_admin() {

	if ( $_GET['saved'] ) echo '<div id="message" class="updated fade"><p>Monterrey options saved. <a href="'. get_bloginfo('url') .'">View Site &raquo;</a></strong></p></div>';
	
?>

<div class="wrap">
<h2>Monterrey Options</h2>


	<form id='dd_options' method="post">
	
			<h3>Menu</h3>
				
				<p><label for="dd_menu_home" style="width: 90px;">Home link:</label>
					<input type="radio" name="dd_menu_home" value="yes" <?php if (get_option('dd_menu_home') == "yes") { echo "checked='checked'"; } ?> /> Show in the menu<br />
					<input type="radio" name="dd_menu_home" value="no" style="margin-left: 90px;" <?php if (get_option('dd_menu_home') == "no") { echo "checked='checked'"; } ?> /> Do not show in the menu<br />
					<div class="hint" style="margin-left: 90px;">A link home is also created in the header but an additional link is shown in the menu by deafult.</div></p>
					
				<p><label for="dd_menu_order" style="width: 90px;">Order:</label>
					<input type="radio" name="dd_menu_order" value="alpha" <?php if (get_option('dd_menu_order') == "alpha") { echo "checked='checked'"; } ?> /> Alphabetically<br />
					<input type="radio" name="dd_menu_order" value="by_id" style="margin-left: 90px;" <?php if (get_option('dd_menu_order') == "by_id") { echo "checked='checked'"; } ?> /> By ID<br />
					<input type="radio" name="dd_menu_order" value="page_order" style="margin-left: 90px;" <?php if (get_option('dd_menu_order') == "page_order") { echo "checked='checked'"; } ?> /> Page Order<br />
					<div class="hint" style="margin-left: 90px;">Page order is set when creating and editing pages. Alphabetical is the default.</div></p>

			
			<h3>Asides</h3>
			
				<p><label for="dd_asides_cat">Category for Asides:</label>
					<?php
					global $wpdb;
					$asides_cats = $wpdb->get_results("SELECT * from $wpdb->categories");
					?>
					<select name="dd_asides_cat" id="dd_asides_cat">
						<option value="0">No Asides</option>
						<option value="0">----</option>
						<?php
						foreach ($asides_cats as $cat) {
							if ($cat->cat_ID == get_option('dd_asides_cat')) {
								echo '<option value="' . $cat->cat_ID . '" selected="selected">' . $cat->cat_name . '</option>';
							} else {
								echo '<option value="' . $cat->cat_ID . '">' . $cat->cat_name . '</option>';
							}
						}
						?>
					</select><br />
					<?php 
						if (get_option('dd_asides_cat') == 0) { 
							echo "<div class='hint'>To enable asides please select the category you'd like to use.</div>";
						} else {
							echo "<div class='hint'>Select 'No Asides' to turn Asides off.</div>";
						}
					?>
					</p>
		
		
		<h3>Gravatars</h3>
		
			<?php
				if (function_exists('gravatar')) {
			?>
			
				<p><label>Turn on Gravatars:</label>
					<input type="checkbox" name="dd_gravatars" id="dd_gravatars" <?php if (get_option('dd_gravatars') == "on") { echo "checked='checked'"; } ?> /><br />
					<div class="hint">What on earth is a <a href="http://gravatar.com/">Gravatar</a>? You can change gravatar options via Options &raquo; Gravatars. 
					You could also read <a href="http://www.skippy.net/blog/2005/03/24/gravatars/">Skippy's Documentation</a>.</div></p>
			
			<?php
				} else {
			?>
			
				<p><div class="hint">You must download and activate <a href="http://www.skippy.net/blog/2005/03/24/gravatars/">Skippy's Gravatar Plug-in</a> for the ability to turn them on.</div></p>
			
			<?php
				}
			?>
		
				
		<p><input name="save" id="save" type="submit" value="Save Options" /></p>
		
		<input type="hidden" name="dd_action" value="save" />
	
	</form>

<?php
}

function dd_admin_header() { ?>

	<style media="screen" type="text/css">
		
		form#dd_options { margin: 20px 0 0 40px; }
		
			form#dd_options h3 {
				font-size: 1.5em;
				font-weight: normal;
				margin: 30px 0 0 0;
				}
				
				form#dd_options p { margin: 10px 0; }
				
				form#dd_options label { 
					width: 140px;
					display: block;
					float: left;
					}
					
					form#dd_options div.hint {
						color: #666;
						margin: -8px 0 0 140px;
						}
						
			form#dd_options input#save { margin: 20px 0 0 140px; }
			
			
		
	</style>

<?php }

add_action('admin_head', 'dd_admin_header');
add_action('admin_menu', 'dd_add_admin');

?>