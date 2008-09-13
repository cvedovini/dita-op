<?php get_header() ?>

			<div class="hfeed">

<?php while ( have_posts() ) : the_post() ?>

				<div id="post-<?php the_ID() ?>" class="<?php blogtxt_post_class() ?>">
					<h2 class="entry-title"><a href="<?php the_permalink() ?>" title="<?php printf(__('Permalink to %s', 'blogtxt'), wp_specialchars(get_the_title(), 1)) ?>" rel="bookmark"><?php the_title() ?></a></h2>
					<div class="entry-content">
<?php the_content('<span class="more-link">'.__('Continue Reading &raquo;', 'blogtxt').'</span>'); ?>

<?php link_pages('<div class="page-link">'.__('Pages: ', 'blogtxt'), "</div>\n", 'number'); ?>

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
						<span class="entry-tags"><?php the_tags(__('Tagged: ', 'blogtxt'), ", ", "") ?></span>
<?php edit_post_link(__('Edit', 'blogtxt'), "\t\t\t\t\t<span class=\"meta-sep\">&equiv;</span>\n\t\t\t\t\t<span class='entry-edit'>", "</span>\n"); ?>
					</div>
				</div><!-- .post -->

<?php endwhile ?>

				<div id="nav-below" class="navigation">
					<div class="nav-previous"><?php next_posts_link(__('&laquo; Earlier posts', 'blogtxt')) ?></div>
					<div class="nav-next"><?php previous_posts_link(__('Later posts &raquo;', 'blogtxt')) ?></div>
				</div>

			</div><!-- .hfeed -->
		</div><!-- #content -->
	</div><!-- #container -->

<?php get_sidebar() ?>
<?php get_footer() ?>