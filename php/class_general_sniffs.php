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
	}
}