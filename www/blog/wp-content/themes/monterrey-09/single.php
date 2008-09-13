<?php get_header(); ?>

	<div id="content">
     <div id="entries">
				
  <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	
		<div class="entry" id="post-<?php the_ID(); ?>">
			<h2 class="single"><a href="<?php echo get_permalink() ?>" rel="bookmark" title="Permanent Link: <?php the_title(); ?>"><?php the_title(); ?></a></h2>
			<div class="posted"><?php the_time('F j, Y') ?></div>	
			<div class="body">
				<?php the_content('<p class="serif">Read the rest of this entry &raquo;</p>'); ?>
	
				<?php link_pages('<p><strong>Pages:</strong> ', '</p>', 'number'); ?>
	
			</div>
		</div>
		
		<div id="single" class="postmetadata">
			<small>
				This entry was posted
				on <?php the_time('l, F jS, Y') ?> at <?php the_time() ?>
				and is filed under <?php the_category(', ') ?>. You can <?php comments_rss_link('feed'); ?> this entry. 
				
				<?php if (('open' == $post-> comment_status) && ('open' == $post->ping_status)) {
					// Both Comments and Pings are open ?>
					You can <a href="#respond">leave a response</a>, or <a href="<?php trackback_url(true); ?>" rel="trackback">trackback</a> from your own site.
				
				<?php } elseif (!('open' == $post-> comment_status) && ('open' == $post->ping_status)) {
					// Only Pings are Open ?>
					Responses are currently closed, but you can <a href="<?php trackback_url(true); ?> " rel="trackback">trackback</a> from your own site.
				
				<?php } elseif (('open' == $post-> comment_status) && !('open' == $post->ping_status)) {
					// Comments are open, Pings are not ?>
					You can skip to the end and leave a response. Pinging is currently not allowed.
	
				<?php } elseif (!('open' == $post-> comment_status) && !('open' == $post->ping_status)) {
					// Neither Comments, nor Pings are open ?>
					Both comments and pings are currently closed.
				
				<?php } edit_post_link('Edit this entry.','',''); ?>
				
			</small>
		</div>
		
	<?php comments_template(); ?>
	
	<?php endwhile; else: ?>
	
		<p>Sorry, no posts matched your criteria.</p>
	
<?php endif; ?>
	 </div>
	</div>


<?php get_sidebar(); ?>
<?php get_footer(); ?>
