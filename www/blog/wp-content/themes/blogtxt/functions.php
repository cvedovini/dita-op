<?php
// Produces links for every page just below the header
function blogtxt_globalnav() {
	echo "\t\t\t<div id=\"globalnav\"><ul id=\"menu\">";
	if ( !is_front_page() ) { ?><li class="page_item_home home-link"><a href="<?php bloginfo('home'); ?>/" title="<?php echo wp_specialchars(get_bloginfo('name'), 1) ?>" rel="home"><?php _e('Home', 'blogtxt') ?></a></li><?php }
	$menu = wp_list_pages('title_li=&sort_column=menu_order&echo=0'); // Params for the page list in header.php
	echo str_replace(array("\r", "\n", "\t"), '', $menu);
	echo "</ul></div>\n";
}

// Produces an hCard for the "admin" user
function blogtxt_admin_hCard() {
	global $wpdb, $admin_info;
	$admin_info = get_userdata(1);
	echo '<span class="vcard"><a class="url fn n" href="' . $admin_info->user_url . '"><span class="given-name">' . $admin_info->first_name . '</span> <span class="family-name">' . $admin_info->last_name . '</span></a></span>';
}

// Produces an hCard for post authors
function blogtxt_author_hCard() {
	global $wpdb, $authordata;
	echo '<span class="entry-author author vcard"><a class="url fn n" href="' . get_author_link(false, $authordata->ID, $authordata->user_nicename) . '" title="View all posts by ' . $authordata->display_name . '">' . get_the_author() . '</a></span>';
}

// Produces semantic classes for the body element; Originally from the Sandbox, http://www.plaintxt.org/themes/sandbox/
function blogtxt_body_class( $print = true ) {
	global $wp_query, $current_user;

	$c = array('wordpress');

	blogtxt_date_classes(time(), $c);

	is_home()       ? $c[] = 'home'       : null;
	is_archive()    ? $c[] = 'archive'    : null;
	is_date()       ? $c[] = 'date'       : null;
	is_search()     ? $c[] = 'search'     : null;
	is_paged()      ? $c[] = 'paged'      : null;
	is_attachment() ? $c[] = 'attachment' : null;
	is_404()        ? $c[] = 'four04'     : null;

	if ( is_single() ) {
		the_post();
		$c[] = 'single';
		if ( isset($wp_query->post->post_date) )
			blogtxt_date_classes(mysql2date('U', $wp_query->post->post_date), $c, 's-');
		foreach ( (array) get_the_category() as $cat )
			$c[] = 's-category-' . $cat->category_nicename;
			$c[] = 's-author-' . get_the_author_login();
		rewind_posts();
	}

	elseif ( is_author() ) {
		$author = $wp_query->get_queried_object();
		$c[] = 'author';
		$c[] = 'author-' . $author->user_nicename;
	}
	
	elseif ( is_category() ) {
		$cat = $wp_query->get_queried_object();
		$c[] = 'category';
		$c[] = 'category-' . $cat->category_nicename;
	}

	elseif ( is_page() ) {
		the_post();
		$c[] = 'page';
		$c[] = 'page-author-' . get_the_author_login();
		rewind_posts();
	}

	if ( $current_user->ID )
		$c[] = 'loggedin';
		
	$c = join(' ', apply_filters('body_class',  $c));

	return $print ? print($c) : $c;
}

// Produces semantic classes for the each individual post div; Originally from the Sandbox, http://www.plaintxt.org/themes/sandbox/
function blogtxt_post_class( $print = true ) {
	global $post, $blogtxt_post_alt;

	$c = array('hentry', "p$blogtxt_post_alt", $post->post_type, $post->post_status);

	$c[] = 'author-' . get_the_author_login();

	if ( is_attachment() )
		$c[] = 'attachment';

	foreach ( (array) get_the_category() as $cat )
		$c[] = 'category-' . $cat->category_nicename;

	blogtxt_date_classes(mysql2date('U', $post->post_date), $c);

	if ( ++$blogtxt_post_alt % 2 )
		$c[] = 'alt';
		
	$c = join(' ', apply_filters('post_class', $c));

	return $print ? print($c) : $c;
}
$blogtxt_post_alt = 1;

// Produces semantic classes for the each individual comment li; Originally from the Sandbox, http://www.plaintxt.org/themes/sandbox/
function blogtxt_comment_class( $print = true ) {
	global $comment, $post, $blogtxt_comment_alt;

	$c = array($comment->comment_type);

	if ( $comment->user_id > 0 ) {
		$user = get_userdata($comment->user_id);

		$c[] = "byuser commentauthor-$user->user_login";

		if ( $comment->user_id === $post->post_author )
			$c[] = 'bypostauthor';
	}

	blogtxt_date_classes(mysql2date('U', $comment->comment_date), $c, 'c-');
	if ( ++$blogtxt_comment_alt % 2 )
		$c[] = 'alt';

	$c[] = "c$blogtxt_comment_alt";

	if ( is_trackback() ) {
		$c[] = 'trackback';
	}

	$c = join(' ', apply_filters('comment_class', $c));

	return $print ? print($c) : $c;
}

// Produces date-based classes for the three functions above; Originally from the Sandbox, http://www.plaintxt.org/themes/sandbox/
function blogtxt_date_classes($t, &$c, $p = '') {
	$t = $t + (get_option('gmt_offset') * 3600);
	$c[] = $p . 'y' . gmdate('Y', $t);
	$c[] = $p . 'm' . gmdate('m', $t);
	$c[] = $p . 'd' . gmdate('d', $t);
	$c[] = $p . 'h' . gmdate('h', $t);
}

// Returns other categories except the current one (redundant); Originally from the Sandbox, http://www.plaintxt.org/themes/sandbox/
function blogtxt_other_cats($glue) {
	$current_cat = single_cat_title('', false);
	$separator = "\n";
	$cats = explode($separator, get_the_category_list($separator));

	foreach ( $cats as $i => $str ) {
		if ( strstr($str, ">$current_cat<") ) {
			unset($cats[$i]);
			break;
		}
	}

	if ( empty($cats) )
		return false;

	return trim(join($glue, $cats));
}

// Returns other tags except the current one (redundant); Originally from the Sandbox, http://www.plaintxt.org/themes/sandbox/
function blogtxt_other_tags($glue) {
	$current_tag = single_tag_title('', '',  false);
	$separator = "\n";
	$tags = explode($separator, get_the_tag_list("", "$separator", ""));

	foreach ( $tags as $i => $str ) {
		if ( strstr($str, ">$current_tag<") ) {
			unset($tags[$i]);
			break;
		}
	}

	if ( empty($tags) )
		return false;

	return trim(join($glue, $tags));
}

// Produces an avatar image with the hCard-compliant photo class
function blogtxt_commenter_link() {
	$commenter = get_comment_author_link();
	if ( ereg( '<a[^>]* class=[^>]+>', $commenter ) ) {
		$commenter = ereg_replace( '(<a[^>]* class=[\'"]?)', '\\1url ' , $commenter );
	} else {
		$commenter = ereg_replace( '(<a )/', '\\1class="url "' , $commenter );
	}
	$email = get_comment_author_email();
	$avatar_size = get_option('blogtxt_avatarsize');
	if ( empty($avatar_size) ) $avatar_size = '16';
	$avatar = str_replace( "class='avatar", "class='photo avatar", get_avatar( "$email", "$avatar_size" ) );
	echo $avatar . ' <span class="fn n">' . $commenter . '</span>';
}

// Function to filter the default gallery shortcode
function blogtxt_gallery($attr) {
	global $post;
	if ( isset($attr['orderby']) ) {
		$attr['orderby'] = sanitize_sql_orderby($attr['orderby']);
		if ( !$attr['orderby'] )
			unset($attr['orderby']);
	}

	extract(shortcode_atts( array(
		'orderby'    => 'menu_order ASC, ID ASC',
		'id'         => $post->ID,
		'itemtag'    => 'dl',
		'icontag'    => 'dt',
		'captiontag' => 'dd',
		'columns'    => 3,
		'size'       => 'thumbnail',
	), $attr ));

	$id           =  intval($id);
	$orderby      =  addslashes($orderby);
	$attachments  =  get_children("post_parent=$id&post_type=attachment&post_mime_type=image&orderby={$orderby}");

	if ( empty($attachments) )
		return null;

	if ( is_feed() ) {
		$output = "\n";
		foreach ( $attachments as $id => $attachment )
			$output .= wp_get_attachment_link( $id, $size, true ) . "\n";
		return $output;
	}

	$listtag     =  tag_escape($listtag);
	$itemtag     =  tag_escape($itemtag);
	$captiontag  =  tag_escape($captiontag);
	$columns     =  intval($columns);
	$itemwidth   =  $columns > 0 ? floor(100/$columns) : 100;

	$output = apply_filters( 'gallery_style', "\n" . '<div class="gallery">', 9 ); // Available filter: gallery_style

	foreach ( $attachments as $id => $attachment ) {
		$img_lnk = get_attachment_link($id);
		$img_src = wp_get_attachment_image_src( $id, $size );
		$img_src = $img_src[0];
		$img_alt = $attachment->post_excerpt;
		if ( $img_alt == null )
			$img_alt = $attachment->post_title;
		$img_rel = apply_filters( 'gallery_img_rel', 'attachment' ); // Available filter: gallery_img_rel
		$img_class = apply_filters( 'gallery_img_class', 'gallery-image' ); // Available filter: gallery_img_class

		$output  .=  "\n\t" . '<' . $itemtag . ' class="gallery-item gallery-columns-' . $columns .'">';
		$output  .=  "\n\t\t" . '<' . $icontag . ' class="gallery-icon"><a href="' . $img_lnk . '" title="' . $img_alt . '" rel="' . $img_rel . '"><img src="' . $img_src . '" alt="' . $img_alt . '" class="' . $img_class . ' attachment-' . $size . '" /></a></' . $icontag . '>';

		if ( $captiontag && trim($attachment->post_excerpt) ) {
			$output .= "\n\t\t" . '<' . $captiontag . ' class="gallery-caption">' . $attachment->post_excerpt . '</' . $captiontag . '>';
		}

		$output .= "\n\t" . '</' . $itemtag . '>';
		if ( $columns > 0 && ++$i % $columns == 0 )
			$output .= "\n</div>\n" . '<div class="gallery">';
	}
	$output .= "\n</div>\n";

	return $output;
}

// Loads a blog.txt-style Search widget
function widget_blogtxt_search($args) {
	extract($args);
	$options = get_option('widget_blogtxt_search');
	$title = empty($options['title']) ? __( 'Blog Search', 'blogtxt' ) : $options['title'];
	$button = empty($options['button']) ? __( 'Find', 'blogtxt' ) : $options['button'];
?>
		<?php echo $before_widget ?>
				<?php echo $before_title ?><label for="s"><?php echo $title ?></label><?php echo $after_title ?>
			<form id="searchform" method="get" action="<?php bloginfo('home') ?>">
				<div>
					<input id="s" name="s" class="text-input" type="text" value="<?php the_search_query() ?>" size="10" tabindex="1" accesskey="S" />
					<input id="searchsubmit" name="searchsubmit" class="submit-button" type="submit" value="<?php echo $button ?>" tabindex="2" />
				</div>
			</form>
		<?php echo $after_widget ?>
<?php
}

// Widget: Search; element controls for customizing text within Widget plugin
function widget_blogtxt_search_control() {
	$options = $newoptions = get_option('widget_blogtxt_search');
	if ( $_POST['search-submit'] ) {
		$newoptions['title'] = strip_tags( stripslashes( $_POST['search-title'] ) );
		$newoptions['button'] = strip_tags( stripslashes( $_POST['search-button'] ) );
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option( 'widget_blogtxt_search', $options );
	}
	$title = attribute_escape( $options['title'] );
	$button = attribute_escape( $options['button'] );
?>
			<p><label for="search-title"><?php _e( 'Title:', 'blogtxt' ) ?> <input class="widefat" id="search-title" name="search-title" type="text" value="<?php echo $title; ?>" /></label></p>
			<p><label for="search-button"><?php _e( 'Button Text:', 'blogtxt' ) ?> <input class="widefat" id="search-button" name="search-button" type="text" value="<?php echo $button; ?>" /></label></p>
			<input type="hidden" id="search-submit" name="search-submit" value="1" />
<?php
}

// Loads a blog.txt-style Meta widget
function widget_blogtxt_meta($args) {
	extract($args);
	$options = get_option('widget_meta');
	$title = empty($options['title']) ? __('Meta', 'blogtxt') : $options['title'];
?>
		<?php echo $before_widget; ?>
			<?php echo $before_title . $title . $after_title; ?>
			<ul>
				<li id="copyright">&copy; <?php echo( date('Y') ); ?> <?php blogtxt_admin_hCard(); ?></li>
				<li id="generator-link"><?php _e('Powered by <a href="http://wordpress.org/" title="WordPress">WordPress</a>', 'blogtxt') ?></li>
				<li id="web-standards"><?php printf(__('Compliant <a href="http://validator.w3.org/check/referer" title="Valid XHTML">XHTML</a> &amp; <a href="http://jigsaw.w3.org/css-validator/validator?profile=css2&amp;warning=2&amp;uri=%s" title="Valid CSS">CSS</a>', 'blogtxt'), get_bloginfo('stylesheet_url') ); ?></li>
				<?php wp_register() ?>

				<li><?php wp_loginout() ?></li>
				<?php wp_meta() ?>
			</ul>
		<?php echo $after_widget; ?>
<?php
}

function widget_blogtxt_homelink($args) {
	extract($args);
	$options = get_option('widget_blogtxt_homelink');
	$title = empty($options['title']) ? __( 'Home', 'blogtxt' ) : $options['title'];
	if ( !is_front_page() || is_paged() ) {
?>
			<?php echo $before_widget; ?>
				<?php echo $before_title; ?><a href="<?php bloginfo('home'); ?>/" title="<?php echo wp_specialchars(get_bloginfo('name'), 1) ?>" rel="home"><?php echo $title; ?></a><?php echo $after_title; ?>
			<?php echo $after_widget; ?>
<?php }
}

// Loads the control functions for the Home Link, allowing control of its text
function widget_blogtxt_homelink_control() {
	$options = $newoptions = get_option('widget_blogtxt_homelink');
	if ( $_POST['homelink-submit'] ) {
		$newoptions['title'] = strip_tags( stripslashes( $_POST['homelink-title'] ) );
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option( 'widget_blogtxt_homelink', $options );
	}
	$title = attribute_escape( $options['title'] );
?>
			<p><?php _e('Adds a link to the home page on every page <em>except</em> the home.', 'blogtxt'); ?></p>
			<p><label for="homelink-title"><?php _e( 'Title:', 'blogtxt' ) ?> <input class="widefat" id="homelink-title" name="homelink-title" type="text" value="<?php echo $title; ?>" /></label></p>
			<input type="hidden" id="homelink-submit" name="homelink-submit" value="1" />
<?php
}

// Loads blog.txt-style RSS Links (separate from Meta) widget
function widget_blogtxt_rsslinks($args) {
	extract($args);
	$options = get_option('widget_blogtxt_rsslinks');
	$title = empty($options['title']) ? __( 'RSS Links', 'blogtxt' ) : $options['title'];
?>
		<?php echo $before_widget; ?>
			<?php echo $before_title . $title . $after_title; ?>
			<ul>
				<li><a href="<?php bloginfo('rss2_url') ?>" title="<?php echo wp_specialchars( get_bloginfo('name'), 1 ) ?> <?php _e( 'Posts RSS feed', 'blogtxt' ); ?>" rel="alternate" type="application/rss+xml"><?php _e( 'All posts', 'blogtxt' ) ?></a></li>
				<li><a href="<?php bloginfo('comments_rss2_url') ?>" title="<?php echo wp_specialchars(bloginfo('name'), 1) ?> <?php _e( 'Comments RSS feed', 'blogtxt' ); ?>" rel="alternate" type="application/rss+xml"><?php _e( 'All comments', 'blogtxt' ) ?></a></li>
			</ul>
		<?php echo $after_widget; ?>
<?php
}

// Loads the control functions for the RSS Links, allowing control of its text
function widget_blogtxt_rsslinks_control() {
	$options = $newoptions = get_option('widget_blogtxt_rsslinks');
	if ( $_POST['rsslinks-submit'] ) {
		$newoptions['title'] = strip_tags( stripslashes( $_POST['rsslinks-title'] ) );
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option( 'widget_blogtxt_rsslinks', $options );
	}
	$title = attribute_escape( $options['title'] );
?>
			<p><label for="rsslinks-title"><?php _e( 'Title:', 'blogtxt' ) ?> <input class="widefat" id="rsslinks-title" name="rsslinks-title" type="text" value="<?php echo $title; ?>" /></label></p>
			<input type="hidden" id="rsslinks-submit" name="rsslinks-submit" value="1" />
<?php
}

// Loads a recent comments widget just like the default blog.txt one
function widget_blogtxt_recent_comments($args) {
	global $wpdb, $comments, $comment;
	extract($args);
	$options = get_option('widget_blogtxt_recent_comments');
	$title = empty($options['title']) ? __('Recent Comments', 'blogtxt') : $options['title'];
	$count = empty($options['count']) ? __('5', 'blogtxt') : $options['count'];
	$comments = $wpdb->get_results("SELECT comment_author, comment_author_url, comment_ID, comment_post_ID, SUBSTRING(comment_content,1,65) AS comment_excerpt FROM $wpdb->comments LEFT OUTER JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID = $wpdb->posts.ID) WHERE comment_approved = '1' AND comment_type = '' AND post_password = '' ORDER BY comment_date_gmt DESC LIMIT $count");
?>
		<?php echo $before_widget; ?>
			<?php echo $before_title ?><?php echo $title ?><?php echo $after_title ?>
				<ul id="recentcomments"><?php
				if ( $comments ) : foreach ($comments as $comment) :
				echo  '<li class="recentcomments">' . sprintf(__('<span class="comment-author vcard">%1$s</span> <span class="comment-entry-title">on <cite title="%2$s">%2$s</cite></span> <blockquote class="comment-summary" cite="%3$s" title="Comment on %2$s">%4$s &hellip;</blockquote>'),
					'<a href="'. get_permalink($comment->comment_post_ID) . '#comment-' . $comment->comment_ID . '" title="' . $comment->comment_author . ' on ' . get_the_title($comment->comment_post_ID) . '"><span class="fn n">' . $comment->comment_author . '</span></a>',
					get_the_title($comment->comment_post_ID),
					get_permalink($comment->comment_post_ID),
					strip_tags($comment->comment_excerpt) ) . '</li>';
				endforeach; endif;?></ul>
		<?php echo $after_widget; ?>
<?php
}

// Allows control over the text for the blog.txt recent comments widget
function widget_blogtxt_recent_comments_control() {
	$options = $newoptions = get_option('widget_blogtxt_recent_comments');
	if ( $_POST['rc-submit'] ) {
		$newoptions['title'] = strip_tags( stripslashes( $_POST['rc-title'] ) );
		$newoptions['count'] = strip_tags( stripslashes( $_POST['rc-count'] ) );
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option( 'widget_blogtxt_recent_comments', $options );
	}
	$rc_title = attribute_escape( $options['title'] );
	$rc_count = attribute_escape( $options['count'] );
?>
			<p><label for="rc-title"><?php _e( 'Title:', 'blogtxt' ) ?> <input class="widefat" id="rc-title" name="rc-title" type="text" value="<?php echo $rc_title; ?>" /></label></p>
			<p>
				<label for="rc-count"><?php _e('Number of comments to show:', 'blogtxt'); ?> <input style="width:25px;text-align:center;" id="rc-count" name="rc-count" type="text" value="<?php echo $rc_count; ?>" /></label>
				<br />
				<small><?php _e('(at most 15)'); ?></small>
			</p>
			<input type="hidden" id="rc-submit" name="rc-submit" value="1" />
<?php
}

// Loads, checks that Widgets are loaded and working
function blogtxt_widgets_init() {
	if ( !function_exists('register_sidebars') )
		return;

	$p = array(
		'before_title' => "<h3 class='widgettitle'>",
		'after_title' => "</h3>\n",
	);

	register_sidebars(2, $p);

	// Finished intializing Widgets plugin, now let's load the blog.txt default widgets; first, blog.txt search widget
	$widget_ops = array(
		'classname'    =>  'widget_search',
		'description'  =>  __( "A search form for your blog (blog.txt)", "blogtxt" )
	);
	wp_register_sidebar_widget( 'search', __( 'Search', 'blogtxt' ), 'widget_blogtxt_search', $widget_ops );
	unregister_widget_control('search');
	wp_register_widget_control( 'search', __( 'Search', 'blogtxt' ), 'widget_blogtxt_search_control' );

	// blog.txt Meta widget
	$widget_ops = array(
		'classname'    =>  'widget_meta',
		'description'  =>  __( "Log in/out and administration links (blog.txt)", "blogtxt" )
	);
	wp_register_sidebar_widget( 'meta', __( 'Meta', 'blogtxt' ), 'widget_blogtxt_meta', $widget_ops );
	unregister_widget_control('meta');
	wp_register_widget_control( 'meta', __('Meta'), 'wp_widget_meta_control' );

	//blog.txt Home Link widget
	$widget_ops = array(
		'classname'    =>  'widget_home_link',
		'description'  =>  __( "Link to the front page when elsewhere (blog.txt)", "blogtxt" )
	);
	wp_register_sidebar_widget( 'home_link', __( 'Home Link', 'blogtxt' ), 'widget_blogtxt_homelink', $widget_ops );
	wp_register_widget_control( 'home_link', __( 'Home Link', 'blogtxt' ), 'widget_blogtxt_homelink_control' );

	//blog.txt Recent Comments widget
	$widget_ops = array(
		'classname'    =>  'widget_blogtxt_recent_comments',
		'description'  =>  __( "Semantic recent comments (blog.txt)", "blogtxt" )
	);
	wp_register_sidebar_widget( 'blogtxt-recent-comments', __( 'Recent Comments', 'blogtxt' ), 'widget_blogtxt_recent_comments', $widget_ops );
	wp_register_widget_control( 'blogtxt-recent-comments', __( 'Recent Comments', 'blogtxt' ), 'widget_blogtxt_recent_comments_control' );

	//blog.txt RSS Links widget
	$widget_ops = array(
		'classname'    =>  'widget_rss_links',
		'description'  =>  __( "RSS links for both posts and comments (blog.txt)", "blogtxt" )
	);
	wp_register_sidebar_widget( 'rss_links', __( 'RSS Links', 'blogtxt' ), 'widget_blogtxt_rsslinks', $widget_ops );
	wp_register_widget_control( 'rss_links', __( 'RSS Links', 'blogtxt' ), 'widget_blogtxt_rsslinks_control' );
}

// Loads the admin menu; sets default settings for each
function blogtxt_add_admin() {
	if ( $_GET['page'] == basename(__FILE__) ) {
		if ( 'save' == $_REQUEST['action'] ) {
			check_admin_referer('blogtxt_save_options');
			update_option( 'blogtxt_authorlink', strip_tags( stripslashes( $_REQUEST['bt_authorlink'] ) ) );
			update_option( 'blogtxt_basefontfamily', strip_tags( stripslashes( $_REQUEST['bt_basefontfamily'] ) ) );
			update_option( 'blogtxt_basefontsize', strip_tags( stripslashes( $_REQUEST['bt_basefontsize'] ) ) );
			update_option( 'blogtxt_blogtitlefontfamily', strip_tags( stripslashes( $_REQUEST['bt_blogtitlefontfamily'] ) ) );
			update_option( 'blogtxt_headingfontfamily', strip_tags( stripslashes( $_REQUEST['bt_headingfontfamily'] ) ) );
			update_option( 'blogtxt_layoutalignment', strip_tags( stripslashes( $_REQUEST['bt_layoutalignment'] ) ) );
			update_option( 'blogtxt_layouttype', strip_tags( stripslashes( $_REQUEST['bt_layouttype'] ) ) );
			update_option( 'blogtxt_layoutwidth', strip_tags( stripslashes( $_REQUEST['bt_layoutwidth'] ) ) );
			update_option( 'blogtxt_miscfontfamily', strip_tags( stripslashes( $_REQUEST['bt_miscfontfamily'] ) ) );
			update_option( 'blogtxt_posttextalignment', strip_tags( stripslashes( $_REQUEST['bt_posttextalignment'] ) ) );
			update_option( 'blogtxt_avatarsize', strip_tags( stripslashes( $_REQUEST['bt_avatarsize'] ) ) );
			header("Location: themes.php?page=functions.php&saved=true");
			die;
		} elseif ( 'reset' == $_REQUEST['action'] ) {
			check_admin_referer('blogtxt_reset_options');
			delete_option('blogtxt_authorlink');
			delete_option('blogtxt_basefontfamily');
			delete_option('blogtxt_basefontsize');
			delete_option('blogtxt_blogtitlefontfamily');
			delete_option('blogtxt_headingfontfamily');
			delete_option('blogtxt_layoutalignment');
			delete_option('blogtxt_layouttype');
			delete_option('blogtxt_layoutwidth');
			delete_option('blogtxt_miscfontfamily');
			delete_option('blogtxt_posttextalignment');
			delete_option('blogtxt_avatarsize');
			header("Location: themes.php?page=functions.php&reset=true");
			die;
		}
		add_action('admin_head', 'blogtxt_admin_head');
	}
	add_theme_page( __( 'Blog.txt Theme Options', 'blogtxt' ), __( 'Theme Options', 'blogtxt' ), 'edit_themes', basename(__FILE__), 'blogtxt_admin' );
}

function blogtxt_donate() { 
	$form = '<form id="paypal" action="https://www.paypal.com/cgi-bin/webscr" method="post">
		<div id="donate">
			<input type="hidden" name="cmd" value="_s-xclick" />
			<input type="image" name="submit" src="https://www.paypal.com/en_US/i/btn/x-click-butcc-donate.gif" alt="Donate with PayPal - it\'s fast, free and secure!" />
			<img src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" alt="Donate with PayPal" />
			<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----" />
		</div>
	</form>' . "\n\t";
	echo $form;
}

function blogtxt_admin_head() {
// Additional CSS styles for the theme options menu
?>
<style type="text/css" media="screen,projection">
/*<![CDATA[*/
	p.info span{font-weight:bold;}
	label.arial,label.courier,label.georgia,label.lucida-console,label.lucida-unicode,label.tahoma,label.times,label.trebuchet,label.verdana{font-size:1.2em;line-height:175%;}
	.arial{font-family:arial,helvetica,sans-serif;}
	.courier{font-family:'courier new',courier,monospace;}
	.georgia{font-family:georgia,times,serif;}
	.lucida-console{font-family:'lucida console',monaco,monospace;}
	.lucida-unicode{font-family:'lucida sans unicode','lucida grande',sans-serif;}
	.tahoma{font-family:tahoma,geneva,sans-serif;}
	.times{font-family:'times new roman',times,serif;}
	.trebuchet{font-family:'trebuchet ms',helvetica,sans-serif;}
	.verdana{font-family:verdana,geneva,sans-serif;}
	form#paypal{float:right;margin:1em 0 0.5em 1em;}
/*]]>*/
</style>
<?php
}

function blogtxt_admin() { // Theme options menu 
	if ( $_REQUEST['saved'] ) { ?><div id="message1" class="updated fade"><p><?php printf(__('Blog.txt theme options saved. <a href="%s">View site.</a>', 'blogtxt'), get_bloginfo('home') . '/'); ?></p></div><?php }
	if ( $_REQUEST['reset'] ) { ?><div id="message2" class="updated fade"><p><?php _e('Blog.txt theme options reset.', 'blogtxt'); ?></p></div><?php } ?>

<div class="wrap" id="blogtxt-options">
	<h2><?php _e('Blog.txt Theme Options', 'blogtxt'); ?></h2>
	<?php printf( __('%1$s<p>Thanks for selecting the <a href="http://www.plaintxt.org/themes/blogtxt/" title="blog.txt theme for WordPress">blog.txt</a> theme by <span class="vcard"><a class="url fn n" href="http://scottwallick.com/" title="scottwallick.com" rel="me designer"><span class="given-name">Scott</span> <span class="additional-name">Allan</span> <span class="family-name">Wallick</span></a></span>. Please read the included <a href="%2$s" title="Open the readme.html" rel="enclosure" id="readme">documentation</a> for more information about the blog.txt and its advanced features. <strong>If you find this theme useful, please consider <label for="paypal">donating</label>.</strong> You must click on <i><u>S</u>ave Options</i> to save any changes. You can also discard your changes and reload the default settings by clicking on <i><u>R</u>eset</i>.</p>', 'blogtxt'), blogtxt_donate(), get_template_directory_uri() . '/readme.html' ); ?>

	<form action="<?php echo wp_specialchars( $_SERVER['REQUEST_URI'] ) ?>" method="post">
		<?php wp_nonce_field('blogtxt_save_options'); echo "\n"; ?>
		<h3><?php _e('Typography', 'blogtxt'); ?></h3>
		<table class="form-table" summary="Blog.txt typography options">
			<tr valign="top">
				<th scope="row"><label for="bt_basefontsize"><?php _e('Base font size', 'blogtxt'); ?></label></th> 
				<td>
					<input id="bt_basefontsize" name="bt_basefontsize" type="text" class="text" value="<?php if ( get_option('blogtxt_basefontsize') == "" ) { echo "80%"; } else { echo attribute_escape( get_option('blogtxt_basefontsize') ); } ?>" tabindex="1" size="10" />
					<p class="info"><?php _e('The base font size globally affects the size of text throughout your blog. This can be in any unit (e.g., px, pt, em), but I suggest using a percentage (%). Default is <span>80%</span>.', 'blogtxt'); ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Base font family', 'blogtxt'); ?></th> 
				<td>
					<input id="bt_basefontArial" name="bt_basefontfamily" type="radio" class="radio" value="arial,helvetica,sans-serif" <?php if ( get_option('blogtxt_basefontfamily') == "arial,helvetica,sans-serif" ) { echo 'checked="checked"'; } ?> tabindex="2" /> <label for="bt_basefontArial" class="arial">Arial</label><br />
					<input id="bt_basefontCourier" name="bt_basefontfamily" type="radio" class="radio" value="'courier new',courier,monospace" <?php if ( get_option('blogtxt_basefontfamily') == "'courier new',courier,monospace" ) { echo 'checked="checked"'; } ?> tabindex="3" /> <label for="bt_basefontCourier" class="courier">Courier</label><br />
					<input id="bt_basefontGeorgia" name="bt_basefontfamily" type="radio" class="radio" value="georgia,times,serif" <?php if ( ( get_option('blogtxt_basefontfamily') == "") || ( get_option('blogtxt_basefontfamily') == "georgia,times,serif") ) { echo 'checked="checked"'; } ?> tabindex="4" /> <label for="bt_basefontGeorgia" class="georgia">Georgia</label><br />
					<input id="bt_basefontLucidaConsole" name="bt_basefontfamily" type="radio" class="radio" value="'lucida console',monaco,monospace" <?php if ( get_option('blogtxt_basefontfamily') == "'lucida console',monaco,monospace" ) { echo 'checked="checked"'; } ?> tabindex="5" /> <label for="bt_basefontLucidaConsole" class="lucida-console">Lucida Console</label><br />
					<input id="bt_basefontLucidaUnicode" name="bt_basefontfamily" type="radio" class="radio" value="'lucida sans unicode','lucida grande',sans-serif" <?php if ( get_option('blogtxt_basefontfamily') == "'lucida sans unicode','lucida grande',sans-serif" ) { echo 'checked="checked"'; } ?> tabindex="6" /> <label for="bt_basefontLucidaUnicode" class="lucida-unicode">Lucida Sans Unicode</label><br />
					<input id="bt_basefontTahoma" name="bt_basefontfamily" type="radio" class="radio" value="tahoma,geneva,sans-serif" <?php if ( get_option('blogtxt_basefontfamily') == "tahoma,geneva,sans-serif" ) { echo 'checked="checked"'; } ?> tabindex="7" /> <label for="bt_basefontTahoma" class="tahoma">Tahoma</label><br />
					<input id="bt_basefontTimes" name="bt_basefontfamily" type="radio" class="radio" value="'times new roman',times,serif" <?php if ( get_option('blogtxt_basefontfamily') == "'times new roman',times,serif" ) { echo 'checked="checked"'; } ?>tabindex="8" /> <label for="bt_basefontTimes" class="times">Times</label><br />
					<input id="bt_basefontTrebuchetMS" name="bt_basefontfamily" type="radio" class="radio" value="'trebuchet ms',helvetica,sans-serif" <?php if ( get_option('blogtxt_basefontfamily') == "'trebuchet ms',helvetica,sans-serif" ) { echo 'checked="checked"'; } ?> tabindex="9" /> <label for="bt_basefontTrebuchetMS" class="trebuchet">Trebuchet MS</label><br />
					<input id="bt_basefontVerdana" name="bt_basefontfamily" type="radio" class="radio" value="verdana,geneva,sans-serif" <?php if ( get_option('blogtxt_basefontfamily') == "verdana,geneva,sans-serif" ) { echo 'checked="checked"'; } ?> tabindex="10" /> <label for="bt_basefontVerdana" class="verdana">Verdana</label>
					<p class="info"><?php printf(__('The base font family sets the font for content area. The selection is limited to %1$s fonts, as they will display correctly. Default is <span class="georgia">Georgia</span>.', 'blogtxt'), '<cite><a href="http://en.wikipedia.org/wiki/Web_safe_fonts" title="Web safe fonts - Wikipedia">web safe</a></cite>'); ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Heading font family', 'blogtxt'); ?></th> 
				<td>
					<input id="bt_headingfontArial" name="bt_headingfontfamily" type="radio" class="radio" value="arial,helvetica,sans-serif" <?php if ( ( get_option('blogtxt_headingfontfamily') == "") || ( get_option('blogtxt_headingfontfamily') == "arial,helvetica,sans-serif") ) { echo 'checked="checked"'; } ?> tabindex="11" /> <label for="bt_headingfontArial" class="arial">Arial</label><br />
					<input id="bt_headingfontCourier" name="bt_headingfontfamily" type="radio" class="radio" value="'courier new',courier,monospace" <?php if ( get_option('blogtxt_headingfontfamily') == "'courier new',courier,monospace" ) { echo 'checked="checked"'; } ?> tabindex="12" /> <label for="bt_headingfontCourier" class="courier">Courier</label><br />
					<input id="bt_headingfontGeorgia" name="bt_headingfontfamily" type="radio" class="radio" value="georgia,times,serif" <?php if ( get_option('blogtxt_headingfontfamily') == "georgia,times,serif" ) { echo 'checked="checked"'; } ?> tabindex="13" /> <label for="bt_headingfontGeorgia" class="georgia">Georgia</label><br />
					<input id="bt_headingfontLucidaConsole" name="bt_headingfontfamily" type="radio" class="radio" value="'lucida console',monaco,monospace" <?php if ( get_option('blogtxt_headingfontfamily') == "'lucida console',monaco,monospace" ) { echo 'checked="checked"'; } ?> tabindex="14" /> <label for="bt_headingfontLucidaConsole" class="lucida-console">Lucida Console</label><br />
					<input id="bt_headingfontLucidaUnicode" name="bt_headingfontfamily" type="radio" class="radio" value="'lucida sans unicode','lucida grande',sans-serif" <?php if ( get_option('blogtxt_headingfontfamily') == "'lucida sans unicode','lucida grande',sans-serif" ) { echo 'checked="checked"'; } ?> tabindex="15" /> <label for="bt_headingfontLucidaUnicode" class="lucida-unicode">Lucida Sans Unicode</label><br />
					<input id="bt_headingfontTahoma" name="bt_headingfontfamily" type="radio" class="radio" value="tahoma,geneva,sans-serif" <?php if ( get_option('blogtxt_headingfontfamily') == "tahoma,geneva,sans-serif" ) { echo 'checked="checked"'; } ?> tabindex="16" /> <label for="bt_headingfontTahoma" class="tahoma">Tahoma</label><br />
					<input id="bt_headingfontTimes" name="bt_headingfontfamily" type="radio" class="radio" value="'times new roman',times,serif" <?php if ( get_option('blogtxt_headingfontfamily') == "'times new roman',times,serif" ) { echo 'checked="checked"'; } ?> tabindex="17" /> <label for="bt_headingfontTimes" class="times">Times</label><br />
					<input id="bt_headingfontTrebuchetMS" name="bt_headingfontfamily" type="radio" class="radio" value="'trebuchet ms',helvetica,sans-serif" <?php if ( get_option('blogtxt_headingfontfamily') == "'trebuchet ms',helvetica,sans-serif" ) { echo 'checked="checked"'; } ?> tabindex="18" /> <label for="bt_headingfontTrebuchetMS" class="trebuchet">Trebuchet MS</label><br />
					<input id="bt_headingfontVerdana" name="bt_headingfontfamily" type="radio" class="radio" value="verdana,geneva,sans-serif" <?php if ( get_option('blogtxt_headingfontfamily') == "verdana,geneva,sans-serif" ) { echo 'checked="checked"'; } ?> tabindex="19" /> <label for="bt_headingfontVerdana" class="verdana">Verdana</label>
					<p class="info"><?php printf(__('The heading font family sets the font for all content headings and blog description. The selection is limited to %1$s fonts, as they will display correctly. Default is <span class="arial">Arial</span>. ', 'blogtxt'), '<cite><a href="http://en.wikipedia.org/wiki/Web_safe_fonts" title="Web safe fonts - Wikipedia">web safe</a></cite>'); ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Blog title font family', 'blogtxt'); ?></th> 
				<td>
					<input id="bt_blogtitlefontArial" name="bt_blogtitlefontfamily" type="radio" class="radio" value="arial,helvetica,sans-serif" <?php if ( get_option('blogtxt_blogtitlefontfamily') == "arial,helvetica,sans-serif" ) { echo 'checked="checked"'; } ?> tabindex="20" /> <label for="bt_blogtitlefontArial" class="arial">Arial</label><br />
					<input id="bt_blogtitlefontCourier" name="bt_blogtitlefontfamily" type="radio" class="radio" value="'courier new',courier,monospace" <?php if ( get_option('blogtxt_blogtitlefontfamily') == "'courier new',courier,monospace" ) { echo 'checked="checked"'; } ?> tabindex="21" /> <label for="bt_blogtitlefontCourier" class="courier">Courier</label><br />
					<input id="bt_blogtitlefontGeorgia" name="bt_blogtitlefontfamily" type="radio" class="radio" value="georgia,times,serif" <?php if ( get_option('blogtxt_blogtitlefontfamily') == "georgia,times,serif" ) { echo 'checked="checked"'; } ?> tabindex="22" /> <label for="bt_blogtitlefontGeorgia" class="georgia">Georgia</label><br />
					<input id="bt_blogtitlefontLucidaConsole" name="bt_blogtitlefontfamily" type="radio" class="radio" value="'lucida console',monaco,monospace" <?php if ( get_option('blogtxt_blogtitlefontfamily') == "'lucida console',monaco,monospace" ) { echo 'checked="checked"'; } ?> tabindex="23" /> <label for="bt_blogtitlefontLucidaConsole" class="lucida-console">Lucida Console</label><br />
					<input id="bt_blogtitlefontLucidaUnicode" name="bt_blogtitlefontfamily" type="radio" class="radio" value="'lucida sans unicode','lucida grande',sans-serif" <?php if ( get_option('blogtxt_blogtitlefontfamily') == "'lucida sans unicode','lucida grande',sans-serif" ) { echo 'checked="checked"'; } ?> tabindex="24" /> <label for="bt_blogtitlefontLucidaUnicode" class="lucida-unicode">Lucida Sans Unicode</label><br />
					<input id="bt_blogtitlefontTahoma" name="bt_blogtitlefontfamily" type="radio" class="radio" value="tahoma,geneva,sans-serif" <?php if ( get_option('blogtxt_blogtitlefontfamily') == "tahoma,geneva,sans-serif" ) { echo 'checked="checked"'; } ?> tabindex="25" /> <label for="bt_blogtitlefontTahoma" class="tahoma">Tahoma</label><br />
					<input id="bt_blogtitlefontTimes" name="bt_blogtitlefontfamily" type="radio" class="radio" value="'times new roman',times,serif" <?php if ( ( get_option('blogtxt_blogtitlefontfamily') == "") || ( get_option('blogtxt_blogtitlefontfamily') == "'times new roman',times,serif") ) { echo 'checked="checked"'; } ?> tabindex="26" /> <label for="bt_blogtitlefontTimes" class="times">Times</label><br />
					<input id="bt_blogtitlefontTrebuchetMS" name="bt_blogtitlefontfamily" type="radio" class="radio" value="'trebuchet ms',helvetica,sans-serif" <?php if ( get_option('blogtxt_blogtitlefontfamily') == "'trebuchet ms',helvetica,sans-serif" ) { echo 'checked="checked"'; } ?> tabindex="27" /> <label for="bt_blogtitlefontTrebuchetMS" class="trebuchet">Trebuchet MS</label><br />
					<input id="bt_blogtitlefontVerdana" name="bt_blogtitlefontfamily" type="radio" class="radio" value="verdana,geneva,sans-serif" <?php if ( get_option('blogtxt_blogtitlefontfamily') == "verdana,geneva,sans-serif" ) { echo 'checked="checked"'; } ?> tabindex="28" /> <label for="bt_blogtitlefontVerdana" class="verdana">Verdana</label>
					<p class="info"><?php printf(__('The blog title font family sets the font for the blog title (and sidebar headings, actually). The selection is limited to %1$s fonts, as they will display correctly. Default is <span class="times">Times</span>. ', 'blogtxt'), '<cite><a href="http://en.wikipedia.org/wiki/Web_safe_fonts" title="Web safe fonts - Wikipedia">web safe</a></cite>'); ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Miscellanea font family', 'blogtxt'); ?></th> 
				<td>
					<input id="bt_miscfontArial" name="bt_miscfontfamily" type="radio" class="radio" value="arial,helvetica,sans-serif" <?php if ( get_option('blogtxt_miscfontfamily') == "arial,helvetica,sans-serif" ) { echo 'checked="checked"'; } ?> tabindex="29" /> <label for="bt_miscfontArial" class="arial">Arial</label><br />
					<input id="bt_miscfontCourier" name="bt_miscfontfamily" type="radio" class="radio" value="'courier new',courier,monospace" <?php if ( get_option('blogtxt_miscfontfamily') == "'courier new',courier,monospace" ) { echo 'checked="checked"'; } ?> tabindex="30" /> <label for="bt_miscfontCourier" class="courier">Courier</label><br />
					<input id="bt_miscfontGeorgia" name="bt_miscfontfamily" type="radio" class="radio" value="georgia,times,serif" <?php if ( get_option('blogtxt_miscfontfamily') == "georgia,times,serif" ) { echo 'checked="checked"'; } ?> tabindex="31" /> <label for="bt_miscfontGeorgia" class="georgia">Georgia</label><br />
					<input id="bt_miscfontLucidaConsole" name="bt_miscfontfamily" type="radio" class="radio" value="'lucida console',monaco,monospace" <?php if ( get_option('blogtxt_miscfontfamily') == "'lucida console',monaco,monospace" ) { echo 'checked="checked"'; } ?> tabindex="32" /> <label for="bt_miscfontLucidaConsole" class="lucida-console">Lucida Console</label><br />
					<input id="bt_miscfontLucidaUnicode" name="bt_miscfontfamily" type="radio" class="radio" value="'lucida sans unicode','lucida grande',sans-serif" <?php if ( get_option('blogtxt_miscfontfamily') == "'lucida sans unicode','lucida grande',sans-serif" ) { echo 'checked="checked"'; } ?> tabindex="33" /> <label for="bt_miscfontLucidaUnicode" class="lucida-unicode">Lucida Sans Unicode</label><br />
					<input id="bt_miscfontTahoma" name="bt_miscfontfamily" type="radio" class="radio" value="tahoma,geneva,sans-serif" <?php if ( get_option('blogtxt_miscfontfamily') == "tahoma,geneva,sans-serif" ) { echo 'checked="checked"'; } ?> tabindex="34" /> <label for="bt_miscfontTahoma" class="tahoma">Tahoma</label><br />
					<input id="bt_miscfontTimes" name="bt_miscfontfamily" type="radio" class="radio" value="'times new roman',times,serif" <?php if ( get_option('blogtxt_miscfontfamily') == "'times new roman',times,serif" ) { echo 'checked="checked"'; } ?> tabindex="35" /> <label for="bt_miscfontTimes" class="times">Times</label><br />
					<input id="bt_miscfontTrebuchetMS" name="bt_miscfontfamily" type="radio" class="radio" value="'trebuchet ms',helvetica,sans-serif" <?php if ( get_option('blogtxt_miscfontfamily') == "'trebuchet ms',helvetica,sans-serif" ) { echo 'checked="checked"'; } ?> tabindex="36" /> <label for="bt_miscfontTrebuchetMS" class="trebuchet">Trebuchet MS</label><br />
					<input id="bt_miscfontVerdana" name="bt_miscfontfamily" type="radio" class="radio" value="verdana,geneva,sans-serif" <?php if ( ( get_option('blogtxt_miscfontfamily') == "") || ( get_option('blogtxt_miscfontfamily') == "verdana,geneva,sans-serif") ) { echo 'checked="checked"'; } ?> tabindex="37" /> <label for="bt_miscfontVerdana" class="verdana">Verdana</label><br />
					<p class="info"><?php printf(__('The miscellanea font family sets the font for the sidebar content, input fields, and post footers. The selection is limited to %1$s fonts, as they will display correctly. Default is <span class="verdana">Verdana</span>. ', 'blogtxt'), '<cite><a href="http://en.wikipedia.org/wiki/Web_safe_fonts" title="Web safe fonts - Wikipedia">web safe</a></cite>'); ?></p>
				</td>
			</tr>
		</table>
		<h3><?php _e('Layout', 'blogtxt'); ?></h3>
		<table class="form-table" summary="Blog.txt layout options">
			<tr valign="top">
				<th scope="row"><label for="bt_layoutwidth"><?php _e('Layout width', 'blogtxt'); ?></label></th> 
				<td>
					<input id="bt_layoutwidth" name="bt_layoutwidth" type="text" class="text" value="<?php if ( get_option('blogtxt_layoutwidth') == "" ) { echo "60em"; } else { echo attribute_escape( get_option('blogtxt_layoutwidth') ); } ?>" tabindex="38" size="10" />
					<p class="info"><?php _e('The layout width determines the normal width of the entire layout. This can be in any unit (e.g., px, pt, %). Default is <span>60em</span>.', 'blogtxt'); ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="bt_layouttype"><?php _e('Layout type', 'blogtxt'); ?></label></th> 
				<td>
					<select id="bt_layouttype" name="bt_layouttype" tabindex="39" class="dropdown">
						<option value="2c-l.css" <?php if ( get_option('blogtxt_layouttype') == "2c-l.css" ) { echo 'selected="selected"'; } ?>><?php _e('Two-column (left)', 'blogtxt'); ?> </option>
						<option value="2c-r.css" <?php if ( ( get_option('blogtxt_layouttype') == "") || ( get_option('blogtxt_layouttype') == "2c-r.css") ) { echo 'selected="selected"'; } ?>><?php _e('Two-column (right)', 'blogtxt'); ?> </option>
						<option value="3c-b.css" <?php if ( get_option('blogtxt_layouttype') == "3c-b.css" ) { echo 'selected="selected"'; } ?>><?php _e('Three-column (both)', 'blogtxt'); ?> </option>
					</select>
					<p class="info"><?php _e('Choose one of the options for the type of layout: two column with a left or right sidebars, or three-column with left and right sidebars. Default is <span>Two-column (right)</span>.', 'blogtxt'); ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="bt_layoutalignment"><?php _e('Layout alignment', 'blogtxt'); ?></label></th> 
				<td>
					<select id="bt_layoutalignment" name="bt_layoutalignment" tabindex="40" class="dropdown">
						<option value="center" <?php if ( get_option('blogtxt_layoutalignment') == "center" ) { echo 'selected="selected"'; } ?>><?php _e('Centered', 'blogtxt'); ?> </option>
						<option value="left" <?php if ( ( get_option('blogtxt_layoutalignment') == "") || ( get_option('blogtxt_layoutalignment') == "left") ) { echo 'selected="selected"'; } ?>><?php _e('Left', 'blogtxt'); ?> </option>
						<option value="right" <?php if ( get_option('blogtxt_layoutalignment') == "right" ) { echo 'selected="selected"'; } ?>><?php _e('Right', 'blogtxt'); ?> </option>
					</select>
					<p class="info"><?php _e('Choose one of the options for the alignment of the entire layout. Default is <span>left</span>.', 'blogtxt'); ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="bt_posttextalignment"><?php _e('Post text alignment', 'blogtxt'); ?></label></th> 
				<td>
					<select id="bt_posttextalignment" name="bt_posttextalignment" tabindex="41" class="dropdown">
						<option value="center" <?php if ( get_option('blogtxt_posttextalignment') == "center" ) { echo 'selected="selected"'; } ?>><?php _e('Centered', 'blogtxt'); ?> </option>
						<option value="justify" <?php if ( get_option('blogtxt_posttextalignment') == "justify" ) { echo 'selected="selected"'; } ?>><?php _e('Justified', 'blogtxt'); ?> </option>
						<option value="left" <?php if ( ( get_option('blogtxt_posttextalignment') == "") || ( get_option('blogtxt_posttextalignment') == "left") ) { echo 'selected="selected"'; } ?>><?php _e('Left', 'blogtxt'); ?> </option>
						<option value="right" <?php if ( get_option('blogtxt_posttextalignment') == "right" ) { echo 'selected="selected"'; } ?>><?php _e('Right', 'blogtxt'); ?> </option>
					</select>
					<p class="info"><?php _e('Choose one of the options for the alignment of the post entry text. Default is <span>left</span>.', 'blogtxt'); ?></p>
				</td>
			</tr>
		</table>
		<h3><?php _e('Content', 'blogtxt'); ?></h3>
		<table class="form-table" summary="Blog.txt content options">
			<tr valign="top">
				<th scope="row"><label for="bt_authorlink"><?php _e('Author link', 'blogtxt'); ?></label></th> 
				<td>
					<select id="bt_authorlink" name="bt_authorlink" tabindex="42" class="dropdown">
						<option value="displayed" <?php if ( ( get_option('blogtxt_authorlink') == "") || ( get_option('blogtxt_authorlink') == "displayed") ) { echo 'selected="selected"'; } ?>><?php _e('Displayed', 'blogtxt'); ?> </option>
						<option value="hidden" <?php if ( get_option('blogtxt_authorlink') == "hidden" ) { echo 'selected="selected"'; } ?>><?php _e('Hidden', 'blogtxt'); ?> </option>
					</select>
					<p class="info"><?php _e('The author\'s name and link to his/her corresponding archives page can be displayed or hidden. The "hidden" setting disables the link in an author\'s name in single post footers (and in pages &mdash; see the <a href="#readme">documentation</a> for info). Default is <span>displayed</span>.', 'blogtxt'); ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="bt_avatarsize"><?php _e('Avatar size', 'blogtxt'); ?></label></th> 
				<td>
					<input id="bt_avatarsize" name="bt_avatarsize" type="text" class="text" value="<?php if ( get_option('blogtxt_avatarsize') == "" ) { echo "16"; } else { echo attribute_escape( get_option('blogtxt_avatarsize') ); } ?>" size="6" />
					<p class="info"><?php _e('Sets the avatar size in pixels, if avatars are enabled. Default is <span>16</span>.', 'blogtxt'); ?></p>
				</td>
			</tr>
		</table>
		<p class="submit">
			<input name="save" type="submit" value="<?php _e('Save Options', 'blogtxt'); ?>" tabindex="43" accesskey="S" />  
			<input name="action" type="hidden" value="save" />
			<input name="page_options" type="hidden" value="bt_authorlink,bt_basefontfamily,bt_basefontsize,bt_blogtitlefontfamily,bt_headingfontfamily,bt_layoutalignment,bt_layouttype,bt_layoutwidth,bt_miscfontfamily,bt_posttextalignment,bt_avatarsize" />
		</p>
	</form>
	<h3 id="reset"><?php _e('Reset Options', 'blogtxt'); ?></h3>
	<p><?php _e('Resetting deletes all stored blog.txt options from your database. After resetting, default options are loaded but are not stored until you click <i>Save Options</i>. A reset does not affect the actual theme files in any way. If you are uninstalling blog.txt, please reset before removing the theme files to clear your databse.', 'blogtxt'); ?></p>
	<form action="<?php echo wp_specialchars( $_SERVER['REQUEST_URI'] ) ?>" method="post">
		<?php wp_nonce_field('blogtxt_reset_options'); echo "\n"; ?>
		<p class="submit">
			<input name="reset" type="submit" value="<?php _e('Reset Options', 'blogtxt'); ?>" onclick="return confirm('<?php _e('Click OK to reset. Any changes to these theme options will be lost!', 'blogtxt'); ?>');" tabindex="44" accesskey="R" />
			<input name="action" type="hidden" value="reset" />
			<input name="page_options" type="hidden" value="bt_authorlink,bt_basefontfamily,bt_basefontsize,bt_blogtitlefontfamily,bt_headingfontfamily,bt_layoutalignment,bt_layouttype,bt_layoutwidth,bt_miscfontfamily,bt_posttextalignment,bt_avatarsize" />
		</p>
	</form>
</div>
<?php
}

// Loads settings for the theme options to use
function blogtxt_wp_head() {
	function blogtxt_author_link() { // Option to show the author link, or not
		global $wpdb, $authordata;
		if ( get_option('blogtxt_authorlink') == "" ) {
			if ( is_single() || is_page() ) {
				return '<span class="entry-author author vcard"><a class="url fn" href="' . get_author_link(false, $authordata->ID, $authordata->user_nicename) . '" title="View all posts by ' . $authordata->display_name . '">' . get_the_author() . '</a></span>';
			} else {
				echo '<span class="meta-sep">&dagger;</span> <span class="entry-author author vcard"><a class="url fn" href="' . get_author_link(false, $authordata->ID, $authordata->user_nicename) . '" title="View all posts by ' . $authordata->display_name . '">' . get_the_author() . '</a></span>';
			}
		} elseif ( get_option('blogtxt_authorlink') =="displayed" ) {
			if ( is_single() || is_page() ) {
				return '<span class="entry-author author vcard"><a class="url fn" href="' . get_author_link(false, $authordata->ID, $authordata->user_nicename) . '" title="View all posts by ' . $authordata->display_name . '">' . get_the_author() . '</a></span>';
			} else {
				echo '<span class="meta-sep">&dagger;</span> <span class="entry-author author vcard"><a class="url fn" href="' . get_author_link(false, $authordata->ID, $authordata->user_nicename) . '" title="View all posts by ' . $authordata->display_name . '">' . get_the_author() . '</a></span>';
			}
		} elseif ( get_option('blogtxt_authorlink') =="hidden" ) {
			if ( is_single() || is_page() ) {
				return '<span class="entry-author author vcard"><span class="fn n">' . get_the_author() . '</span></span>';
			} else {
				echo '';
			}
		};
	}
	if ( get_option('blogtxt_basefontsize') == "" ) {
		$basefontsize = '80%';
	} else {
		$basefontsize = attribute_escape( stripslashes( get_option('blogtxt_basefontsize') ) ); 
	};
	if ( get_option('blogtxt_basefontfamily') == "" ) {
		$basefontfamily = 'georgia,times,serif';
	} else {
		$basefontfamily = wp_specialchars( stripslashes( get_option('blogtxt_basefontfamily') ) ); 
	};
	if ( get_option('blogtxt_headingfontfamily') == "" ) {
		$headingfontfamily = 'arial,helvetica,sans-serif';
	} else {
		$headingfontfamily = wp_specialchars( stripslashes( get_option('blogtxt_headingfontfamily') ) ); 
	};
	if ( get_option('blogtxt_blogtitlefontfamily') == "" ) {
		$blogtitlefontfamily = '\'times new roman\',times,serif';
	} else {
		$blogtitlefontfamily = wp_specialchars( stripslashes( get_option('blogtxt_blogtitlefontfamily') ) ); 
	};
	if ( get_option('blogtxt_miscfontfamily') == "" ) {
		$miscfontfamily = 'verdana,geneva,sans-serif';
	} else {
		$miscfontfamily = wp_specialchars( stripslashes( get_option('blogtxt_miscfontfamily') ) ); 
	};
	if ( get_option('blogtxt_layoutwidth') == "" ) {
		$layoutwidth = '60em';
	} else {
		$layoutwidth = attribute_escape( stripslashes( get_option('blogtxt_layoutwidth') ) );
	};
	if ( get_option('blogtxt_layouttype') == "" ) {
		$layouttype = '2c-r.css';
	} else {
		$layouttype = attribute_escape( stripslashes( get_option('blogtxt_layouttype') ) );
	};
	if ( get_option('blogtxt_layoutalignment') == "" ) {
		$layoutalignment = 'body div#wrapper{margin:5em 0 0 7em;}';
		} elseif ( get_option('blogtxt_layoutalignment') =="center" ) {
			$layoutalignment = 'body div#wrapper{margin:5em auto 0 auto;padding:0 1em;}';
		} elseif ( get_option('blogtxt_layoutalignment') =="left" ) {
			$layoutalignment = 'body div#wrapper{margin:5em 0 0 7em;}';
		} elseif ( get_option('blogtxt_layoutalignment') =="right" ) {
			$layoutalignment = 'body div#wrapper{margin:5em 3em 0 auto;}';
	};
	if ( get_option('blogtxt_posttextalignment') == "" ) {
		$posttextalignment = 'left';
	} else {
		$posttextalignment = attribute_escape( stripslashes( get_option('blogtxt_posttextalignment') ) ); 
	};

?>
	<link rel="stylesheet" type="text/css" media="screen,projection" href="<?php bloginfo('template_directory'); ?>/layouts/<?php echo $layouttype; ?>" />

<style type="text/css" media="screen,projection">
/*<![CDATA[*/
/* CSS inserted by blog.txt theme options */
	body{font-size:<?php echo $basefontsize; ?>;}
	body,div.comments h3.comment-header span.comment-count,div.entry-content ul.xoxo li.hentry span.entry-title{font-family:<?php echo $basefontfamily; ?>;}
	div#wrapper{width:<?php echo $layoutwidth; ?>;}
	div.hfeed .entry-title,div.hfeed .page-title,div.comments h3,div.entry-content h2,div.entry-content h3,div.entry-content h4,div.entry-content h5,div.entry-content h6,div#header div#blog-description,div#header div.archive-description{font-family:<?php echo $headingfontfamily; ?>;}
	div#header h1#blog-title,div.sidebar ul li h3{font-family:<?php echo $blogtitlefontfamily; ?>;}
	body input#s,div.entry-content div.page-link,div.entry-content p.attachment-name,div.entry-content q,div.comments ol.commentlist q,div.formcontainer div.form-input input,div.formcontainer div.form-textarea textarea,div.hentry div.entry-meta,div.sidebar{font-family:<?php echo $miscfontfamily; ?>;}
	div.hfeed div.hentry{text-align:<?php echo $posttextalignment; ?>;}
	<?php echo $layoutalignment; ?>

/*]]>*/
</style>
<?php // Checks that everything has loaded properly
}

add_action('admin_menu', 'blogtxt_add_admin');
add_action('wp_head', 'blogtxt_wp_head');
add_action('init', 'blogtxt_widgets_init');

add_filter('archive_meta', 'wptexturize');
add_filter('archive_meta', 'convert_smilies');
add_filter('archive_meta', 'convert_chars');
add_filter('archive_meta', 'wpautop');

add_filter('post_gallery', 'blogtxt_gallery', $attr);

// Readies for translation.
load_theme_textdomain('blogtxt')
?>