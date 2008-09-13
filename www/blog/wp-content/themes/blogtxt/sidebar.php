	<div id="primary" class="sidebar">
		<ul>
	<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar(1) ) : // Begin Widgets for Sidebar 1; displays widgets or default contents below ?>
	<?php if ( !is_front_page() || is_paged() ) { // Displays a home link everywhere except the home page ?>
			<li id="home-link">
				<h3><a href="<?php bloginfo('home') ?>" title="<?php echo wp_specialchars(get_bloginfo('name'), 1) ?>"><?php _e('&laquo; Home', 'blogtxt'); ?></a></h3>
			</li>
	<?php } ?>

			<?php wp_list_pages('title_li=<h3>'.__('Contents', 'blogtxt').'</h3>&sort_column=menu_order' ) ?>

	<?php if ( is_home() ) { ?>
	<?php global $wpdb, $comments, $comment;
	// Mini-function for blog.txt recent comments 
	$comments = $wpdb->get_results("SELECT comment_author, comment_author_url, comment_ID, comment_post_ID, SUBSTRING(comment_content,1,65) AS comment_excerpt FROM $wpdb->comments LEFT OUTER JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID = $wpdb->posts.ID) WHERE comment_approved = '1' AND comment_type = '' AND post_password = '' ORDER BY comment_date_gmt DESC LIMIT 5"); ?>
			<li id="blogtxt-recent-comments">
				<h3><?php _e('Recent Comments', 'blogtxt') ?></h3>
				<ul id="recentcomments"><?php
				if ( $comments ) : foreach ($comments as $comment) :
				echo  '<li class="recentcomments">' . sprintf(__('<span class="comment-author vcard">%1$s</span> <span class="comment-entry-title">on <cite title="%2$s">%2$s</cite></span> <blockquote class="comment-summary" cite="%3$s" title="Comment on %2$s">%4$s &hellip;</blockquote>'),
					'<a href="'. get_permalink($comment->comment_post_ID) . '#comment-' . $comment->comment_ID . '" title="' . $comment->comment_author . ' on ' . get_the_title($comment->comment_post_ID) . '"><span class="fn n">' . $comment->comment_author . '</span></a>',
					get_the_title($comment->comment_post_ID),
					get_permalink($comment->comment_post_ID),
					strip_tags($comment->comment_excerpt) ) . '</li>';
				endforeach; endif; ?></ul>
			</li>
	<?php } ?>
	<?php endif; // End Widgets ?>

		</ul>
	</div><!-- #primary .sidebar -->

	<div id="secondary" class="sidebar">
		<ul>
	<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar(2) ) : // Begin Widgets for Sidebar 2; displays widgets or default contents below ?>
	<?php if ( is_home() || is_category() || is_tag() ) { // Displays category archives on the home and category pages?>
			<li id="categories">
				<h3><?php _e('Category Archives', 'blogtxt'); ?></h3>
				<ul>
	<?php wp_list_categories('title_li=&orderby=name&use_desc_for_title=1&hierarchical=1') ?>

				</ul>
			</li>
			<li id="tag-cloud">
				<h3><?php _e('Tag Archives', 'blogtxt'); ?></h3>
				<p><?php wp_tag_cloud() ?></p>
			</li>
	<?php } if ( is_home() || is_page() ) { // Displays RSS and Meta links on the home and 'page' pages ?>
			<li id="rss_links">
				<h3><?php _e('RSS Feeds', 'blogtxt') ?></h3>
				<ul>
					<li><a href="<?php bloginfo('rss2_url') ?>" title="<?php echo wp_specialchars(get_bloginfo('name'), 1) ?> RSS 2.0 Feed" rel="alternate" type="application/rss+xml"><?php _e('All posts', 'blogtxt') ?></a></li>
					<li><a href="<?php bloginfo('comments_rss2_url') ?>" title="<?php echo wp_specialchars(bloginfo('name'), 1) ?> Comments RSS 2.0 Feed" rel="alternate" type="application/rss+xml"><?php _e('All comments', 'blogtxt') ?></a></li>
				</ul>
			</li>
			<li id="meta">
				<h3><?php bloginfo('name') ?></h3>
				<ul>
					<li id="copyright">&copy; <?php echo( date('Y') ); ?> <?php blogtxt_admin_hCard(); ?></li>
					<li id="generator-link"><?php _e('Powered by <a href="http://wordpress.org/" title="WordPress" rel="generator">WordPress</a>', 'blogtxt') ?></li>
					<li id="web-standards"><?php printf(__('Compliant <a href="http://validator.w3.org/check/referer" title="Valid XHTML">XHTML</a> &amp; <a href="http://jigsaw.w3.org/css-validator/validator?profile=css2&amp;warning=2&amp;uri=%s" title="Valid CSS">CSS</a>', 'blogtxt'), get_bloginfo('stylesheet_url') ); ?></li>
					<?php wp_register() ?>

					<li><?php wp_loginout() ?></li>
					<?php wp_meta() // Do not remove; helps plugins work ?>

				</ul>
			</li>
	<?php } elseif ( is_date() ) { // Displays monthly archives on date-based archive pages?>
			<li id="archives">
				<h3><?php _e('Monthly Archives', 'blogtxt') ?></h3>
				<ul>
	<?php wp_get_archives('type=monthly') ?>

				</ul>
			</li>
	<?php } elseif ( is_author() ) { // Displays author archives on author archvies ?>
			<li id="authors">
				<h3><?php _e('Author Archives', 'blogtxt') ?></h3>
				<ul>
	<?php wp_list_authors('optioncount=0&exclude_admin=0&show_fullname=1&hide_empty=1') ?>

				</ul>
			</li>
	<?php } ?>
			<li id="search">
				<h3><label for="s"><?php _e('Search', 'blogtxt') ?></label></h3>
				<form id="searchform" method="get" action="<?php bloginfo('home') ?>">
					<div>
						<input id="s" name="s" type="text" value="<?php the_search_query() ?>" size="10" />
						<input id="searchsubmit" name="searchsubmit" type="submit" value="<?php _e('Find', 'blogtxt') ?>" />
					</div>
				</form>
			</li>
	<?php endif; // End Widgets ?>

		</ul>
	</div><!-- #secondary .sidebar -->
