<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

	<head profile="http://gmpg.org/xfn/11">
	
		<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
		
		<title><?php bloginfo('name'); ?> <?php if ( is_single() ) { ?> &raquo; Blog Archive <?php } ?> <?php wp_title(); ?></title>
		
		<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" /> <!-- leave this for stats -->
		
		<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
		<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
		
		<?php wp_head(); ?>
	
	</head>
	
	
<body>
<div id="container">
  <div id="header">
    <h1><a href="<?php echo get_settings('home'); ?>/"><?php bloginfo('name'); ?></a></h1>
    <div class="description"><?php bloginfo('description'); ?></div>
  </div><!-- /header -->

  <div id="navigation">
    <ul>
<?php if (get_option('dd_menu_home') == "yes") { ?> 
      <li <?php echo is_home() ? 'class="current_page_item"' : '' ?>><a href="<?php bloginfo('url'); ?>">Home</a></li>
<?php } ?>
<?php if (get_option('dd_menu_order') == "alpha") { 
		 wp_list_pages('sort_column=post_title&title_li=' );
	  } else if (get_option('dd_menu_order') == "by_id") {
		 wp_list_pages('sort_column=ID&title_li=' );
	  } else if (get_option('dd_menu_order') == "menu_order") {
		 wp_list_pages('sort_column=menu_order&title_li=' );
	  } else {
		 wp_list_pages('title_li=' );
	  }					
?>	
	</ul>
    <a href="<?php bloginfo('rss2_url'); ?>" class="feedicon"></a>
  </div><!-- /navigation -->


