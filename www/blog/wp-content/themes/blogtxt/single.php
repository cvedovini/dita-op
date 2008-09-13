<?php get_header(); ?>

			<div class="hfeed">

<?php the_post(); ?>

				<div id="post-<?php the_ID(); ?>" class="<?php blogtxt_post_class(); ?>">
					<h2 class="entry-title"><?php the_title(); ?></h2>
					<div class="entry-content">
<?php the_content('<span class="more-link">'.__('Continue Reading &raquo;', 'blogtxt').'</span>'); ?>

<?php link_pages('<div class="page-link">'.__('Pages: ', 'blogtxt'), "</div>\n", 'number'); ?>

					</div>

<!-- <?php trackback_rdf(); ?> -->

				</div><!-- .post -->

<?php comments_template(); ?>

				<div id="nav-below" class="navigation">
					<div class="nav-previous"><?php previous_post_link(__('&laquo; %link', 'blogtxt')) ?></div>
					<div class="nav-next"><?php next_post_link(__('%link &raquo;', 'blogtxt')) ?></div>
				</div>

			</div><!-- .hfeed -->
		</div><!-- #content -->
	</div><!-- #container -->

	<div id="primary" class="sidebar">
		<ul>
			<li id="home-link">
				<h3><a href="<?php bloginfo('home') ?>" title="<?php echo wp_specialchars(get_bloginfo('name'), 1) ?>"><?php _e('&laquo; Home', 'blogtxt'); ?></a></h3>
			</li>
			<li class="entry-meta">
				<h3><?php _e('About This Post', 'blogtxt') ?></h3>
				<ul>
					<li><?php printf(__('Written by %s', 'blogtxt'), blogtxt_author_link() ) ?></li>
					<li><?php printf(__('<abbr class="published" title="%1$sT%2$s">%3$s at %4$s</abbr>', 'blogtxt'), get_the_time('Y-m-d'), get_the_time('H:i:sO'), get_the_time('F jS, Y'), get_the_time() ) ?></li>
					<?php edit_post_link(__('Edit this entry', 'blogtxt'),'<li class="entry-edit">&equiv; ','</li>') ?>
				</ul>
			</li>
			<li class="entry-category">
				<h3><?php _e('Categories', 'blogtxt') ?></h3>
				<ul>
					<li><?php the_category('</li><li>') ?></li>
				</ul>
			</li>
			<li class="entry-tags">
				<h3><?php _e('Tags', 'blogtxt') ?></h3>
				<ul>
					<?php the_tags("<li>", "</li><li>", "</li>") ?>
				</ul>
			</li>
		</ul>
	</div><!-- single.php #primary .sidebar -->

	<div id="secondary" class="sidebar">
		<ul>
			<li class="entry-interact">
				<h3><?php _e('Interact', 'blogtxt') ?></h3>
				<ul>
<?php if (('open' == $post-> comment_status) && ('open' == $post->ping_status)) : ?>
					<li class="comment-link"><?php _e('<a href="#respond" title="Post a comment">Post a comment</a>', 'blogtxt') ?></li>
					<li class="trackback-link"><?php printf(__('<a href="%s" rel="trackback" title="Trackback URL for your post">Trackback URI</a>', 'blogtxt'), get_trackback_url() ) ?></li>
<?php elseif (!('open' == $post-> comment_status) && ('open' == $post->ping_status)) : ?>
					<li class="comment-link"><?php _e('Comments closed', 'blogtxt') ?></li>
					<li class="trackback-link"><?php printf(__('<a href="%s" rel="trackback" title="Trackback URL for your post">Trackback URI</a>', 'blogtxt'), get_trackback_url() ) ?></li>
<?php elseif (('open' == $post-> comment_status) && !('open' == $post->ping_status)) : ?>
					<li class="comment-link"><?php _e('<a href="#respond" title="Post a comment">Post a comment</a>', 'blogtxt') ?></li>
					<li class="trackback-link"><?php _e('Trackbacks closed', 'blogtxt') ?></li>
<?php elseif (!('open' == $post-> comment_status) && !('open' == $post->ping_status)) : ?>
					<li class="comment-link"><?php _e('Comments closed', 'blogtxt') ?></li>
					<li class="trackback-link"><?php _e('Trackbacks closed', 'blogtxt') ?></li>
<?php endif; ?>
				</ul>
			</li>
			<li id="rss-links">
				<h3><?php _e('RSS Feeds', 'blogtxt') ?></h3>
				<ul>
					<li><?php comments_rss_link(__('Comments to this post', 'blogtxt')); ?></li>
					<li><a href="<?php bloginfo('rss2_url') ?>" title="<?php echo wp_specialchars(get_bloginfo('name'), 1) ?> RSS 2.0 Feed" rel="alternate" type="application/rss+xml"><?php _e('All posts', 'blogtxt') ?></a></li>
					<li><a href="<?php bloginfo('comments_rss2_url') ?>" title="<?php echo wp_specialchars(bloginfo('name'), 1) ?> Comments RSS 2.0 Feed" rel="alternate" type="application/rss+xml"><?php _e('All comments', 'blogtxt') ?></a></li>
				</ul>
			</li>
			<li id="search">
				<h3><label for="s"><?php _e('Search', 'blogtxt') ?></label></h3>
				<form id="searchform" method="get" action="<?php bloginfo('home') ?>">
					<div>
						<input id="s" name="s" type="text" value="<?php echo wp_specialchars(stripslashes($_GET['s']), true) ?>" size="10" />
						<input id="searchsubmit" name="searchsubmit" type="submit" value="<?php _e('Find', 'blogtxt') ?>" />
					</div>
				</form>
			</li>
		</ul>
	</div><!-- single.php #secondary .sidebar -->

<?php get_footer() ?>