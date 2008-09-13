<?php header("HTTP/1.1 404 Not Found"); ?>
<?php get_header() ?>

			<div class="hfeed">
				<div id="post-0" class="post hentry p1">
					<h3 class="entry-title"><?php _e('Nothing Found', 'blogtxt') ?></h3>
					<div class="entry-content">
						<p><?php _e('Sorry, but nothing matched your search criteria. Please try again with some different keywords.', 'blogtxt') ?></p>
					</div>
				<form id="error404-searchform" method="get" action="<?php bloginfo('home') ?>">
					<div>
						<input id="error404-s" name="s" type="text" value="<?php the_search_query() ?>" size="40" />
						<input id="error404-searchsubmit" name="searchsubmit" type="submit" value="<?php _e('Search', 'blogtxt') ?>" />
					</div>
				</form>
				</div><!-- #post-0 .post -->
			</div><!-- .hfeed -->
		</div><!-- #content -->
	</div><!-- #container -->

<?php get_sidebar() ?>
<?php get_footer() ?>