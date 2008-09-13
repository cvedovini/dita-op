	<div id="sidebar">

		<div class="section">
          <h4><?php echo _("Categories") ?></h4>
		
		<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar() ) : ?>
				<ul>
				<?php wp_list_cats('sort_column=name&optioncount=0&hierarchical=0'); ?>
				</ul>
        </div>
<?php if ( is_home() || is_page() ) { ?>
			<?php get_links_list(); ?>	
<?php } ?>		
				
		<div id="section">
          <h4>Meta</h4>
				<ul>
					<?php wp_register(); ?>
					<li><?php wp_loginout(); ?></li>
					<li><a href="http://validator.w3.org/check/referer" title="This page validates as XHTML 1.0 Transitional">Valid <abbr title="eXtensible HyperText Markup Language">XHTML</abbr></a></li>
					<li><a href="http://gmpg.org/xfn/"><abbr title="XHTML Friends Network">XFN</abbr></a></li>
					<li><a href="http://wordpress.org/" title="Powered by WordPress, state-of-the-art semantic personal publishing platform.">WordPress</a></li>
					<?php wp_meta(); ?>
				</ul>
        </div>

		<?php endif; ?>
		
		<div style="clear: both;"></div>
		
		<!-- The search form stays, brother -->
		
		<li style="margin: 0;">
				<?php include (TEMPLATEPATH . '/searchform.php'); ?>
			</li>
			
		<div style="clear: both;"></div>
		
  </div><!-- /sidebar -->


