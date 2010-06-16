<?php

function fbprofile_content($more_link_text = '(more...)', $stripteaser = 0, $more_file = '') {
	$content = get_the_content($more_link_text, $stripteaser, $more_file);
	// Apply only Wordpress default filters
	$content = wptexturize($content);
	// $content = convert_smilies($content);
	$content = convert_chars($content);
	$content = wpautop($content);
	$content = do_shortcode($content);
	$content = prepend_attachment($content);
	$content = str_replace(']]>', ']]&gt;', $content);
	echo $content;
}

// Produces semantic classes for the each individual post div; Originally from the Sandbox, http://www.plaintxt.org/themes/sandbox/
function fbprofile_post_class( $print = true ) {
	global $post, $fbprofile_post_alt;

	$c = array('hentry', "p$fbprofile_post_alt", $post->post_type, $post->post_status);

	$c[] = 'author-' . get_the_author_login();
	
	foreach ( (array) get_the_category() as $cat )
		$c[] = 'category-' . $cat->category_nicename;

	fbprofile_date_classes(mysql2date('U', $post->post_date), $c);

	if ( ++$fbprofile_post_alt % 2 )
		$c[] = 'alt';
		
	$c = join(' ', apply_filters('post_class', $c));

	return $print ? print($c) : $c;
}
$fbprofile_post_alt = 1;

// Produces date-based classes for the three functions above; Originally from the Sandbox, http://www.plaintxt.org/themes/sandbox/
function fbprofile_date_classes($t, &$c, $p = '') {
	$t = $t + (get_settings('gmt_offset') * 3600);
	$c[] = $p . 'y' . gmdate('Y', $t);
	$c[] = $p . 'm' . gmdate('m', $t);
	$c[] = $p . 'd' . gmdate('d', $t);
	$c[] = $p . 'h' . gmdate('h', $t);
}

// Readies for translation.
load_theme_textdomain('fbprofile');
