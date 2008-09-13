<?php get_header() ?>

			<div class="hfeed">

<?php if (have_posts()) : ?>

				<h2 class="page-title"><?php _e('Search Results', 'blogtxt') ?> <span class="page-subtitle"><?php the_search_query() ?></span></h2>

<?php while (have_posts()) : the_post(); ?>

				<div id="post-<?php the_ID() ?>" class="<?php blogtxt_post_class() ?>">
					<h3 class="entry-title"><a href="<?php the_permalink() ?>" title="<?php printf(__('Permalink to %s', 'blogtxt'), wp_specialchars(get_the_title(), 1)) ?>" rel="bookmark"><?php the_title() ?></a></h3>
					<div class="entry-content">
<?php the_excerpt('<span class="more-link">'.__('Continue Reading &raquo;', 'blogtxt').'</span>') ?>

					</div>
					<div class="entry-meta">
						<span class="meta-sep">&para;</span>
						<span class="entry-date"><?php _e('Posted', 'blogtxt') ?> <abbr class="published" title="<?php the_time('Y-m-d\TH:i:sO'); ?>"><?php unset($previousday); printf(__('%1$s', 'blogtxt'), the_date('d F Y', false)) ?></abbr></span>
						<?php blogtxt_author_link(); // Function for author link option ?>
						<span class="meta-sep">&sect;</span>
						<span class="entry-category"><?php the_category(' &sect; ') ?></span>
						<span class="meta-sep">&Dagger;</span>
						<span class="entry-comments"><?php comments_popup_link(__('Comments (0)', 'blogtxt'), __('Comments (1)', 'blogtxt'), __('Comments (%)', 'blogtxt')) ?></span>
						<span class="meta-sep">&deg;</span>
						<span class="entry-tags"><?php the_tags(__('Tagged: ', 'blogtxt'), ", ", "</span>") ?></span>
<?php edit_post_link(__('Edit', 'blogtxt'), "\t\t\t\t\t<span class=\"meta-sep\">&equiv;</span>\n\t\t\t\t\t<span class='entry-edit'>", "</span>\n"); ?>
					</div>
				</div><!-- .post -->

<?php endwhile; ?>

				<div id="nav-below" class="navigation">
					<div class="nav-previous"><?php next_posts_link(__('&laquo; Earlier posts', 'blogtxt')) ?></div>
					<div class="nav-next"><?php previous_posts_link(__('Later posts &raquo;', 'blogtxt')) ?></div>
				</div>

<?php else : ?>

				<h2 class="page-title"><?php _e('Search Results', 'blogtxt') ?> <span class="page-subtitle"><?php printf(__('%1$s', 'blogtxt'), wp_specialchars(stripslashes($_GET['s']), true) ); ?></span></h2>

				<div id="post-0" class="post hentry p1">
					<h3 class="entry-title"><?php _e('Nothing Found', 'blogtxt') ?></h3>
					<div class="entry-content">
						<p><?php _e('Sorry, but nothing matched your search criteria. Please try again with some different keywords.', 'blogtxt') ?></p>
					</div>
				<form id="noresults-searchform" method="get" action="<?php bloginfo('home') ?>">
					<div>
						<input id="noresults-s" name="s" type="text" value="<?php the_search_query() ?>" size="40" />
						<input id="noresults-searchsubmit" name="searchsubmit" type="submit" value="<?php _e('Search', 'blogtxt') ?>" />
					</div>
				</form>
				</div><!-- #post-0 .post -->

<?php endif; ?>

			</div><!-- .hfeed -->
		</div><!-- #content -->
	</div><!-- #container -->

<?php get_sidebar() ?>
<?php get_footer() ?>