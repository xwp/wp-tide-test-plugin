<?php
/**
 * Contains security errors.
 *
 * @package wp-tide-test-plugin
 */

/**
 * Class General_Sniffs.
 */
class General_Sniffs {
	
	public function testErrors ( ) {
        $args = array("post_type"=>"post","post_status"=>'publish','meta_query'=> array());
        $args_two = array(
        'post_type'=> 'page'
        );

        extract( $args );

        $args = apply_filters( 'invalidFilterName', $args );

		$wpQuery = new WP_Query( array(
        	'orderby' => 'rand',
	        'posts_per_page' => -1
        ) );

		$test = file_get_contents( 'test.php' );
		$test = get_term_link( 'test' );
		$test = get_page_by_path( 'test' );
		$test = get_page_by_title( 'test' );
		$test = get_term_by( 'test', 'test' );
		$test = get_category_by_slug( 'test' );
		url_to_postid( 'test' );
		attachment_url_to_postid( 'test' );
		wp_remote_get( 'test' );
		get_posts( 'test' );
		wp_get_post_terms( 'test' );
		term_exists( 'test' );
		count_user_posts( 'test' );
		wp_old_slug_redirect();
		get_adjacent_post( 'test' );
		wp_redirect( 'test' );
		wp_is_mobile();

		$posts = $wpQuery->posts;

        $this->other_errors();

        add_filter( 'show_admin_bar', array( $this, 'remove_admin_bar' ) );

		add_filter( 'cron_schedules', 'example_add_cron_interval' );

		add_action( 'admin_menu', 'register_my_custom_menu_page' );
		add_action( 'wp_head', 'wp_head_errors' );
	}

	public function other_errors() {
		error_log( 'test' );
		var_dump( 'test' );
		var_export( 'test' );
		print_r( 'test' );
		trigger_error( 'test' );
		set_error_handler( 'test' );
		debug_backtrace( 'test' );
		debug_print_backtrace( 'test' );
		wp_debug_backtrace_summary( 'test' );

		$a = 1;

		if(in_array( $a, array( 1, 2,3 ) )){
		    echo "test";
		}

		create_function( 'test', 'tesg' );
		serialize( array( 'test' ) );
		sql_regcase( 'test' );
		ereg( 'test', 'test' );
		eregi( 'test', 'test' );
		ereg_replace( 'test', 'test', 'test' );
		eregi_replace( 'test', 'test','test' );
		split( 'test', 'test' );
		spliti( 'test', 'test' );

		if ( 2 == 3 ) {
		    echo "test";
		}

		if ( $a === 1 ) {
		    echo "test";
		}
	}

	public function remove_admin_bar() {
		return false;
	}

	function example_add_cron_interval( $schedules ) {
		$schedules['five_seconds'] = array(
			'interval' => 5,
			'display'  => esc_html__( 'Every Five Seconds' ),
		);

		return $schedules;
	}

	function register_my_custom_menu_page() {
		// add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
		add_menu_page( 'Custom Menu Page Title', 'Custom Menu Page', 'manage_options', __FILE__ . '/custom.php', '', 'dashicons-welcome-widgets-menus', 90 );
	}

	function vip_other() {
		global $wpdb;
		$wpdb->users;
		$wpdb->usermeta;
		$test = $_COOKIE;
		$test = $_SERVER['HTTP_USER_AGENT'];
		$test = $_SERVER['REMOTE_ADDR'];
		$test = $_SESSION;

		session_abort();
		session_cache_expire();
		session_cache_limiter();
		session_commit();
		session_destroy();
		session_encode();
		session_gc();
		session_get_cookie_params();
		session_id();
		session_module_name();
		session_name();
		session_regenerate_id();
		session_register_shutdown();
		session_reset();
		session_save_path();
		session_start();
		session_status();
		session_unset();
		session_write_close();

		date_default_timezone_set( 'UTC' );
	}
	
	public function wp() {
		$ch = curl_init();
		$curlConfig = array(
			CURLOPT_URL            => "http://www.example.com/yourscript.php",
			CURLOPT_POST           => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POSTFIELDS     => array(
				'field1' => 'some date',
				'field2' => 'some other data',
			)
		);
		curl_setopt_array($ch, $curlConfig);
		$result = curl_exec($ch);
		curl_close($ch);

		query_posts( array() );
		wp_reset_query();

		__( $result, '' );
		esc_attr__( $result, '' );
		esc_html__( $result, '' );
		_e( $result, '' );
		esc_attr_e( $result, '' );
		esc_html_e( $result, '' );
		translate_with_gettext_context( $result, '' );
		_x( $result, '' );
		_ex( $result, '' );
		esc_attr_x( $result, '' );
		esc_html_x( $result, '' );
		_n( $result, '', '' );
		_nx( $result, '', '', '' );
		_n_noop( $result, '' );
		_nx_noop( $result, '', '' );
	}
	
	public function deprecated_functions() {
		the_category_id();
		the_category_head();
		permalink_link();
		start_wp();
		previous_post();
		next_post();
		get_linksbyname();
		get_linkobjectsbyname();
		get_linkobjects();
		get_linksbyname_withrating();
		get_links_withrating();
		get_autotoggle();
		list_cats();
		wp_list_cats();
		dropdown_cats();
		list_authors();
		wp_get_post_cats();
		wp_set_post_cats();
		get_archives();
		link_pages();
		wp_get_links();
		get_links();
		get_links_list();
		links_popup_script();
		get_linkcatname();
		tinymce_include();
		comments_rss();
		permalink_single_rss();
		comments_rss_link();
		get_category_rss_link();
		get_author_rss_link();
		get_the_attachment_link();
		get_attachment_icon_src();
		get_attachment_icon();
		get_attachment_innerHTML();
		documentation_link();
		gzip_compression();
		wp_get_cookie_login();
		dropdown_categories();
		dropdown_link_categories();
		get_the_author_description();
		the_author_description();
		get_the_author_login();
		get_the_author_firstname();
		the_author_firstname();
		get_the_author_lastname();
		the_author_lastname();
		get_the_author_nickname();
		the_author_nickname();
		get_the_author_email();
		the_author_email();
		get_the_author_icq();
		the_author_icq();
		get_the_author_yim();
		the_author_yim();
		get_the_author_msn();
		the_author_msn();
		get_the_author_aim();
		the_author_aim();
		get_author_name();
		get_the_author_url();
		the_author_url();
		get_the_author_ID();
		the_author_ID();
		__ngettext();
		__ngettext_noop();
		get_alloptions();
		automatic_feed_links();
		wp_dropdown_cats();
		codepress_footer_js();
		use_codepress();
		is_plugin_page();
		update_category_cache();
		get_users_of_blog();
		get_author_user_ids();
		get_nonauthor_user_ids();
		wp_timezone_supported();
		wp_dashboard_quick_press();
		wp_tiny_mce();
		wp_preload_dialogs();
		wp_print_editor_js();
		wp_quicktags();
		favorite_actions();
		get_boundary_post_rel_link();
		start_post_rel_link();
		get_index_rel_link();
		index_rel_link();
		get_parent_post_rel_link();
		parent_post_rel_link();
		is_blog_user();
		media_upload_image();
		media_upload_audio();
		media_upload_video();
		media_upload_file();
		type_url_form_image();
		type_url_form_audio();
		type_url_form_video();
		type_url_form_file();
	}

	public function wp_head_errors() {
		?>
		<link rel="stylesheet" href="style.css">
		<script src="main.js"></script>

		<?php
	}
}