<?php
/*
Plugin Name: WordPress.com Stats
Plugin URI: http://wordpress.org/extend/plugins/stats/
Description: Tracks views, post/page views, referrers, and clicks. Requires a WordPress.com API key.
Author: Andy Skelton
Version: 1.3.5

Requires WordPress 2.1 or later. Not for use with WPMU.

Looking for a way to hide the gif? Put this in your stylesheet:
img#wpstats{display:none}

Recent changes:
1.3.5 - Compatibility with WordPress 2.7
1.3.4 - Compatibility with WordPress 2.7
1.3.3 - wpStats.update_postinfo no longer triggered by revision saves (post_type test)

*/

// You can hardcode a WP.com API key here.
$stats_wpcom_api_key = '';

function stats_get_api_key() {
	if ( !empty( $GLOBALS['stats_wpcom_api_key'] ) )
		return $GLOBALS['stats_wpcom_api_key'];

	return stats_get_option('api_key');
}

function stats_set_api_key($api_key) {
	stats_set_option('api_key', $api_key);
}

function stats_get_options() {
	$options = get_option( 'stats_options' );

	if ( !isset( $options['version'] ) || $options['version'] < STATS_VERSION ) {
		$options = stats_upgrade_options( $options );

		stats_set_options( $options );
	}

	return $options;
}

function stats_get_option( $option ) {
	$options = stats_get_options();

	if ( isset( $options[$option] ) )
		return $options[$option];

	return null;
}

function stats_set_option( $option, $value ) {
	$options = stats_get_options();
	
	$options[$option] = $value;
	
	stats_set_options($options);
}

function stats_set_options($options) {
	update_option( 'stats_options', $options );
}

function stats_upgrade_options( $options ) {
	$defaults = array(
		'host'         => '',
		'path'         => '',
		'blog_id'      => false,
	);

	if ( is_array( $options ) && !empty( $options ) )
		$options = array_merge( $defaults, $options );
	else
		$options = $defaults;

	$options['version'] = STATS_VERSION;

	return $options;
}

function stats_footer() {
	global $wp_the_query, $current_user;

	$options = stats_get_options();

	if ( !empty($current_user->ID) || empty($options['blog_id']) )
		return;

	$a['blog'] = $options['blog_id'];
	$a['v'] = 'ext';
	if ( $wp_the_query->is_single || $wp_the_query->is_page )
		$a['post'] = $wp_the_query->get_queried_object_id();
	else
		$a['post'] = '0';

?>
<script src="http://stats.wordpress.com/e-<?php echo gmdate('YW'); ?>.js" type="text/javascript"></script>
<script type="text/javascript">
st_go({<?php echo stats_array($a); ?>});
var load_cmc = function(){linktracker_init(<?php echo "{$a['blog']},{$a['post']},2"; ?>);};
if ( typeof addLoadEvent != 'undefined' ) addLoadEvent(load_cmc);
else load_cmc();
</script>
<?php
}

function stats_array($kvs) {
	$kvs = apply_filters('stats_array', $kvs);
	$kvs = array_map('addslashes', $kvs);
	foreach ( $kvs as $k => $v )
		$jskvs[] = "$k:'$v'";
	return join(',', $jskvs);
}

function stats_admin_menu() {
	if ( stats_get_option('blog_id') ) {
		$hook = add_submenu_page('index.php', __('Blog Stats'), __('Blog Stats'), 'manage_options', 'stats', 'stats_reports_page');
		add_action("load-$hook", 'stats_reports_load');
	}
	$hook = add_submenu_page('plugins.php', __('WordPress.com Stats Plugin'), __('WordPress.com Stats'), 'manage_options', 'wpstats', 'stats_admin_page');
	add_action("load-$hook", 'stats_admin_load');
	add_action("admin_head-$hook", 'stats_admin_head');
	add_action('admin_notices', 'stats_admin_notices');
}

function stats_reports_load() {
	add_action('admin_head', 'stats_reports_head');
}

function stats_reports_head() {
?>
<style type="text/css">
	body { height: 100%; }
	#statsreport { height: 2500px; width: 100%; }
</style>
<?php
}

function stats_reports_page() {
	if ( isset( $_GET['noheader'] ) )
		return stats_dashboard_widget_content();
	$blog_id = stats_get_option('blog_id');
	$day = isset( $_GET['day'] ) && preg_match( '/^\d{4}-\d{2}-\d{2}$/', $_GET['day'] ) ? "&day=$_GET[day]" : '';
	echo "<iframe id='statsreport' frameborder='0' src='http://dashboard.wordpress.com/wp-admin/index.php?page=estats&blog=$blog_id&noheader=true$day'></iframe>";
}

function stats_admin_load() {
	global $plugin_page;

	if ( ! empty( $_POST['action'] ) && $_POST['_wpnonce'] == wp_create_nonce('stats') ) {
		switch( $_POST['action'] ) {
			case 'reset' :
				stats_set_options(array());
				wp_redirect( "plugins.php?page=$plugin_page" );
				exit;

			case 'enter_key' :
				stats_check_key( $_POST['api_key'] );
				wp_redirect( "plugins.php?page=$plugin_page" );
				exit;

			case 'add_or_replace' :
				$key_check = stats_get_option('key_check');
				stats_set_api_key($key_check[0]);
				if ( isset($_POST['add']) ) {
					stats_get_blog_id($key_check[0]);
				} else {
					extract( parse_url( get_option( 'home' ) ) );
					$path = rtrim( $path, '/' );
					if ( empty( $path ) )
						$path = '/';
					$options = stats_get_options();
					$options['blog_id'] = intval($_POST['blog_id']);
					$options['api_key'] = $key_check[0];
					$options['host'] = $host;
					$options['path'] = $path;
					stats_set_options($options);
					stats_update_bloginfo();
				}
				stats_set_option('key_check', false);
				wp_redirect( "plugins.php?page=$plugin_page" );
				exit;
		}
	}

	$options = stats_get_options();
	if ( empty( $options['blog_id']) && empty( $options['key_check'] ) && stats_get_api_key() )
		stats_check_key( $stats_get_api_key );
}

function stats_admin_notices() {
	if ( stats_get_api_key() )
		return;
	echo "<div class='updated' style='background-color:#f66;'><p>" . sprintf(__('<a href="%s">WordPress.com Stats</a> needs attention: please enter an API key or disable the plugin.'), "plugins.php?page=wpstats") . "</p></div>";
}

function stats_admin_head() {
	?>
	<style type="text/css">
		#statserror {
			border: 1px solid #766;
			background-color: #d22;
			padding: 1em 3em;
		}
	</style>
	<?php
}

function stats_admin_page() {
	global $plugin_page;

	$options = stats_get_options();
	?>
	<div class="wrap">
		<h2><?php _e('WordPress.com Stats'); ?></h2>
		<div class="narrow">
<?php if ( !empty($options['error']) ) : ?>
			<div id='statserror'>
				<h3><?php _e('Error from last API Key attempt:'); ?></h3>
				<p><?php echo $options['error']; ?></p>
			</div>
<?php $options['error'] = false; stats_set_options($options); endif; ?>

<?php if ( empty($options['blog_id']) && !empty($options['key_check']) ) : ?>
			<p><?php printf(__('The API key "%1$s" belongs to the WordPress.com account of "%2$s". If this is not your account, please re-enter your API key.'), $options['key_check'][0], $options['key_check'][1]); ?></p>

<?php	if ( !empty( $GLOBALS['stats_wpcom_api_key'] ) ) : ?>
			<p><?php _e('Your API key is hard-coded in the plugin file. Please edit the plugin and then reload this page.'); ?></p>
<?php	else : ?>
			<p>
			<form method="post">
			<?php wp_nonce_field('stats'); ?>
			<input type="hidden" name="action" value="reset" />
			<input type="submit" value="<?php echo js_escape(__('Re-enter API key')); ?>" />
			</form>
			</p>
<?php	endif; ?>

<?php	if ( !empty($options['key_check'][2]) ) : ?>
			<form method="post">
			<?php wp_nonce_field('stats'); ?>
			<input type="hidden" name="action" value="add_or_replace" />
			<p><?php _e('According to the WordPress.com database, this API key is already associated with at least one self-hosted blog. You can <strong>add</strong> this as a new blog on your WordPress.com account or <strong>replace</strong> an existing blog and inherit its stats history.'); ?> </p>
			<h3><?php _e('Add new blog to my account'); ?></h3>
			<p><?php _e('Do this if this blog is new or has never been associated with your API key. This blog will be added to your WordPress.com account.'); ?></p>
			<p><input type="submit" name="add" value="<?php echo js_escape(__('Add to WordPress.com account')); ?>" /></p>
			<h3><?php _e('Replace an existing blog'); ?></h3>
			<p><?php _e('Do this if you want this blog to take over the stats history of another blog associated with your WordPress.com account. This is appropriate if you have reinstalled WordPress, changed hosts, or restored your blog from an export file.'); ?></p>
			<p>
			<select name="blog_id">
				<option selected="selected" value="0"><?php _e('Select a blog to replace'); ?></option>
<?php		foreach ( $options['key_check'][2] as $blog ) : ?>
				<option value="<?php echo $blog['userblog_id']; ?>"><?php echo $blog['siteurl']; ?></option>
<?php		endforeach; ?>
			</select>
			<input type="submit" name="replace" value="<?php echo js_escape(__('Replace')); ?>" />
			</p>
			</form>
<?php	else : ?>
			<form method="post">
			<?php wp_nonce_field('stats'); ?>
			<input type="hidden" name="action" value="add_or_replace" />
			<h3><?php _e('Add new blog to my account'); ?></h3>
			<p><?php _e('Do this if this blog is new or has never been associated with your API key. This blog will be added to your WordPress.com account.'); ?></p>
			<p><input type="submit" name="add" value="<?php echo js_escape(__('Add to WordPress.com account')); ?>" /></p>
			</form>
<?php	endif; ?>

<?php elseif ( empty( $options['blog_id'] ) ) : ?>
			<p><?php _e('The WordPress.com Stats Plugin is not working because it needs to be linked to a WordPress.com account.'); ?></p>

<?php	if ( empty( $GLOBALS['stats_wpcom_api_key'] ) ) : ?>
			<form action="plugins.php?page=<?php echo $plugin_page; ?>" method="post">
				<?php wp_nonce_field('stats'); ?>
				<p><?php _e('Enter your WordPress.com API key to link this blog to your WordPress.com account. Be sure to use your own API key! Using any other key will lock you out of your stats. (<a href="http://wordpress.com/profile/">Get your key here.</a>)'); ?></p>
				<label for="api_key"><?php _e('API Key:'); ?> <input type="text" name="api_key" id="api_key" value="<?php echo $api_key; ?>" /></label>
				<input type="hidden" name="action" value="enter_key" />
				<p class="submit"><input type="submit" value="<?php _e('Save &raquo;'); ?>" /></p>
			</form>
<?php	else : ?>
			<p><?php _e('An API Key is present in the source code but it did not work.') ?></p>
<?php	endif; ?>

<?php else : ?>
			<p><?php _e('The WordPress.com Stats Plugin is configured and working.'); ?></p>
			<p><?php _e('Visitors who are logged in are not counted. (This means you.)'); ?></p>
			<p><?php printf(__('Visit <a href="%s">your Dashboard</a> to see your blog stats. If you are asked to log in, use your WordPress.com username and password.'), 'index.php?page=stats'); ?></p>
<?php endif; ?>

		</div>
	</div>

	<?php
	stats_set_options( $options );
}

function stats_xmlrpc_methods( $methods ) {
	$my_methods = array(
		'wpStats.get_posts' => 'stats_get_posts',
		'wpStats.get_blog' => 'stats_get_blog'
	);

	return array_merge( $methods, $my_methods );
}

function stats_get_posts( $args ) {
	list( $post_ids ) = $args;
	
	$post_ids = array_map( 'intval', (array) $post_ids );
	$r = 'include=' . join(',', $post_ids);
	$posts = get_posts( $r );
	$_posts = array();

	foreach ( $post_ids as $post_id )
		$_posts[$post_id] = stats_get_post($post_id);

	return $_posts;
}

function stats_get_blog( ) {
	$home = parse_url( get_option('home') );
	return array(
		'host' => $home['host'],
		'path' => $home['path'],
		'name' => get_option('blogname'),
		'description' => get_option('blogdescription'),
		'siteurl' => get_option('siteurl'),
		'version' => STATS_VERSION
	);
}

function stats_get_post( $post_id ) {
	$post = get_post( $post_id );
	if ( empty( $post ) )
		$post = get_page( $post_id );
	return array(
		'id' => $post->ID,
		'permalink' => get_permalink($post->ID),
		'title' => $post->post_title,
		'type' => $post->post_type
	);
}

function stats_client() {
	require_once( ABSPATH . WPINC . '/class-IXR.php' );
	$client = new IXR_ClientMulticall( STATS_XMLRPC_SERVER );
	$client->useragent = 'WordPress/' . $client->useragent;
	return $client;
}

function stats_add_call() {
	global $stats_xmlrpc_client;
	if ( empty($stats_xmlrpc_client) ) {
		$stats_xmlrpc_client = stats_client();
		ignore_user_abort(true);
		add_action('shutdown', 'stats_multicall_query');
	}

	$args = func_get_args();

	call_user_method_array( 'addCall', $stats_xmlrpc_client, $args );
}

function stats_multicall_query() {
	global $stats_xmlrpc_client;

	$stats_xmlrpc_client->query();
}

function stats_update_bloginfo() {
	stats_add_call(
		'wpStats.update_bloginfo',
		stats_get_api_key(),
		stats_get_option('blog_id'),
		stats_get_blog()
	);
}

function stats_update_post( $post_id ) {
	if ( !in_array( get_post_type($post_id), array('post', 'page', 'attachment') ) )
		return;

	stats_add_call(
		'wpStats.update_postinfo',
		stats_get_api_key(),
		stats_get_option('blog_id'),
		stats_get_post($post_id)
	);
}

function stats_flush_posts() {
	stats_add_call(
		'wpStats.flush_posts',
		stats_get_api_key(),
		stats_get_option('blog_id')
	);
}

// WP < 2.5
function stats_activity() {
	if ( did_action( 'rightnow_end' ) )
		return;

	$options = stats_get_options();

	if ( $options['blog_id'] ) {
		?>
		<h3><?php _e('WordPress.com Blog Stats'); ?></h3>
		<p><?php printf(__('Visit %s to see your blog stats.'), '<a href="http://dashboard.wordpress.com/wp-admin/index.php?page=stats&blog=' . $options['blog_id'] . '">' . __('your Global Dashboard') . '</a>'); ?></p>
		<?php
	}
}

function stats_check_key($api_key) {
	$options = stats_get_options();

	require_once( ABSPATH . WPINC . '/class-IXR.php' );

	$client = new IXR_Client( STATS_XMLRPC_SERVER );

	$client->query( 'wpStats.check_key', $api_key, stats_get_blog() );

	if ( $client->isError() ) {
		if ( $client->getErrorCode() == -32300 )
			$options['error'] = __('Your blog was unable to connect to WordPress.com. Please ask your host for help. (' . $client->getErrorMessage() . ')');
		else
			$options['error'] = $client->getErrorMessage();
		stats_set_options( $options );
		return false;
	} else {
		$options['error'] = false;
	}

	$options['key_check'] = $client->getResponse();
	stats_set_options($options);

	return true;
}

function stats_get_blog_id($api_key) {
	$options = stats_get_options();

	require_once( ABSPATH . WPINC . '/class-IXR.php' );

	$client = new IXR_Client( STATS_XMLRPC_SERVER );

	extract( parse_url( get_option( 'home' ) ) );

	$path = rtrim( $path, '/' );

	if ( empty( $path ) )
		$path = '/';

	$client->query( 'wpStats.get_blog_id', $api_key, stats_get_blog() );

	if ( $client->isError() ) {
		if ( $client->getErrorCode() == -32300 )
			$options['error'] = __('Your blog was unable to connect to WordPress.com. Please ask your host for help. (' . $client->getErrorMessage() . ')');
		else
			$options['error'] = $client->getErrorMessage();
		stats_set_options( $options );
		return false;
	} else {
		$options['error'] = false;
	}

	$response = $client->getResponse();

	$blog_id = isset($response['blog_id']) ? (int) $response['blog_id'] : false;

	$options[ 'host' ] = $host;
	$options[ 'path' ] = $path;
	$options[ 'blog_id' ] = $blog_id;

	stats_set_options( $options );

	stats_set_api_key( $api_key );

	return $blog_id;
}

function stats_activate() {
	$options = stats_get_options();

	if ( empty($options['blog_id']) && $api_key = stats_get_api_key() )
		stats_get_blog_id($api_key);
}

function stats_deactivate() {
	delete_option('stats_options');
	delete_option('stats_dashboard_widget');
}

/* Dashboard Stuff: WP >= 2.5 */

function stats_register_dashboard_widget() {
	if ( ( !$blog_id = stats_get_option('blog_id') ) || !stats_get_api_key() || !current_user_can( 'manage_options' ) )
		return;

	// wp_dashboard_empty: we load in the content after the page load via JS
	wp_register_sidebar_widget( 'dashboard_stats', __( 'Stats' ), 'wp_dashboard_empty', array(
		'width' => 'full'
	) );
	wp_register_widget_control( 'dashboard_stats', __( 'Stats' ), 'stats_register_dashboard_widget_control', array(), array(
		'widget_id' => 'dashboard_stats',
	) );

	add_action( 'admin_head', 'stats_dashboard_head' );
}

function stats_dashboard_widget_options() {
	$defaults = array( 'chart' => 1, 'top' => -1, 'search' => 7, 'active' => 7 );
	if ( ( !$options = get_option( 'stats_dashboard_widget' ) ) || !is_array($options) )
		$options = array();
	return array_merge( $defaults, $options );
}

function stats_register_dashboard_widget_control() {
	$periods   = array( '1' => __('day'), '7' => __('week'), '31' => __('month') );
	$intervals = array( '-1' => __('all time'), '1' => __('the past day'), '7' => __('the past week'), '31' => __('the past month'), '90' => __('the past quarter'), '365' => __('the past year') );
	$options = stats_dashboard_widget_options();


	if ( 'post' == strtolower($_SERVER['REQUEST_METHOD']) && isset( $_POST['widget_id'] ) && 'dashboard_stats' == $_POST['widget_id'] ) {
		if ( isset($periods[$_POST['chart']]) )
			$options['chart'] = $_POST['chart'];
		foreach ( array( 'top', 'search', 'active' ) as $key )
			if ( isset($intervals[$_POST[$key]]) )
				$options[$key] = $_POST[$key];
		update_option( 'stats_dashboard_widget', $options );
	}
?>
	<p>
		<label for="chart"><?php _e( 'Chart stats by' ); ?></label>
		<select id="chart" name="chart">
<?php foreach ( $periods as $val => $label ) : ?>
			<option value="<?php echo $val; ?>"<?php selected( $val, $options['chart'] ); ?>><?php echo wp_specialchars( $label ); ?></option>
<?php endforeach; ?>
		</select>.
	</p>

	<p>
		<label for="top"><?php _e( 'Show top posts over' ); ?></label>
		<select id="top" name="top">
<?php foreach ( $intervals as $val => $label ) : ?>
			<option value="<?php echo $val; ?>"<?php selected( $val, $options['top'] ); ?>><?php echo wp_specialchars( $label ); ?></option>
<?php endforeach; ?>
		</select>.
	</p>

	<p>
		<label for="search"><?php _e( 'Show top search terms over' ); ?></label>
		<select id="search" name="search">
<?php foreach ( $intervals as $val => $label ) : ?>
			<option value="<?php echo $val; ?>"<?php selected( $val, $options['search'] ); ?>><?php echo wp_specialchars( $label ); ?></option>
<?php endforeach; ?>
		</select>.
	</p>

	<p>
		<label for="active"><?php _e( 'Show most active posts over' ); ?></label>
		<select id="active" name="active">
<?php foreach ( $intervals as $val => $label ) : ?>
			<option value="<?php echo $val; ?>"<?php selected( $val, $options['active'] ); ?>><?php echo wp_specialchars( $label ); ?></option>
<?php endforeach; ?>
		</select>.
	</p>

<?php
}

function stats_add_dashboard_widget( $widgets ) {
	global $wp_registered_widgets;
	if ( !isset($wp_registered_widgets['dashboard_stats']) || !current_user_can( 'manage_options' ) )
		return $widgets;

	array_splice( $widgets, 2, 0, 'dashboard_stats' );
	return $widgets;
}

// Javascript and CSS for dashboard widget
function stats_dashboard_head() { ?>
<script type="text/javascript">
/* <![CDATA[ */
jQuery( function($) {
	var dashStats = $('#dashboard_stats.postbox div.inside');
	if ( dashStats.find( '.dashboard-widget-control-form' ).size() ) {
		return;
	}

	if ( !dashStats.size() ) {
		dashStats = $('#dashboard_stats div.dashboard-widget-content');
		var h = parseInt( dashStats.parent().height() ) - parseInt( dashStats.prev().height() );
		var args = 'width=' + dashStats.width() + '&height=' + h.toString();
	} else {
		var args = 'width=' + ( dashStats.prev().width() * 2 ).toString();
	}

	dashStats.not( '.dashboard-widget-control' ).load('index.php?page=stats&noheader&' + args );
} );
/* ]]> */
</script>
<style type="text/css">
/* <![CDATA[ */
#dashboard_stats .dashboard-widget-content {
	padding-top: 25px;
}
#stats-info h4 {
	font-size: 1em;
	margin: 0 0 .3em;
}
<?php if ( version_compare( '2.7-z', $GLOBALS['wp_version'], '<=' ) ) : ?>
#dashboard_stats {
	overflow-x: hidden;
}
#dashboard_stats #stats-graph {
	margin: 0;
}
#stats-info {
	border-top: 1px solid #ccc;
}
#stats-info .stats-section {
	width: 50%;
	float: left;
}
#stats-info .stats-section-inner {
	margin: 1em 0;
}
#stats-info div#active {
	border-top: 1px solid #ccc;
}
#stats-info p {
	margin: 0 0 .25em;
	color: #999;
}
#stats-info div#top-search p {
	color: #333;
}
#stats-info p a {
	display: block;
}
<?php else : ?>
#stats-graph {
	width: 50%;
	float: left;
}
#stats-info {
	width: 49%;
	float: left;
}
#stats-info div {
	margin: 0 0 1em 30px;
}
#stats-info div#active {
	margin-bottom: 0;
}
#stats-info p {
	margin: 0;
	color: #999;
}
<?php endif; ?>
/* ]]> */
</style>
<?php
}

function stats_get_csv( $table, $args = null ) {
	$blog_id = stats_get_option('blog_id');
	$key = stats_get_api_key();

	if ( !$blog_id || !$key )
		return array();

	$defaults = array( 'end' => false, 'days' => false, 'limit' => 3, 'post_id' => false, 'summarize' => '' );

	$args = wp_parse_args( $args, $defaults );
	$args['table'] = $table;
	$args['blog_id'] = $blog_id;
	$args['api_key'] = $key;

	$stats_csv_url = add_query_arg( $args, 'http://stats.wordpress.com/csv.php' );

	$key = md5( $stats_csv_url );

	// Get cache
	$stats_cache = get_option( 'stats_cache' );
	if ( !$stats_cache || !is_array($stats_cache) )
		$stats_cache = array();

	// Return or expire this key
	if ( isset($stats_cache[$key]) ) {
		$time = key($stats_cache[$key]);
		if ( time() - $time < 300 )
			return $stats_cache[$key][$time];
		unset( $stats_cache[$key] );
	}

	$stats_rows = array();
	do {
		if ( !$stats = stats_get_remote_csv( $stats_csv_url ) )
			break;

		$labels = array_shift( $stats );

		if ( 0 === stripos( $labels[0], 'error' ) )
			break;

		$stats_rows = array();
		for ( $s = 0; isset($stats[$s]); $s++ ) {
			$row = array();
			foreach ( $labels as $col => $label )
				$row[$label] = $stats[$s][$col];
			$stats_rows[] = $row;
		}
	} while(0);

	// Expire old keys
	foreach ( $stats_cache as $k => $cache )
		if ( !is_array($cache) || 300 < time() - key($cache) )
			unset($stats_cache[$k]);

	// Set cache
	$stats_cache[$key] = array( time() => $stats_rows );
	update_option( 'stats_cache', $stats_cache );

	return $stats_rows;
}

function stats_get_remote_csv( $url ) {
	$url = clean_url( $url, null, 'url' );

	// Yay!
	if ( ini_get('allow_url_fopen') ) {
		$fp = @fopen($url, 'r');
		if ( !$fp )
			return false;

		//stream_set_timeout($fp, $timeout); // Requires php 4.3
		$data = array();
		while ( $remote_read = fgetcsv($fp, 1000) )
			$data[] = $remote_read;
		fclose($fp);
		return $data;
	}

	// Boo - we need to use wp_remote_fopen for maximium compatibility
	if ( !$csv = wp_remote_fopen( $url ) )
		return false;

	return stats_str_getcsv( $csv );
}

// rather than parsing the csv and its special cases, we create a new file and do fgetcsv on it.
function stats_str_getcsv( $csv ) {
	if ( !$temp = tmpfile() ) // tmpfile() automatically unlinks
		return false;

	$data = array();

	fwrite($temp, $csv, strlen($csv));
	fseek($temp, 0);
	while ( false !== $row = fgetcsv($temp, 1000) )
		$data[] = $row;
	fclose($temp);

	return $data;
}

function stats_dashboard_widget_content() {
	$blog_id = stats_get_option('blog_id');
	if ( ( !$width  = (int) ( $_GET['width'] / 2 ) ) || $width  < 250 )
		$width  = 370;
	if ( ( !$height = (int) $_GET['height'] - 36 )   || $height < 230 )
		$height = 230;

	$_width  = $width  - 5;
	$_height = $height - ( $GLOBALS['is_winIE'] ? 16 : 5 ); // hack!

	$options = stats_dashboard_widget_options();

	$src = clean_url( "http://dashboard.wordpress.com/wp-admin/index.php?page=estats&blog=$blog_id&noheader=true&chart&unit=$options[chart]&width=$_width&height=$_height" );

	echo "<iframe id='stats-graph' class='stats-section' frameborder='0' style='width: {$width}px; height: {$height}px; overflow: hidden' src='$src'></iframe>";

	$post_ids = array();

	if ( version_compare( '2.7-z', $GLOBALS['wp_version'], '<=' ) ) {
		$csv_args = array( 'top' => '&limit=8', 'active' => '&limit=5', 'search' => '&limit=5' );
		$printf = __( '%s %s Views' );
	} else {
		$csv_args = array( 'top' => '', 'active' => '', 'search' => '' );
		$printf = __( '%s, %s views' );
	}

	foreach ( $top_posts = stats_get_csv( 'postviews', "days=$options[top]$csv_args[top]" ) as $post )
		$post_ids[] = $post['post_id'];
	foreach ( $active_posts = stats_get_csv( 'postviews', "days=$options[active]$csv_args[active]" ) as $post )
		$post_ids[] = $post['post_id'];

	// cache
	get_posts( array( 'include' => join( ',', array_unique($post_ids) ) ) );

	$searches = array();
	foreach ( $search_terms = stats_get_csv( 'searchterms', "days=$options[search]$csv_args[search]" ) as $search_term )
		$searches[] = $search_term['searchterm'];

?>
<div id="stats-info">
	<div id="top-posts" class='stats-section'>
		<div class="stats-section-inner">
		<h4 class="heading"><?php _e( 'Top Posts' ); ?></h4>
		<?php foreach ( $top_posts as $post ) : if ( !get_post( $post['post_id'] ) ) continue; ?>
		<p><?php printf(
			$printf,
			'<a href="' . get_permalink( $post['post_id'] ) . '">' . get_the_title( $post['post_id'] ) . '</a>',
//			'<a href="' . $post['post_permalink'] . '">' . $post['post_title'] . '</a>',
			number_format_i18n( $post['views'] )
		); ?></p>
		<?php endforeach; ?>
		</div>
	</div>
	<div id="top-search" class='stats-section'>
		<div class="stats-section-inner">
		<h4 class="heading"><?php _e( 'Top Searches' ); ?></h4>
		<p><?php echo join( ',&nbsp; ', $searches );?></p>
		</div>
	</div>
	<div id="active" class='stats-section'>
		<div class="stats-section-inner">
		<h4 class="heading"><?php _e( 'Most Active' ); ?></h4>
		<?php foreach ( $active_posts as $post ) : if ( !get_post( $post['post_id'] ) ) continue; ?>
		<p><?php printf(
			$printf,
			'<a href="' . get_permalink( $post['post_id'] ) . '">' . get_the_title( $post['post_id'] ) . '</a>',
//			'<a href="' . $post['post_permalink'] . '">' . $post['post_title'] . '</a>',
			number_format_i18n( $post['views'] )
		); ?></p>
		<?php endforeach; ?>
		</div>
	</div>
</div>
<br class="clear" />
<p class="textright">
	<a class="button" href="index.php?page=stats"><?php _e( 'View All' ); ?></a>
</p>
<?php
	exit;
}

if ( !function_exists('number_format_i18n') ) {
	function number_format_i18n( $number, $decimals = null ) { return number_format( $number, $decimals ); }
}

add_action( 'wp_dashboard_setup', 'stats_register_dashboard_widget' );
add_filter( 'wp_dashboard_widgets', 'stats_add_dashboard_widget' );


// Boooooooooooring init stuff
register_activation_hook(__FILE__, 'stats_activate');
register_deactivation_hook(__FILE__, 'stats_deactivate');
add_action( 'admin_menu', 'stats_admin_menu' );
add_action( 'activity_box_end', 'stats_activity', 1 ); // WP < 2.5

// Plant the tracking code in the footer
add_action( 'wp_footer', 'stats_footer', 101 );

// Tell HQ about changed settings
add_action( 'update_option_home', 'stats_update_bloginfo' );
add_action( 'update_option_siteurl', 'stats_update_bloginfo' );
add_action( 'update_option_blogname', 'stats_update_bloginfo' );
add_action( 'update_option_blogdescription', 'stats_update_bloginfo' );

// Tell HQ about changed posts
add_action( 'save_post', 'stats_update_post', 10, 1 );

// Tell HQ to drop all post info for this blog
add_action( 'update_option_permalink_structure', 'stats_flush_posts' );

// Teach the XMLRPC server how to dance properly
add_filter( 'xmlrpc_methods', 'stats_xmlrpc_methods' );

define( 'STATS_VERSION', '2' );
define( 'STATS_XMLRPC_SERVER', 'http://wordpress.com/xmlrpc.php' );
