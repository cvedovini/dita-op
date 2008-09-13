<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes() ?>>
<head profile="http://gmpg.org/xfn/11">
	<title><?php bloginfo('name') ?><?php if ( is_404() ) : ?> - <?php _e('Page not found', 'blogtxt') ?><?php elseif ( is_home() ) : ?> - <?php bloginfo('description') ?><?php elseif ( is_category() ) : ?> - <?php echo single_cat_title(); ?><?php elseif ( is_date() ) : ?> - <?php _e('Blog archives', 'blogtxt') ?><?php elseif ( is_search() ) : ?> - <?php _e('Search results', 'blogtxt') ?><?php else : ?> - <?php the_title() ?><?php endif ?></title>
	<meta http-equiv="content-type" content="<?php bloginfo('html_type') ?>; charset=<?php bloginfo('charset') ?>" />
	<link rel="stylesheet" type="text/css" media="screen,projection" href="<?php bloginfo('stylesheet_url'); ?>" title="blog.txt" />
	<link rel="stylesheet" type="text/css" media="print" href="<?php bloginfo('template_directory'); ?>/print.css" />
	<link rel="alternate" type="application/rss+xml" href="<?php bloginfo('rss2_url') ?>" title="<?php bloginfo('name') ?> <?php _e('RSS feed', 'blogtxt' ) ?>" />
	<link rel="alternate" type="application/rss+xml" href="<?php bloginfo('comments_rss2_url') ?>" title="<?php bloginfo('name') ?> <?php _e( 'comments RSS feed', 'blogtxt' ) ?>" />
	<link rel="pingback" href="<?php bloginfo('pingback_url') ?>" />

<?php wp_head() // Do not remove; helps plugins work ?>

</head>

<body class="<?php blogtxt_body_class() ?>">

<div id="wrapper">
	<div id="container">
		<div id="content">

			<div id="header">
				<h1 id="blog-title"><a href="<?php echo get_settings('home') ?>/" title="<?php bloginfo('name') ?>"><?php bloginfo('name') ?></a></h1>
<?php the_post() ?>
<?php if ( is_day() ) : ?>
				<div class="archive-description"><?php _e('You are currently viewing the daily archives for', 'blogtxt') ?> <?php the_time(__('l, F Y', 'blogtxt')) ?></div>
<?php elseif ( is_month() ) : ?>
				<div class="archive-description"><?php _e('You are currently viewing the monthly archives for', 'blogtxt') ?> <?php the_time(__('F Y', 'blogtxt')) ?></div>
<?php elseif ( is_year() ) : ?>
				<div class="archive-description"><?php _e('You are currently viewing the yearly archives for', 'blogtxt') ?> <?php the_time(__('Y', 'blogtxt')) ?></div>
<?php elseif ( is_author() ) : ?>
				<div class="archive-description"><?php _e('You are currently viewing the author archives of ', 'blogtxt') ?> <?php the_author(); ?></div>
<?php elseif ( is_category() ) : ?>
				<div class="archive-description"><?php if ( !('' == category_description()) ) : echo single_cat_title(); _e(' &mdash; ', 'blogtxt'); echo category_description(); else : echo 'You are currently viewing the category archives of '; echo single_cat_title(); endif; ?></div>
<?php else : ?>
				<div id="blog-description"><?php bloginfo('description') ?></div>
<?php endif; ?>
<?php rewind_posts() ?>
			</div><!-- #header -->
	
			<div class="access"><span class="content-access"><a href="#content" title="<?php _e('Skip to content', 'blogtxt'); ?>"><?php _e('Skip to content', 'blogtxt'); ?></a></span></div>

<?php blogtxt_globalnav() // Adds page links below header, which are hidden by style.css; increases accessibility ?>