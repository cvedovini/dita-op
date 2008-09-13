<div class="comments">
<?php
	$req = get_settings('require_name_email'); // Checks if fields are required
	if ( 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']) )
		die ( 'Please do not load this page directly. Thanks!' );
	if ( ! empty($post->post_password) ) :
		if ( $_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password ) :
?>
	<div class="nopassword important"><?php _e('Enter the password to view comments to this post.', 'blogtxt') ?></div>
</div>
<?php
			return;
		endif;
	endif;
?>
<?php if ( $comments ) : ?>
<?php global $blogtxt_comment_alt // Gives .alt class for every-other comment/pingback ?>
<?php
$ping_count = $comment_count = 0;
foreach ( $comments as $comment )
	get_comment_type() == "comment" ? ++$comment_count : ++$ping_count;
?>
<?php if ( $comment_count ) : ?>
<?php $blogtxt_comment_alt = 0 // Resets comment count for .alt classes ?>

	<h3 class="comment-header" id="numcomments"><?php printf(__($comment_count > 1 ? 'Comments <span class="comment-count">%d</span>' : 'Comments <span class="comment-count">1</span>'), $comment_count) ?></h3>
	<ol id="comments" class="commentlist">
<?php foreach ($comments as $comment) : ?>
<?php if ( get_comment_type() == "comment" ) : ?>
		<li id="comment-<?php comment_ID() ?>" class="<?php blogtxt_comment_class() ?>">
			<span class="comment-author vcard"><?php blogtxt_commenter_link() ?> <?php _e('wrote:', 'blogtxt') ?></span>
			<?php if ($comment->comment_approved == '0') : ?><span class="unapproved"><?php _e('Your comment is awaiting moderation.', 'blogtxt') ?></span><?php endif; ?>
<?php comment_text() ?>
			<span class="comment-meta"><?php printf(__('Posted <abbr class="comment-published" title="%1$s">%2$s at %3$s</abbr> <a class="comment-permalink" href="%4$s" title="Permalink to this comment">&para;</a>', 'blogtxt'),
				get_the_time('Y-m-d\TH:i:sO'),
				get_comment_date('d M Y'),
				get_comment_time(),
				'#comment-' . get_comment_ID() ); ?> <?php edit_comment_link(__('Edit', 'blogtxt'), "<span class=\"comment-edit\"> &equiv; ", "</span>"); ?></span>
		</li>

<?php endif; ?>
<?php endforeach; ?>

	</ol><!-- #comments .commentlist -->

<?php endif; ?>

<?php if ( $ping_count ) : ?>
<?php $blogtxt_comment_alt = 0 // Resets comment count for .alt classes for pingbacks ?>

	<h3 class="comment-header" id="numpingbacks"><?php printf(__($ping_count > 1 ? 'Trackbacks &amp; Pingbacks <span class="comment-count">%d</span>' : 'Trackbacks &amp; Pingbacks <span class="comment-count">1</span>', 'blogtxt'), $ping_count) ?></h3>
	<ol id="pingbacks" class="commentlist">

<?php foreach ( $comments as $comment ) : ?>
<?php if ( get_comment_type() != "comment" ) : ?>
		<li id="comment-<?php comment_ID() ?>" class="<?php blogtxt_comment_class() ?>">
			<span class="pingback-meta vcard"><?php printf(__('From <span class="fn n url org">%1$s</span> on <abbr class="comment-published" title="%2$s">%3$s at %4$s</abbr> <a class="pingback-permalink" href="%5$s" title="Permalink to this pingback">&para;</a>', 'blogtxt'),
				get_comment_author_link(),
				get_the_time('Y-m-d\TH:i:sO'),
				get_comment_date('d M Y'),
				get_comment_time(),
				'#comment-' . get_comment_ID() ); ?> <?php edit_comment_link(__('Edit', 'blogtxt'), "<span class=\"comment-edit\"> &equiv; ", "</span>"); ?></span>
			<?php if ($comment->comment_approved == '0') : ?><span class="unapproved"><?php _e('Your comment is awaiting moderation.', 'blogtxt') ?></span><?php endif; ?>
<?php comment_text() ?>
		</li>

<?php endif ?>
<?php endforeach; ?>

	</ol><!-- #pingbacks .commentlist -->

<?php endif ?>
<?php endif ?>

<?php if ( 'open' == $post->comment_status ) : ?>

	<h3 id="respond"><?php _e('Post a Comment', 'blogtxt') ?></h3>
<?php if ( get_option('comment_registration') && !$user_ID ) : ?>
	<p id="mustlogin"><?php printf(__('You must be <a href="%s" title="Log in">logged in</a> to post a comment.', 'blogtxt'),
			get_option('siteurl') . '/wp-login.php?redirect_to=' . get_permalink() ) ?></p>

<?php else : ?>

	<div class="formcontainer">	

		<form id="commentform" action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post">
<?php if ( $user_ID ) : ?>

			<p id="loggedin"><?php printf(__('Logged in as <a href="%1$s" title="View your profile" class="fn">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>', 'blogtxt'),
					get_option('siteurl') . '/wp-admin/profile.php',
					wp_specialchars($user_identity, true),
					get_option('siteurl') . '/wp-login.php?action=logout&amp;redirect_to=' . get_permalink() ) ?></p>

<?php else : ?>

			<p id="comment-notes"><?php _e('Your email is <em>never</em> published nor shared.', 'blogtxt') ?> <?php if ($req) _e('Required fields are marked <span class="req-field">*</span>', 'blogtxt') ?></p>

			<div class="form-input"><label for="author"><input id="author" name="author" type="text" value="<?php echo $comment_author ?>" size="30" maxlength="20" tabindex="3" /> <?php _e('Name', 'blogtxt') ?><?php if ($req) _e(' <span class="req-field">*</span>', 'blogtxt') ?></label></div>

			<div class="form-input"><label for="email"><input id="email" name="email" type="text" value="<?php echo $comment_author_email ?>" size="30" maxlength="50" tabindex="4" /> <?php _e('Email', 'blogtxt') ?><?php if ($req) _e(' <span class="req-field">*</span>', 'blogtxt') ?></label></div>

			<div class="form-input"><label for="url"><input id="url" name="url" type="text" value="<?php echo $comment_author_url ?>" size="30" maxlength="50" tabindex="5" /> <?php _e('Website', 'blogtxt') ?></label></div>

<?php endif ?>

			<div class="form-textarea-label"><label for="comment"><?php _e('Message', 'blogtxt') ?></label></div>
			<div class="form-textarea"><textarea id="comment" name="comment" cols="45" rows="8" tabindex="6"></textarea></div>
			<div class="form-submit"><input id="submit" name="submit" type="submit" value="<?php _e('Post', 'blogtxt') ?>" tabindex="7" /><input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" /></div>

<?php do_action('comment_form', $post->ID); ?>
		</form><!-- .commentform-->
	</div><!-- .formcontainer -->

<?php endif ?>
<?php endif ?>
</div><!-- .comments -->
