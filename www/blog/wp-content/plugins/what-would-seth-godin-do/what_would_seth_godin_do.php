<?php

/*
Plugin Name: What Would Seth Godin Do
Plugin URI: http://richardkmiller.com/wordpress-plugin-what-would-seth-godin-do
Description: Displays a custom welcome message to new visitors and another to return visitors.
Version: 1.6
Author: Richard K Miller
Author URI: http://richardkmiller.com/

Copyright (c) 2006-2008 Richard K Miller
Released under the GNU General Public License (GPL)
http://www.gnu.org/licenses/gpl.txt
*/

$wwsgd_settings = wwsgd_initialize_and_get_settings();

$wwsgd_settings['new_visitor_message'] = stripslashes($wwsgd_settings['new_visitor_message']); // TODO: Is stripslashes() the best thing here?
$wwsgd_settings['return_visitor_message'] = stripslashes($wwsgd_settings['return_visitor_message']); // and here?

add_action('admin_menu', 'wwsgd_options_page');
add_action('send_headers', 'wwsgd_set_cookie');
add_filter('the_content', 'wwsgd_message_filter');

function wwsgd_initialize_and_get_settings()
{
	$defaults = array(
		'new_visitor_message' => "<p style=\"border:thin dotted black; padding:3mm;\">If you're new here, you may want to subscribe to my <a href=\"".get_option("home")."/feed/\">RSS feed</a>. Thanks for visiting!</p>",
		'return_visitor_message' => '',
		'message_location' => 'before_post',
		'include_pages' => 'yes',
		'repetition' => '5'
		);

	add_option('wwsgd_settings', $defaults, 'Options for What Would Seth Godin Do');
	return get_option('wwsgd_settings');	
}

function wwsgd_options_page()
{
	if (function_exists('add_options_page'))
	{
		add_options_page('What Would Seth Godin Do', 'WWSGD', 8, basename(__FILE__), 'wwsgd_options_subpanel');
	}
}

function wwsgd_options_subpanel()
{
	global $wwsgd_settings;
	
	if (isset($_POST['wwsgd_save_settings']))
	{
		check_admin_referer('wwsgd_update_options');
		$wwsgd_settings['new_visitor_message'] = stripslashes($_POST['wwsgd_new_visitor_message']);
		$wwsgd_settings['return_visitor_message'] = stripslashes($_POST['wwsgd_return_visitor_message']);
		$wwsgd_settings['message_location'] = stripslashes($_POST['wwsgd_message_location']);
		$wwsgd_settings['include_pages'] = stripslashes($_POST['wwsgd_message_include_pages']);
		$wwsgd_settings['repetition'] = stripslashes($_POST['wwsgd_repetition']);
		
		update_option('wwsgd_settings', $wwsgd_settings);
	}
	if (isset($_POST['wwsgd_reset_settings']))
	{
		check_admin_referer('wwsgd_reset_options');
		delete_option('wwsgd_settings');
		$wwsgd_settings = wwsgd_initialize_and_get_settings();
	}
	?>
	<div class="wrap">
		<h2>What Would Seth Godin Do</h2>
		<p>"One opportunity that's underused is the idea of using cookies to treat returning visitors differently than newbies...." - <a href="http://sethgodin.typepad.com/seths_blog/2006/08/in_the_middle_s.html" target="_blank">Seth Godin, August 17, 2006</a></p>
		<form action="" method="post">
			<input type="hidden" name="wwsgd_save_settings" value="true" />
			<h3>Message to New Visitors:</h3>
			<textarea rows="3" cols="80" name="wwsgd_new_visitor_message"><?php echo attribute_escape($wwsgd_settings['new_visitor_message']); ?></textarea>
			<h3>Repetition</h3>
			<p>Show the above message the first <input type="text" name="wwsgd_repetition" value="<?php echo attribute_escape($wwsgd_settings['repetition']); ?>" size="3" /> times the user visits your blog. Then display the message below.</p>
			<h3>Message to Return Visitors:</h3>
			<textarea rows="3" cols="80" name="wwsgd_return_visitor_message"><?php echo attribute_escape($wwsgd_settings['return_visitor_message']); ?></textarea>
			<h3>Location of Message</h3>
			<p><input type="radio" name="wwsgd_message_location" value="before_post" <?php if ($wwsgd_settings['message_location'] == 'before_post') echo 'checked="checked"'; ?> /> Before Post
			<input type="radio" name="wwsgd_message_location" value="after_post" <?php if ($wwsgd_settings['message_location'] == 'after_post') echo 'checked="checked"'; ?> /> After Post</p>
			<p><input type="radio" name="wwsgd_message_include_pages" value="yes" <?php if ($wwsgd_settings['include_pages'] == 'yes') echo 'checked="checked"'; ?> /> On Posts and Pages
			<input type="radio" name="wwsgd_message_include_pages" value="no" <?php if ($wwsgd_settings['include_pages'] == 'no') echo 'checked="checked"'; ?> /> On Posts Only</p>
			<p><input type="submit" name="submit" value="Save Settings" /></p><a href="../../../Desktop/what_would_seth_godin_do.php" id="" title="what_would_seth_godin_do">what_would_seth_godin_do</a>
			<?php
			if (function_exists('wp_nonce_field'))
				wp_nonce_field('wwsgd_update_options');
			?>
		</form>
		<form action="" method="post">
			<h3>Reset plugin</h3>
			<p>This may clear up some issues.</p>
			<input type="hidden" name="wwsgd_reset_settings" value="true" />
			<p><input type="submit" name="submit" value="Reset Settings" /></p>
			<?php
			if (function_exists('wp_nonce_field'))
				wp_nonce_field('wwsgd_reset_options');
			?>
			</form>
		<h3>Additional Reading</h3>
		<p><a href="http://sethgodin.typepad.com/seths_blog/2008/03/where-do-we-beg.html" target="_blank">Where do we begin?</a> by Seth Godin</p>
		<p><a href="http://fortuito.us/2007/05/how_ads_really_work_superfans_1" target="_blank">How Ads Really Work: Superfans and Noobs</a> by Matthew Haughey</p>
	</div>
	<?php
}

function wwsgd_set_cookie()
{
	global $wwsgd_visits;
		
	if (!is_admin())
	{
		if (isset($_COOKIE['wwsgd_visits']))
		{
			$wwsgd_visits = $_COOKIE['wwsgd_visits'] + 1;
		}
		else
		{
			$wwsgd_visits = 1;
		}
		$url = parse_url(get_option('home'));
		setcookie('wwsgd_visits', $wwsgd_visits, time()+60*60*24*365, $url['path'] . '/');
	}
}

function wwsgd_message_filter($content = '')
{
	global $wwsgd_visits, $wwsgd_settings, $wwsgd_messagedisplayed;
	
	if ($wwsgd_messagedisplayed || is_feed() || ($wwsgd_settings['include_pages'] == 'no' && is_page()))
	{
		return $content;
	}
	else
	{
		$wwsgd_messagedisplayed = true;
		
		if ($wwsgd_visits <= $wwsgd_settings['repetition'] || 0 == $wwsgd_settings['repetition'])
		{
			$wwsgd_message = $wwsgd_settings['new_visitor_message'];
		}
		else
		{
			$wwsgd_message = $wwsgd_settings['return_visitor_message'];
		}

		if ($wwsgd_settings['message_location'] == 'before_post')
		{
			return $wwsgd_message . $content;
		}
		else
		{
			return $content . $wwsgd_message;
		}
	}
}

?>
