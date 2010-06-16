<?php require_once('functions.php'); ?>
<style>
<?php 
include('style.css');

if (file_exists(get_stylesheet_directory().'/fbprofile.css')){
	include(get_stylesheet_directory().'/fbprofile.css');
}
?>
</style>
<div id="container">
<div id="content">
<div class="hfeed" style="margin-top: 1px;">
	<?php
	if (!isset($_POST['fb_sig_in_profile_tab'])) { ?>
	<fb:if-is-app-user>
		<div style="margin-bottom:10px;text-align:right;"><fb:add-profile-tab /><fb:bookmark /></div>
	</fb:else></fb:if-is-app-user>
	<?php } ?>

	<?php while ( have_posts() ) : the_post() ?>
    <div id="post-<?php the_ID() ?>" class="<?php fbprofile_post_class() ?>">
    <div style="float:right"><fb:share-button class="url" href="<?php the_permalink() ?>" /></div>
        <h2 class="entry-title"><a href="<?php the_permalink() ?>" title="<?php printf(__('Permalink to %s', 'fbprofile'), wp_specialchars(get_the_title(), 1)) ?>" rel="bookmark"><?php the_title() ?></a></h2>
        <div class="entry-content">
		<?php fbprofile_content('<span class="more-link">'.__('Continue Reading &raquo;', 'fbprofile').'</span>'); ?>
        </div>
        <div class="entry-meta">
            <span class="entry-date"><?php _e('Written on', 'fbprofile') ?> <abbr class="published" title="<?php the_time('Y-m-d\TH:i:sO'); ?>"><?php unset($previousday); printf(__('%1$s', 'fbprofile'), the_date('d F Y', false)) ?></abbr></span>
            <span class="meta-sep"> <?php _e('by', 'fbprofile') ?> </span> <span class="entry-author author vcard"><?php the_author_posts_link(); ?></span>
            <span class="meta-sep"> <?php _e('under', 'fbprofile'); ?> </span>
            <span class="entry-category"><?php the_category(', ') ?></span>
            <span class="meta-sep"> <?php _e('with', 'fbprofile'); ?> </span>
            <span class="entry-comments"><?php comments_popup_link(__('No Comments ', 'fbprofile'), __('1 Comment', 'fbprofile'), __('% Comments ', 'fbprofile')) ?></span>
            <?php the_tags('<br /><span class="entry-tags">'.__('Tagged with ', 'fbprofile'), ", ", "</span>") ?>
        </div>
    </div><!-- .post -->
	<?php endwhile ?>

	<div id="nav-below" class="navigation">
		<div class="nav-previous"><?php next_posts_link(__('&laquo; Earlier posts', 'fbprofile')) ?></div>
		<div class="nav-next"><?php previous_posts_link(__('Later posts &raquo;', 'fbprofile')) ?></div>
	</div>
</div><!-- .hfeed -->
</div><!-- #content -->
</div><!-- #container -->

<?php
# Google Analytics not authorized on profile tabs 
if (!isset($_POST['fb_sig_in_profile_tab'])) {
	$options  = get_option('GoogleAnalyticsPP');
	if (!empty($options)) {
		if ($options['uastring'] != '') {
			echo '<fb:google-analytics uacct="' . $options['uastring'] . '" />';
		}
	}
}
