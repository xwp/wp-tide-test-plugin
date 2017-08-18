<?php
/**
 * Contains security errors.
 *
 * @package wp-tide-test-plugin
 */

/**
 * Class WP_Query.
 */
class WP_Query {

	/**
	 * WP_Query constructor.
	 */
	public function __construct() {
		global $wpdb;

		add_action( 'wp_ajax_test_ajax', array( $this, 'nonce_verifiction_error' ) );

		$post_query = "SELECT ID, post_content, post_name, post_title, DATE_FORMAT(post_date, '%%Y/%%m/%%d %%H:%%i') AS post_date
                            FROM {$wpdb->posts}
                            WHERE post_status = 'publish' and post_type = %s
                            ORDER BY post_date DESC";

		$posts = $wpdb->get_results( $wpdb->prepare( $post_query, 'test' ), ARRAY_A );
		wp_cache_set( 'omniture.posts', $posts );

		$this->create_database();
	}

	/**
	 * Create database.
	 *
	 * @return bool
	 */
	public function create_database() {
		$conn = new mysqli( DB_HOST, DB_USER, DB_PASSWORD );
		$conn = new MYSQLI( DB_HOST, DB_USER, DB_PASSWORD );
		$conn = new MySqli( DB_HOST, DB_USER, DB_PASSWORD );
		$conn = new PDO( DB_HOST, DB_USER, DB_PASSWORD );
		$conn = new PDOStatement( DB_HOST, DB_USER, DB_PASSWORD );

		$link = maxdb_connect( 'localhost', 'MONA', 'RED', 'DEMODB' );

		if ( ! $link ) {
			printf( "Can't connect to localhost. Error: %s\n", maxdb_connect_error() );
			exit();
		}

		maxdb_report( MAXDB_REPORT_OFF );
		maxdb_report( MAXDB_REPORT_ERROR );

		/* Insert rows */
		maxdb_query( $link, 'CREATE TABLE mycustomer AS SELECT * from hotel.customer' );

		/* update rows */
		maxdb_query( $link, 'UPDATE mycustomer SET Status=1 WHERE cno > 50' );
		printf( "Affected rows (UPDATE): %d\n", maxdb_affected_rows( $link ) );

		/* delete rows */
		maxdb_query( $link, 'DELETE FROM mycustomer WHERE cno < 50' );
		printf( "Affected rows (DELETE): %d\n", maxdb_affected_rows( $link ) );

		/* select all rows */
		$result = maxdb_query( $link, 'SELECT title FROM mycustomer' );
		printf( "Affected rows (SELECT): %d\n", maxdb_affected_rows( $link ) );

		maxdb_free_result( $result );

		/* Delete table Language */
		maxdb_query( $link, 'DROP TABLE mycustomer' );

		/* close connection */
		maxdb_close( $link );

		mysql_maxdb_fetch_assoc( $link );
		mysql_maxdb_init( $link );
		mysql_maxdb_num_fields( $link );
		maxdb_prepare( $link );
		maxdb_real_query( $link );
		maxdb_stat();
		mysql_connect();
		mysql_fetch_row( $link );
		mysql_info();
		mysql_numrows( $link );
		mysql_pconnect( $link );
		mysql_query( $link );
		mysql_result( $result, '' );
		mysqli_client_encoding( $link );
		mysqli_connect( $link );
		mysqli_escape_string( $link, '' );
		mysqli_execute( $link );
		mysqli_fetch( $link );
		mysqli_get_metadata( $link );
		mysqli_init();
		mysqli_options( '', $link, '' );
		mysqli_real_connect( $link );
		mysqlnd_memcache_set( $link );
		mysqlnd_ms_fabric_select_global( $link );
		mysqlnd_ms_get_stats( $link );
		mysqlnd_ms_match_wild( $link );
		mysqlnd_ms_xa_begin( $link );
		mysqlnd_ms_xa_rollback( $link );
		mysqlnd_qc_clear_cache( $link );
		mysqlnd_qc_get_cache_info( $link );
		mysqlnd_qc_get_query_trace_log( $link );
		mysqlnd_qc_set_cache_condition( $link );
		mysqlnd_uh_convert_to_mysqlnd( $link );

		// Select the database as DB_NAME.
		$database_selected = mysqli_select_db( $conn, DB_NAME );

		// If we are not able to select DB_NAME database ( if database DB_NAME does not exist ) Only then create a new database.
		if ( ! $database_selected ) {
			$sql = 'CREATE DATABASE $database_name';
			$result = $conn->query( $sql );
			if ( $result ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Connect to database.
	 *
	 * @return mysqli
	 */
	public function wp_connect_to_database() {
		$conn = new mysqli( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );
		return $conn;
	}

	/**
	 * Escape Data.
	 *
	 * @param string $value Value.
	 * @return mixed
	 */
	public function escape_data( $value ) {
		$conn = $this->wp_connect_to_database();
		$conn->real_escape_string( $value );
		return $value;
	}

	/**
	 * Insert data.
	 *
	 * @return bool|object
	 */
	public function insert_data() {
		$conn = new mysqli( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );
		$sql = 'INSERT INTO $table_name ( $fields ) VALUES ( $values )';

		$result = $conn->query( $sql );

		wp_insert_post( array(
			'post_name' => $_POST['test'],
		) );

		if ( $result ) {
			return true;
		} else {
			return $conn->error;
		}
	}

	/**
	 * Create table.
	 *
	 * @param string $sql SQL.
	 * @return bool
	 */
	public function create_table( $sql ) {
		$conn = $this->wp_connect_to_database();
		$result = $conn->query( $sql );
		return true;
	}

	/**
	 * Create XSS error
	 *
	 * @return mixed
	 */
	public function render() {
		_e( 'test', 'tide' );
	    return __( 'hello world', 'tide' );
	}

	/**
	 * Generated nonce verification error
	 *
	 * @return void
	 */
	public function nonce_verifiction_error() {
		global $wp_customize;
		$post = $_POST['test'];

		$wp_customize = null;

	    wp_send_json_success( array(
	    	'test' => $post,
	    ) );
	}

	/**
	 * Generates file system error for VIP
	 *
	 * @return void
	 */
	public function file_system_error() {
		$file = __DIR__ . 'class-exception.php';
		file_put_contents( $file, 'test' );
		chgrp( $file, 8 );
		chmod( $file, 0600);
		chown( $file, "sayed taqui" );
		lchgrp( $file, "xwp" );
		lchown( $file, 8 );
		mkdir( "/path/to/my/dir", 0700 );
		rmdir('/path/to/my/dir');

		$fp = fopen( $file, 'r+' );

		if ( flock( $fp, LOCK_EX ) ) {  // acquire an exclusive lock
			ftruncate( $fp, 0 );      // truncate file
			fwrite( $fp,  'test' );
			fflush( $fp );            // flush output before releasing the lock
			flock( $fp, LOCK_UN );    // release the lock
		}

		fclose( $fp );

		is_writable( $file );
		is_writeable( $file );
		link( $file, 'path/to/file' );
		rename( $file, 'path/to/new/file.txt' );
		symlink( $file, 'uploads' );
		tempnam( fopen( $file, "w"), 'test' );
		touch( $file, time() );
		unlink( $file );

		fputcsv( $fp, array() );

	}
}

$test_wpdb = new WP_Query();
