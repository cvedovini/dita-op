<?php get_header(); ?>

  <div id="content">
   <div id="entries">

    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div class="entry" id="post-<?php the_ID(); ?>">
		<h2 class="single"><?php the_title(); ?></h2>
			<div class="body">
				<?php the_content('<p class="serif">Read the rest of this page &raquo;</p>'); ?>
	
				<?php link_pages('<p><strong>Pages:</strong> ', '</p>', 'number'); ?>
	
			</div>
		</div>
	  <?php endwhile; endif; ?>

	<h4><?php edit_post_link('Edit this entry.', '<p>', '</p>'); ?></h4>

	</div><!-- /content -->
   </div><!-- /entries -->
  

<?php get_sidebar(); ?>

<?php get_footer(); ?>