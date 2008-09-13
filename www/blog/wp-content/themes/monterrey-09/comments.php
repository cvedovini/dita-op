<?php // Do not delete these lines
	if ('comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');

        if (!empty($post->post_password)) { // if there's a password
            if ($_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password) {  // and it doesn't match the cookie
				?>
				
				<p class="nocomments">This post is password protected. Enter the password to view comments.<p>
				
				<?php
				return;
            }
        }

		/* This variable is for alternating comment background */
		$oddcomment = 'even';
?>

<!-- You can start editing here. -->

<?php if ($comments) : ?>
  <div id="comments">
	<h3 id="comments"><?php comments_number('No Responses', 'One Response', '% Responses' );?></h3> 
	
	<?php $oddcomment = ''; ?>

	<?php foreach ($comments as $comment) : ?>

	  <div class="comment <?php echo $oddcomment; ?>" id="comment-<?php comment_ID() ?>">
	    <div class="posted">
          <a href="#" class="author"><?php comment_author_link() ?></a><span class="timestamp"><?php comment_date('F jS, Y') ?> at <?php comment_time() ?></a> <?php edit_comment_link('e','',''); ?></span>
			<?php 
				// Gravatar Stuff
			   if (function_exists('gravatar') && get_option('dd_gravatars') == "on") {
				  if ('' != get_comment_author_url()) {
					 echo "<a href='$comment->comment_author_url' title='Visit $comment->comment_author'>";
				  } else {
					 echo "<a href='http://www.gravatar.com' title='Create your own gravatar at gravatar.com!'>";
				  }
				  echo "<img src='";
				  if ('' == $comment->comment_type) {
					 echo gravatar($comment->comment_author_email);
				  } elseif ( ('trackback' == $comment->comment_type) || ('pingback' == $comment->comment_type) ) {
					 echo gravatar($comment->comment_author_url);
				  }
				  echo "' alt='A gravatar' class='gravatar' /></a>";
			   }
			?>
  		</div>

		<div class="body">
			<?php if ($comment->comment_approved == '0') : ?>
			<p class="await_mod">Your comment is awaiting moderation.</p>
			<?php endif; ?>
			<?php comment_text() ?>
		</div>						
	  </div> <!-- comment -->
		

	<?php /* Changes every other comment to a different class */	
		if ('odd' == $oddcomment) $oddcomment = 'even';
		else $oddcomment = 'odd';
	?>

	<?php endforeach; /* end for each comment */ ?>

	</ol>

 <?php else : // this is displayed if there are no comments so far ?>

  <?php if ('open' == $post->comment_status) : ?> 
		<h3>There are no comments on this post</h3>
		
	 <?php else : // comments are closed ?>
		<!-- If comments are closed. -->
		<h3>Comments are closed.</h3>
		
	<?php endif; ?>
<?php endif; ?>


<?php if ('open' == $post->comment_status) : ?>

<div id="commentformarea">

	<h3 id="respond">Leave a Reply</h3>
	
	<?php if ( get_option('comment_registration') && !$user_ID ) : ?>
	<p>You must be <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php the_permalink(); ?>">logged in</a> to post a comment.</p>
	<?php else : ?>

		<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">
		
		<?php if ( $user_ID ) : ?>
		
		<p>Logged in as <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?action=logout" title="Log out of this account">Logout &raquo;</a></p>
		
		<?php else : ?>
		
		<p><input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="22" tabindex="1" />
		<label for="author"><small>Name <?php if ($req) echo "(required)"; ?></small></label></p>
		
		<p><input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" />
		<label for="email"><small>Mail (not published) <?php if ($req) echo "(required)"; ?></small></label></p>
		
		<p><input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="22" tabindex="3" />
		<label for="url"><small>Website</small></label></p>
		
		<?php endif; ?>
		
		<!--<p><small><strong>XHTML:</strong> You can use these tags: <?php echo allowed_tags(); ?></small></p>-->
		
		<p><textarea name="comment" id="comment" cols="50" rows="10" tabindex="4"></textarea></p>
		
		<p><input name="submit" type="submit" id="submit" tabindex="5" value="Submit Comment" />
		<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
		</p>
		<?php do_action('comment_form', $post->ID); ?>
		
		</form>

</div>

		<div class="navigation">
			<div class="alignleft"><?php previous_post_link('&laquo; %link') ?></div>
			<div class="alignright"><?php next_post_link('%link &raquo;') ?></div>
		</div>

<?php endif; // If registration required and not logged in ?>

<?php endif; // if you delete this the sky will fall on your head ?>
