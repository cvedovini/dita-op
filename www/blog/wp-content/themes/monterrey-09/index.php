<?php get_header(); ?>

  <div id="content">
   <div id="entries">

	<?php if (have_posts()) : ?>
		
		<?php while (have_posts()) : the_post(); ?>

			<?php 
				if (in_category(get_option('dd_asides_cat')) && !$single)
					{
			?>
					<div class="aside" id="post-<?php the_ID(); ?>">
							
						<div class="body">
								<?php the_content('Read the rest of this entry &raquo;'); ?>
						</div>
					
						<div class="posted">
								<?php the_time('g:ia \o\n \t\h\e jS \o\f F, Y') ?> - <?php comments_popup_link('0', '1', '%', '', 'Off'); ?> <?php edit_post_link('edit', '- ', ''); ?>
						</div>
					</div>
					
				<?php } else { ?>
						
					<div class="entry" id="post-<?php the_ID(); ?>">
						<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
						<div class="posted"><?php the_time('F j, Y') ?></div>
                        <div class="comments"><?php comments_popup_link("0", "1", "%", "", "Off"); ?></div>						
						<div class="body">
							<?php the_content('Read the rest of this entry &raquo;'); ?>
						</div>
				
						<div class="postmetadata">Categorized in <?php the_category(', ') ?> <?php edit_post_link('Edit', ' | ', ''); ?></div>
					</div>
				
				<?php }
	
			endwhile; ?>
		
		<?php 
			// This young snippet fixes something too difficult to explain
			
			$numposts = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'publish'");
			$perpage = $wpdb->get_var("SELECT option_value FROM $wpdb->options WHERE option_name = 'posts_per_page'");

			if ($numposts > $perpage) {
		?>
				<div class="navigation">
					<div class="alignleft"><?php next_posts_link('&laquo; Previous Entries') ?></div>
					<div class="alignright"><?php previous_posts_link('Next Entries &raquo;') ?></div>
				</div>
		<?php
			}
		?>
		
	<?php else : ?>

		<h4>Not Found</h4>
		<p class="center">Sorry, but you are looking for something that isn't here.</p>
		<?php include (TEMPLATEPATH . "/searchform.php"); ?>

	<?php endif; ?>


	   </div><!-- /entries -->
	</div><!-- /content -->


<?php get_sidebar(); ?>

<?php get_footer(); ?>
