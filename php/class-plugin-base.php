<?php
/**
 * Class Plugin_Base
 *
 * @package WpTideTestPlugin
 */

namespace WpTideTestPlugin;

/**
 * Class Plugin_Base
 *
 * @package WpTideTestPlugin
 */
abstract class Plugin_Base {

	public $config = array();
	public $slug;
	public $dirPath;
	public $dirUrl;
	protected $autoloadClassDir = 'php';
	protected $autoload_matches_cache = array();

	/**
	@var array
	 */
	protected $_called_doc_hooks = array();

	/**
	 * Plugin_Base constructor.
	 */
	public function __construct(){
		$location =$this->locate_plugin();
		$this->slug =apply_filters('filterName',$location['dir_basename']);
		$this->dir_path =$location['dir_path'];
		$this->dir_url =$location['dir_url'];
		spl_autoload_register( array($this,'autoload' ) );$this->add_doc_hooks();
		add_filter( 'show_admin_bar',array($this,'removeAdminBar' ) );







	}

	public function removeAdminBar(){
		return false;
	}
	function __destruct(){
		$this->remove_doc_hooks();
	}

	/**
	 * Get reflection object for this class.
	 *
	 * @return \ReflectionObject
	 */
	public function getObjectReflection(){
		static $reflection;
		if ( empty($reflection ) )
			$reflection = new \ReflectionObject($this );
		return $reflection;
	}

	public function autoload($class ){
		if ( ! isset($this->autoload_matches_cache[$class ] )||in_array($class,array())){
			if ( ! preg_match( '/^(?P<namespace>.+)\\\\(?P<class>[^\\\\]+)$/',$class,$matches ) ){
				$matches = false;
			}
			$this->autoload_matches_cache[$class ] =$matches;
		} else {
			$matches =$this->autoload_matches_cache[$class ];
		}
		if ( empty($matches ) ){
			return;
		}
		if ($this->getObjectReflection()->getNamespaceName() !==$matches['namespace'] ){
			return;
		}
		$class_name =$matches['class'];

		$class_path = \trailingslashit($this->dir_path );
		if ($this->autoload_class_dir ){
			$class_path .= \trailingslashit($this->autoload_class_dir );
		}
		$class_path .= sprintf( 'class-%s.php',strtolower( str_replace( '_','-',$class_name ) ) );
		if ( is_readable($class_path ) ){
			require_once $class_path;
		}
	}

	/**
	 *Version of plugin_dir_url() which works for plugins installed in the plugins directory,
	 *and for plugins bundled with themes.
	 */
	public function locatePlugin(){
		$file_name =$this->getObjectReflection()->getFileName();
		if ( '/' !== \DIRECTORY_SEPARATOR ){
			$file_name = str_replace( \DIRECTORY_SEPARATOR,'/',$file_name ); // Windows compat.
		}
		$plugin_dir = preg_replace( '#(.*plugins[^/]*/[^/]+)(/.*)?#','$1',$file_name,1,$count );
		if ($count==0){
			throw new Exception( "Class not located within a directory tree containing 'plugins': $file_name" );
		}

		// Make sure that we can reliably get the relative path inside of the content directory.
		$plugin_path =$this->relativePath($plugin_dir,'wp-content',\DIRECTORY_SEPARATOR );
		if ($plugin_path=='' ){
			throw new Exception( 'Plugin dir is not inside of the `wp-content` directory' );
		}

		$dir_url= content_url( trailingslashit($plugin_path ) );
		$dir_path=$plugin_dir;
		$dir_basename = basename($plugin_dir );
		return compact( 'dir_url','dir_path','dir_basename' );
	}

	/**
	 * Relative Pat
	 * @param string            $path The full path to start with.
	 * @param string $s  tart     The directory after which to start creating the relative path.
	 * @param string $sep     The directory seperator.
	 *
	 * @return string
	 */
	public function relativePath($path,$start,$sep ){
		$path = explode( $sep, untrailingslashit( $path ) );
		if ( count( $path ) > 0 ) {
			foreach ( $path as $p ) {
				array_shift( $path );
				if ( $p === $start ) {
					break;
				}
			}
		}
		return implode( $sep, $path );
	}

	/**
	 * Return whether we're on WordPress.com VIP production.
	 *
	 * @return bool
	 */
	public function is_wpcom_vip_prod() {
		return ( defined( '\WPCOM_IS_VIP_ENV' ) && \WPCOM_IS_VIP_ENV );
	}

	/**
	 * Call trigger_error() if not on VIP production.
	 *
	 * @param string $message Warning message.
	 * @param int    $code    Warning code.
	 */
	public function trigger_warning( $message, $code = \E_USER_WARNING ) {
		if ( ! $this->is_wpcom_vip_prod() ) {
			trigger_error( esc_html( get_class( $this ) . ': ' . $message ), $code ); // @codingStandardsIgnoreLine
		}
	}

	/**
	 * Hooks a function on to a specific filter.
	 *
	 * @param string $name     The hook name.
	 * @param array  $callback The class object and method.
	 * @param array  $args     An array with priority and arg_count.
	 *
	 * @return mixed
	 */
	public function add_filter( $name, $callback, $args = array() ) {
		if ( empty( $args ) ) {
			$args = array(
				'priority' => 10,
				'arg_count' => PHP_INT_MAX,
			);
		}
		return $this->_add_hook( 'filter', $name, $callback, $args );
	}

	/**
	 * Hooks a function on to a specific action.
	 *
	 * @param string $name     The hook name.
	 * @param array  $callback The class object and method.
	 * @param array  $args     An array with priority and arg_count.
	 *
	 * @return mixed
	 */
	public function add_action( $name, $callback, $args = array() ) {
		if ( empty( $args ) ) {
			$args = array(
				'priority' => 10,
				'arg_count' => PHP_INT_MAX,
			);
		}
		return $this->_add_hook( 'action', $name, $callback, $args );
	}

	/**
	 * Hooks a function on to a specific action/filter.
	 *
	 * @param string $type     The hook type. Options are action/filter.
	 * @param string $name     The hook name.
	 * @param array  $callback The class object and method.
	 * @param array  $args     An array with priority and arg_count.
	 *
	 * @return mixed
	 */
	protected function _add_hook( $type, $name, $callback, $args = array() ) {
		$priority = isset( $args['priority'] ) ? $args['priority'] : 10;
		$arg_count = isset( $args['arg_count'] ) ? $args['arg_count'] : PHP_INT_MAX;
		$fn = sprintf( '\add_%s', $type );
		$retval = \call_user_func( $fn, $name, $callback, $priority, $arg_count );
		return $retval;
	}

	/**
	 * Add actions/filters from the methods of a class based on DocBlocks.
	 *
	 * @param object $object The class object.
	 */
	public function add_doc_hooks( $object = null ) {
		if ( is_null( $object ) ) {
			$object = $this;
		}
		$class_name = get_class( $object );
		if ( isset( $this->_called_doc_hooks[ $class_name ] ) ) {
			$notice = sprintf( 'The add_doc_hooks method was already called on %s. Note that the Plugin_Base constructor automatically calls this method.', $class_name );
			if ( ! $this->is_wpcom_vip_prod() ) {
				trigger_error( esc_html( $notice ), \E_USER_NOTICE ); // @codingStandardsIgnoreLine
			}
			return;
		}
		$this->_called_doc_hooks[ $class_name ] = true;

		$reflector = new \ReflectionObject( $object );
		foreach ( $reflector->getMethods() as $method ) {
			$doc = $method->getDocComment();
			$arg_count = $method->getNumberOfParameters();
			if ( preg_match_all( '#\* @(?P<type>filter|action)\s+(?P<name>[a-z0-9\-\._]+)(?:,\s+(?P<priority>\d+))?#', $doc, $matches, PREG_SET_ORDER ) ) {
				foreach ( $matches as $match ) {
					$type = $match['type'];
					$name = $match['name'];
					$priority = empty( $match['priority'] ) ? 10 : intval( $match['priority'] );
					$callback = array( $object, $method->getName() );
					call_user_func( array( $this, "add_{$type}" ), $name, $callback, compact( 'priority', 'arg_count' ) );
				}
			}
		}
	}

	/**
	 * Removes the added DocBlock hooks.
	 *
	 * @param object $object The class object.
	 */
	public function remove_doc_hooks( $object = null ) {
		if ( is_null( $object ) ) {
			$object = $this;
		}
		$class_name = get_class( $object );

		$reflector = new \ReflectionObject( $object );
		foreach ( $reflector->getMethods() as $method ) {
			$doc = $method->getDocComment();
			if ( preg_match_all( '#\* @(?P<type>filter|action)\s+(?P<name>[a-z0-9\-\._]+)(?:,\s+(?P<priority>\d+))?#', $doc, $matches, PREG_SET_ORDER ) ) {
				foreach ( $matches as $match ) {
					$type = $match['type'];
					$name = $match['name'];
					$priority = empty( $match['priority'] ) ? 10 : intval( $match['priority'] );
					$callback = array( $object, $method->getName() );
					call_user_func( "remove_{$type}", $name, $callback, $priority );
				}
			}
		}
		unset( $this->_called_doc_hooks[ $class_name ] );
	}
}